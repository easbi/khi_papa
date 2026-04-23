@extends('layouts.template')
@section('content')
    <div class="page-header row no-gutters py-4">
        <div class="col-12 col-sm-4 text-center text-sm-left mb-0">
            <span class="text-uppercase page-subtitle">Dashboard</span>
            <h3 class="page-title">Semua Aktivitas</h3>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="card card-small mb-4">
                <div class="card-header border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div>
                        <h6 class="m-0">Tabel Semua Aktivitas Pegawai</h6>
                        <small class="text-muted">Tersedia dari 2021 hingga sekarang. Lebih Baik Gunakan Filter untuk performa optimal.</small>
                    </div>
                    <div class="btn-group btn-group-sm mt-2 mt-md-0" role="group">
                        <button id="btnReset" class="btn btn-outline-secondary">Reset Filter</button>
                    </div>
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

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3 mb-2">
                            <label class="small font-weight-bold">Tanggal</label>
                            <input type="date" id="filter_tanggal" class="form-control" />
                        </div>

                        <div class="col-md-3 mb-2">
                            <label class="small font-weight-bold">Bulan</label>
                            <select id="filter_bulan" class="form-control">
                                <option value="">Semua Bulan</option>
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}">{{ DateTime::createFromFormat('!m', $i)->format('F') }}</option>
                                @endfor
                            </select>
                        </div>

                        <div class="col-md-3 mb-2">
                            <label class="small font-weight-bold">Tahun</label>
                            <select id="filter_tahun" class="form-control">
                                <option value="">Semua Tahun</option>
                                @foreach ($years as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 mb-2">
                            <label class="small font-weight-bold">Pencarian</label>
                            <input type="text" id="search_custom" class="form-control" placeholder="Cari pegawai, kegiatan, atau jenis..." />
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <button id="btnFilter" class="btn btn-primary btn-block">Terapkan Filter</button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="example" class="table table-striped table-hover w-100" style="min-width: 900px;">
                            <thead class="thead-light">
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
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function () {
            var table = $('#example').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                searching: false,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                ajax: {
                    url: '{{ route('act.allactivity.data') }}',
                    data: function (d) {
                        d.filter_tanggal = $('#filter_tanggal').val();
                        d.filter_bulan = $('#filter_bulan').val();
                        d.filter_tahun = $('#filter_tahun').val();
                        d.search_custom = $('#search_custom').val();
                    }
                },
                order: [[2, 'desc']],
                columns: [
                    { data: 'no', name: 'no', orderable: false, searchable: false },
                    { data: 'fullname', name: 'fullname' },
                    { data: 'tgl', name: 'tgl' },
                    { data: 'jenis_kegiatan', name: 'jenis_kegiatan' },
                    { data: 'kegiatan', name: 'kegiatan' },
                    { data: 'progres', name: 'progres', orderable: false, searchable: false },
                    { data: 'bukti', name: 'bukti', orderable: false, searchable: false },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false },
                ],
                language: {
                    searchPlaceholder: 'Cari data...',
                    processing: 'Memuat data...',
                    lengthMenu: 'Tampilkan _MENU_ baris',
                    info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ entri',
                    infoEmpty: 'Tidak ada entri tersedia',
                    zeroRecords: 'Data tidak ditemukan',
                    paginate: {
                        previous: 'Sebelumnya',
                        next: 'Berikutnya'
                    }
                }
            });

            $('#btnFilter').on('click', function () {
                table.draw();
            });

            $('#btnReset').on('click', function () {
                $('#filter_tanggal').val('');
                $('#filter_bulan').val('');
                $('#filter_tahun').val('');
                $('#search_custom').val('');
                table.draw();
            });

            $('#search_custom').on('keypress', function (e) {
                if (e.which === 13) {
                    table.draw();
                }
            });
        });
    </script>
@endpush
