@extends('layouts.template')
@section('content')

<style type="text/css">
    table.dataTable td {
        white-space: normal !important; /* Bungkus teks secara otomatis */
        word-wrap: break-word;         /* Pecahkan kata yang panjang */
    }
</style>

<!-- Page Header -->
<div class="page-header row no-gutters py-4">
    <div class="col-12 col-sm-4 text-center text-sm-left mb-0">
        <span class="text-uppercase page-subtitle">Kelola Notifikasi</span>
        <h3 class="page-title">BPS Kota Padang Panjang</h3>
    </div>
</div>
<!-- End Page Header -->

@if (Auth::user()->id == 1)
<div class="row mb-4">
    <div class="col text-right">
        <a href="{{ route('notif.create') }}" class="btn btn-primary btn-sm">
            Tambahkan Notifikasi
        </a>
    </div>
</div>
@endif

<!-- Default Light Table -->
<div class="row">
    <div class="col">
        <div class="card card-small mb-4">
            <div class="card-header border-bottom">
                <h6 class="m-0">Tabel Notifikasi</h6>
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
                            <th>Judul</th>
                            <th>Deskripsi</th>
                            <th>Tipe</th>
                            <th>Waktu Pembuatan</th>
                            @if (Auth::user()->id == 1)
                            <th>Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($notifications as $nt)
                        <tr>
                            <td>{{ ++$i }}</td>
                            <td>{{ $nt->title }}</td>
                            <td>{{ $nt->description }}</td>
                            <td>{{ $nt->type }}</td>
                            <td>{{ $nt->created_at }}</td>
                            @if (Auth::user()->id == 1)
                            <td>
                                <form action="{{ route('notif.destroy',$nt->id) }}" method="POST">
                                    <a class="btn btn-primary btn-sm" href="{{ route('notif.edit',$nt->id) }}">Edit</a>
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Delete</button>
                                </form>                                 
                            </td>
                            @endif
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
            autoWidth: false, 
            columnDefs: [
                { targets: 0, width: "2%" },   
                { targets: 1, width: "10%" }, 
                { targets: 2, width: "50%", className: "text-wrap" }, 
                { targets: 3, width: "18%" },  
                { targets: 4, width: "20%" }  
            ]
        });    
    });
</script>

@endpush
