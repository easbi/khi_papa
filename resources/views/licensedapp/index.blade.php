@extends('layouts.template')
@section('content')

<!-- Page Header -->
<div class="page-header row no-gutters py-4">
    <div class="col-12 col-sm-4 text-center text-sm-left mb-0">
        <span class="text-uppercase page-subtitle">Lisensi Aplikasi</span>
        <h3 class="page-title">BPS Kota Padang Panjang</h3>
    </div>
</div>
<!-- End Page Header -->

<!-- Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Detail Aplikasi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><strong>Nama Aplikasi:</strong> <span id="modalNamaAplikasi"></span></p>
                <p><strong>Keterangan:</strong> <span id="modalKeterangan"></span></p>
                <p>
                    <strong>Username:</strong> 
                    <span id="modalUsername" style="display: inline-block; margin-right: 10px;"></span>
                    <button type="button" id="copyUsername" class="btn btn-warning btn-sm btn-secondary">Copy</button>
                </p>
                <p>
                    <strong>Password:</strong> 
                    <span id="modalPassword" style="display: inline-block; margin-right: 10px;"></span>
                    <button type="button" id="togglePassword" class="btn btn-sm btn-secondary">Show</button>
                    <button type="button" id="copyPassword" class="btn btn-warning btn-sm btn-secondary">Copy</button>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@if (Auth::user()->id == 1 || Auth::user()->id == 17 || Auth::user()->id == 20)
<div class="row mb-4">
    <div class="col text-right">
        <a href="{{ route('licensedapp.create') }}" class="btn btn-primary btn-sm">
            Tambahkan Lisensi + 
        </a>
    </div>
</div>
@endif

<!-- Default Light Table -->
<div class="row">
    <div class="col">
        <div class="card card-small mb-4">
            <div class="card-header border-bottom">
                <h6 class="m-0">Tabel Daftar Lisensi Aplikasi</h6>
            </div>
            @if ($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <i class="fa fa-check mx-2"></i>
                <strong>Success!</strong> {{ $message }}
            </div>
            @endif
            <div class="card-body d-flex flex-column">
                <table id="example" class="display responsive nowrap" width="100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Aplikasi</th>
                            <th>Tanggal Awal Lisensi</th>
                            <th>Tanggal Akhir Lisensi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($licensedApps as $lap)
                        <tr>
                            <td>{{ ++$i }}</td>
                            <td>{{ $lap->nama_aplikasi }}</td>
                            <td>{{ \Carbon\Carbon::parse($lap->awal_lisensi)->format('d-F-Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($lap->akhir_lisensi)->format('d-F-Y') }}</td>
                            <td>
                                <form action="{{ route('licensedapp.destroy',$lap->id) }}" method="POST">
                                    <a href="#" 
                                    class="btn btn-info btn-sm btn-show" 
                                    data-nama="{{ $lap->nama_aplikasi }}" 
                                    data-keterangan="{{ $lap->keterangan }}" 
                                    data-username="{{ $lap->username }}" 
                                    data-password="{{ $lap->password }}">
                                    Show
                                    </a>
                                    @if (Auth::user()->id == 1 || Auth::user()->id == 17 || Auth::user()->id == 20)
                                    <a class="btn btn-primary btn-sm" href="{{ route('licensedapp.edit',$lap->id) }}">Edit</a>
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Delete</button>
                                    @endif
                                </form>                                 
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- End Default Light Table -->

@endsection

@push('scripts')
<script type="text/javascript">
$(document).ready(function() {
    // DataTable setup
    $('#example').DataTable({
        scrollX: true,
        responsive: true,
        searching: false,
        ordering: true,
        paging: true,
        pageLength: 10,
        autoWidth: false
    });

    // Modal handling
    $(document).on('click', '.btn-show', function(e) {
        e.preventDefault();

        // Ambil data dari atribut tombol
        const nama = $(this).data('nama');
        const keterangan = $(this).data('keterangan');
        const username = $(this).data('username');
        const password = $(this).data('password');

        // Masukkan data ke modal
        $('#modalNamaAplikasi').text(nama);
        $('#modalKeterangan').html(keterangan);  // Gunakan .html() untuk menampilkan format HTML
        $('#modalUsername').text(username);
        $('#modalPassword')
            .text('******') // Sembunyikan password
            .data('password', password) // Simpan password asli
            .attr('data-visible', 'false'); // Tandai password tersembunyi
        $('#togglePassword').text('Show'); // Tombol default

        // Tampilkan modal
        $('#detailModal').modal('show');
    });

    // Toggle password visibility
    $(document).on('click', '#togglePassword', function() {
        const passwordElement = $('#modalPassword');
        const isVisible = passwordElement.attr('data-visible') === 'true'; // Cek status saat ini

        if (isVisible) {
            passwordElement.text('******'); // Sembunyikan password
            passwordElement.attr('data-visible', 'false');
            $(this).text('Show');
        } else {
            passwordElement.text(passwordElement.data('password')); // Tampilkan password asli
            passwordElement.attr('data-visible', 'true');
            $(this).text('Hide');
        }
    });

    // Copy to clipboard function
    function copyToClipboard(text) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(function() {
                alert('Copied to clipboard!');
            }).catch(function(err) {
                alert('Failed to copy text: ' + err);
            });
        } else {
            const tempInput = $('<input>');
            $('body').append(tempInput);
            tempInput.val(text).select();
            document.execCommand('copy');
            tempInput.remove();
            alert('Copied to clipboard!');
        }
    }

    // Copy Username
    $(document).on('click', '#copyUsername', function() {
        const username = $('#modalUsername').text();
        copyToClipboard(username);
    });

    // Copy Password
    $(document).on('click', '#copyPassword', function() {
        const passwordElement = $('#modalPassword');
        const password = passwordElement.attr('data-visible') === 'true'
            ? passwordElement.text() // Jika terlihat, salin teks asli
            : passwordElement.data('password'); // Jika tersembunyi, salin data asli
        copyToClipboard(password);
    });
});
</script>
@endpush
