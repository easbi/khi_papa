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

<!-- Content -->

<!-- Default Light Table -->

<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>

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
                <table id="example"  class="display responsive nowrap" width="100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Aplikasi</th>
                            <th>Keterangan</th>
                            <th>Tanggal Awal Lisensi</th>
                            <th>Tanggal Akhir Lisensi</th>
                            <th>Username</th>
                            <th>Password</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($licensedApps as $lap)
                        <tr>
                            <td>{{ ++$i }}</td>
                            <td>{{ $lap->nama_aplikasi }}</td>
                            <td>{!! $lap->keterangan !!}</td>
                            <td>{{ \Carbon\Carbon::parse($lap->awal_lisensi)->format('d-F-Y') }}</td>  
                            <td>{{ \Carbon\Carbon::parse($lap->akhir_lisensi)->format('d-F-Y') }}</td>  
                            <td>{{ $lap->username }}</td>
                            <td>{{ $lap->password }}</td>
                            <td>
                                <form action="{{ route('licensedapp.destroy',$lap->id) }}" method="POST">     
                                    <a class="btn btn-info btn-sm" href="#">Show</a>
                                    @if (Auth::user()->id == 1 || Auth::user()->id == 17)
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
<!-- End of Content -->
@endsection

@push('scripts')
<script type="text/javascript">
  $(document).ready(function() {
    $('#example').DataTable({
      "scrollX": true,
      "responsive": true,
      "searching": false,  // Menonaktifkan pencarian untuk mempercepat
      "ordering": true,    // Mengaktifkan pengurutan
      "paging": true,      // Mengaktifkan pagination
      "pageLength": 10,    // Membatasi jumlah per halaman
    });
  });
</script>
@endpush
