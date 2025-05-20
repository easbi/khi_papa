@extends('layouts.template')
@section('content')

<!-- Page Header -->
<div class="page-header row no-gutters py-4">
	<div class="col-12 col-sm-4 text-center text-sm-left mb-0">
		<span class="text-uppercase page-subtitle">Dashboard</span>
		<h3 class="page-title">Semua Aktivitas</h3>
	</div>
</div>
<!-- End Page Header -->

<!-- Content -->

<!-- Default Light Table -->
<div class="row">
	<div class="col">
		<div class="card card-small mb-4">
			<div class="card-header border-bottom">
				<h6 class="m-0">Tabel Semua Aktivitas Pegawai</h6>
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
                            <th>Pegawai</th>
                            <th>Tanggal</th>
                            <th>Jenis Kegiatan</th>
                            <th>Kegiatan</th>
                            <th>Progres</th>
                            <th>Bukti Dukung</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($activities as $act)
                        <tr>
                            <td>{{ ++$i }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($act->fullname , 17) }}</td>
                            <td>{{ Carbon\Carbon::parse($act->tgl)->format('d-M-Y')  }}</td>
                            <td>{{ $act->jenis_kegiatan }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($act->kegiatan , 65) }}</td>
                            <td>
                                @if($act->is_done == 2)
                                    <span class="badge badge-warning">Selesai?</span>
                                @else
                                    <span class="badge badge-success">Selesai</span>
                                @endif
                            </td>                            
                            <td>
                                @if ($act->berkas == NULL AND $act->link == NULL )
                                    <strong class="text-danger"> Tidak ada! </strong>
                                @elseif ($act->berkas != NULL AND $act->link == NULL )
                                    <strong class="text-success"> Berkas </strong>
                                @elseif ($act->berkas == NULL AND $act->link != NULL )                                   
                                    <strong class="text-success"> Link </strong>
                                @elseif ($act->berkas != NULL AND $act->link != NULL )                                    
                                    <strong class="text-success"> Berkas dan Link </strong>
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('act.destroy',$act->id) }}" method="POST">

                                    <a class="btn btn-info btn-sm" href="{{ route('act.show',$act->id) }}">Show</a>
                                    @if ($act->nip == Auth::user()->nip ||  $act->created_by == Auth::user()->nip)
                                    <a class="btn btn-primary btn-sm" href="{{ route('act.edit',$act->id) }}">Edit</a>
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
       responsive: true
    });
  } );
</script>
@endpush
