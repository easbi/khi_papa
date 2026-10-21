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

                                @if ($activity->berkas == null && $activity->link == null)
                                    <strong class="text-danger"> Belum ada Bukti Penyelesaian! </strong>
                                @else
                                    {{-- ===== [GDRIVE] Tombol Berkas: cek storage ===== --}}
                                    @if ($activity->berkas != null)
                                        @if ($activity->berkas_storage === 'gdrive' && $activity->berkas_url)
                                            {{-- File di Google Drive --}}
                                            <a class="btn btn-primary btn-sm" href="{{ $activity->berkas_url }}"
                                                target="_blank" rel="noopener">
                                                <i class="fas fa-cloud"></i> Berkas
                                            </a>
                                        @else
                                            {{-- File di server lokal --}}
                                            <a class="btn btn-secondary btn-sm"
                                                href="{{ url('/bukti', $activity->berkas) }}" target="_blank"
                                                rel="noopener">
                                                <i class="fas fa-file"></i> Berkas
                                            </a>
                                        @endif
                                    @endif

                                    {{-- "dan" hanya muncul kalau ada dua-duanya --}}
                                    @if ($activity->berkas != null && $activity->link != null)
                                        dan
                                    @endif

                                    {{-- ===== Tombol Link (tidak berubah) ===== --}}
                                    @if ($activity->link != null)
                                        <a class="btn btn-primary btn-sm" href="{{ $activity->link }}" target="_blank"
                                            rel="noopener">
                                            <i class="fas fa-link"></i> Link
                                        </a>
                                    @endif
                                @endif
                            </span>
                            <span class="d-flex">
                                <i class="material-icons mr-1">score</i>
                                <strong class="mr-1">Status Pengerjaan:</strong>
                                @if ($activity->is_done == 1)
                                    Selesai
                                @else
                                    Belum Selesai
                                @endif
                            </span>
                            <br>
                            <div class="btn-group" role="group">
                                <a class="btn btn-warning btn-sm"
                                    href="{{ route('act.duplicate', $activity->id) }}">Duplicate</a>
                            </div>
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
        });
    </script>
@endpush
