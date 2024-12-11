@extends('layouts.template')
@section('content')

<!-- Page Header -->
<div class="page-header row no-gutters py-4">
    <div class="col-12 col-sm-4 text-center text-sm-left mb-0">
        <span class="text-uppercase page-subtitle">Alokasi Tim</span>
        <h3 class="page-title">BPS Kota Padang Panjang</h3>
    </div>
</div>
<!-- End Page Header -->

@if (Auth::user()->id == 1 || Auth::user()->id == 17 || Auth::user()->id == 20)
<div class="row mb-4">
    <div class="col text-right">
        <a href="{{ route('assigntim.create') }}" class="btn btn-primary btn-sm">
            Alokasikan Anggota Tim
        </a>
    </div>
</div>
@endif

<!-- Default Light Table -->
<div class="row">
    <div class="col">
        <div class="card card-small mb-4">
            <div class="card-header border-bottom">
                <h6 class="m-0">Tabel Daftar Alokasi Anggota Tim Kerja</h6>
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
                            <th>Ketua Tim Kerja</th>
                            <th>Nama Tim Kerja</th>
                            <th>Project</th>
                            <th>Kegiatan Utama</th>
                            <th>Anggota Tim Kerja</th>
                            <th>Tahun Kerja</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($assigntim as $tk)
                        <tr>
                            <td>{{ ++$i }}</td>
                            <td>{{ $tk->nama_ketua_tim }}</td>
                            <td>{{ $tk->nama_tim_kerja }}</td>
                            <td>{{ $tk->nama_project }}</td>
                            <td>{{ $tk->nama_kegiatan_utama }}</td>
                            <td>{{ $tk->nama_anggota_tim }}</td>
                            <td>{{ $tk->tahun_kerja }}</td>
                            <td>
                                <form action="{{ route('assigntim.destroy',$tk->id) }}" method="POST">
                                    @if (Auth::user()->id == 1 || Auth::user()->id == 17 || Auth::user()->id == 20)
                                    <a class="btn btn-primary btn-sm" href="{{ route('assigntim.edit',$tk->id) }}">Edit</a>
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
});
</script>
@endpush
