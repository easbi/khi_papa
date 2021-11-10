@extends('layouts.template')

@section('content')

<!-- Page Header -->
<div class="page-header row no-gutters py-4">
	<div class="col-12 col-sm-4 text-center text-sm-left mb-0">
		<span class="text-uppercase page-subtitle">Kegiatan-ku Hari Ini (KHI)</span>
		<h3 class="page-title">Detail Kegiatan</h3>
	</div>
</div>
<!-- End Page Header -->

<!-- Content -->

<!-- Default Light Table -->
<div class="row">
	<div class="col">
        <!-- Post Overview -->
        <div class='card card-small mb-3'>
          <div class="card-header border-bottom">
            <h6 class="m-0">Actions</h6>
        </div>
        @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
        @endif

        <div class='card-body p-0'>
            <ul class="list-group list-group-flush">
              <li class="list-group-item p-3">
                <span class="d-flex mb-2">
                  <i class="material-icons mr-1">calendar_today</i>
                  <strong class="mr-1">Tanggal :</strong> {{ $activity->tgl }}
              </span>
              <span class="d-flex mb-2">
                  <i class="material-icons mr-1">visibility</i>
                  <strong class="mr-1">Kondisi Pekerjaan :</strong>
                  <strong class="text-success">{{ $activity->wfo_wfh }}</strong>
              </span>
              <span class="d-flex mb-2">
                  <i class="material-icons mr-1">flag</i>                  
                  <strong class="mr-1">Kegiatan:</strong> {{ $activity->kegiatan }}
              </span>
              <span class="d-flex">
                  <i class="material-icons mr-1">score</i>
                  <strong class="mr-1">Kuantitas dan Satuan:</strong>
                  <strong class="text-warning">{{ $activity->kuantitas }} {{ $activity->satuan }}</strong>
              </span>              
              <span class="d-flex">
                  <i class="material-icons mr-1">score</i>
                  <strong class="mr-1">Berkas/Bukti Kegiatan:</strong>
                  @if ($activity->berkas == NULL )
                    <strong class="text-warning">- </strong>
                  @else 
                    <a class="btn btn-primary btn-sm" href="{{ url('/bukti',$activity->berkas) }}">Berkas</a>
                  @endif
              </span>
              <span class="d-flex">
                  <i class="material-icons mr-1">score</i>
                  <strong class="mr-1">Status Pengerjaan:</strong>
                  @if ($activity->is_done == 1 )
                    Selesai
                  @else
                    Belum Selesai
                  @endif
              </span>
          </li>
          </ul>
      </div>
  </div>
  <!-- / Post Overview -->
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
