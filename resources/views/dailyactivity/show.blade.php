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
                  <strong class="mr-1">Tim Kerja:</strong> {{ $activity->nama_tim_kerja }}
              </span>
              <span class="d-flex mb-2">
                  <i class="material-icons mr-1">flag</i>                  
                  <strong class="mr-1">Proyek:</strong> {{ $activity->nama_project }}
              </span>
              <span class="d-flex mb-2">
                  <i class="material-icons mr-1">flag</i>                  
                  <strong class="mr-1">Kegiatan Utama Tim:</strong> {{ $activity->nama_kegiatan_utama }}
              </span>
              <span class="d-flex mb-2">
                  <i class="material-icons mr-1">flag</i>                  
                  <strong class="mr-1">Kegiatan Individu:</strong> {{ $activity->kegiatan }}
              </span>
              <span class="d-flex">
                  <i class="material-icons mr-1">score</i>
                  <strong class="mr-1">Kuantitas dan Satuan:</strong>
                  <strong class="text-warning">{{ $activity->kuantitas }} {{ $activity->satuan }}</strong>
              </span>   
              <span class="d-flex mb-2">
                  <i class="material-icons mr-1">description</i>
                  <strong class="mr-1">Keterangan Kegiatan:</strong>
                  <div class="keterangan-kegiatan-content">
                      {!! $activity->keterangan !!} <!-- Menampilkan konten HTML dari Quill -->
                  </div>
              </span>           
              <span class="d-flex">
                  <i class="material-icons mr-1">score</i>
                  <strong class="mr-1">Berkas/Bukti Kegiatan:</strong>
                  @if ($activity->berkas == NULL AND $activity->link == NULL )
                    <strong class="text-danger"> Belum ada Bukti Penyelesaian! </strong>
                  @elseif ($activity->berkas != NULL AND $activity->link == NULL )
                    <a class="btn btn-primary btn-sm" href="{{ url('/bukti',$activity->berkas) }}" target="_blank">Berkas</a>
                  @elseif ($activity->berkas == NULL AND $activity->link != NULL )
                    <a class="btn btn-primary btn-sm" href="{{ $activity->link }}" target="_blank">Link</a>
                    @elseif ($activity->berkas != NULL AND $activity->link != NULL )
                    <a class="btn btn-primary btn-sm" href="{{ url('/bukti',$activity->berkas) }}" target="_blank">Berkas</a> 
                     dan        
                    <a class="btn btn-primary btn-sm" href="{{ $activity->link }}" target="_blank">Link</a>
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
