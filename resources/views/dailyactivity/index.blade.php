@extends('layouts.template')
@section('content')

<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
@if($birthdayToday->isNotEmpty())
    <script>console.log("Modal should be visible");</script>
@endif

@if($birthdayToday->isNotEmpty())
    <script type="text/javascript">
        $(document).ready(function() {
            $('#birthdayModal').modal('show');
        });
    </script>
    <div class="modal fade" id="birthdayModal" tabindex="-1" role="dialog" aria-labelledby="birthdayModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="birthdayModalLabel">🎉 Selamat Ulang Tahun! 🎉</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Hidup adalah serangkaian bab. Hari ini, bab baru dimulai. Selamat ulang tahun untuk rekan kerja kami:</p>
                    <ul>
                        @foreach ($birthdayToday as $name)
                            <li><strong>{{ $name }}</strong></li>
                        @endforeach
                    </ul>
                    <p>Hari ini adalah pengingat bahwa kamu sangat berharga. Terima kasih telah menjadi dirimu yang penuh kebaikan. Semoga ceritamu tahun ini penuh dengan kebahagiaan dan kejutan indah 🎂🥳</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endif

<style>
    .modal-header {
        background-color: #f8d7da;
        color: #721c24;
    }

    .modal-body p, .modal-body ul {
        font-size: 16px;
    }
</style>


<!-- Pengumuman Modal -->
<!-- <div class="modal fade" id="announcementModal" tabindex="-1" role="dialog" aria-labelledby="announcementModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="announcementModalLabel">📢 Pengumuman 2025📢</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Terimakasih kepada <b>Ketua Tim Kerja</b> yang sudah menyelesaikan rancangan Matriks Rencana Kerja 2025 sesuai dengan template yang telah disediakan dan disetting di KHI. </p> 
                <p>Selanjutnya, Mulai dari 2025 semua pegawai sudah bisa menggunakan pencatatan aktivitas (Tambahkan Aktivitas -versi 2025) di <b> 1 Januari 2024 </b>. Konsekuensi dari penggunaan ini adalah para pegawai hanya bisa menyatatkan aktivitas yang sudah dialokasikan oleh ketua timnya, sehingga jika tidak ada alokasi maka akan dianggap kegiatan tambahan saja. Mohon tetap selalu berkoordinasi mengenai alokasi tugas ke ketua tim masing-masing. Terimakasih.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal">Oke</button>
            </div>
        </div>
    </div>
</div> -->

<link rel="stylesheet" href="{{asset('css/runningtext1.css')}}">
<div class="running-text-container">
    <div class="running-text" id="runningText">

    </div>
</div>

@push('scripts')
<!-- Script untuk memunculkan modal pengumuman -->
<script type="text/javascript">
    $(document).ready(function() {
        $('#announcementModal').modal('show');
    });
</script>
@endpush



@push('scripts')
<script>
    // Teks untuk ditampilkan secara bergantian
    const texts = [
        @if($birthdayToday->isNotEmpty())
            "🎉🎉🎉🎂🎂🎂 Selamat Ulang tahun hari ini untuk {{ $birthdayToday->join(', ', ' dan ') }}.🎂🎂🎂🎉🎉🎉" ,
        @else
            "Tingkatkan Core Value Ber-AKHLAK anda dengan mengisi KHI",
        @endif
        "Selamat datang di KHI! Pastikan Anda telah mengisi laporan kegiatan harian Anda."

    ];

    let currentIndex = 0;

    function updateRunningText() {
        const runningTextElement = document.getElementById('runningText');
        if (runningTextElement) {
            runningTextElement.innerText = texts[currentIndex];
            currentIndex = (currentIndex + 1) % texts.length;
            runningTextElement.style.animation = 'none'; // Reset animasi
            void runningTextElement.offsetWidth; // Trigger reflow untuk restart animasi
            runningTextElement.style.animation = 'scrollText 10s linear infinite';
        }
    }

    // Ganti teks setiap 10 detik agar sesuai dengan durasi animasi
    setInterval(updateRunningText, 15000);

    // Tampilkan teks pertama kali
    updateRunningText();
</script>
@endpush


<!-- Page Header -->
<div class="page-header row no-gutters py-4">
	<div class="col-12 col-sm-4 text-center text-sm-left mb-0">
		<span class="text-uppercase page-subtitle">Dashboard</span>
		<h3 class="page-title">Rekap Harian</h3>
	</div>
</div>
<!-- End Page Header -->

<!-- Content -->

<!-- Default Light Table -->

<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script>
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<div class="row">
    <div class="col-sm-6">
        <div class="card card-small mb-4">
            <div class="card-header border-bottom">
                <h6 class="m-0">Progress Entri Pegawai Hari Ini</h6>
            </div>
            <div class="card-body d-flex flex-column">
                <style type="text/css">
                    .chart1 {
                        height:105px
                    }
                </style>
                <div class="chart1">
                    <canvas id="myChart1"></canvas>
                    <script type="text/javascript">
                        var bar_ctx = document.getElementById('myChart1');
                        var bar_chart = new Chart(bar_ctx, {
                          type: 'horizontalBar',
                          data: {
                            labels: [],
                            datasets: [{
                              data: [<?php echo number_format($userfill*100/19,2); ?>],
                              backgroundColor: "#00BC43",
                              datalabels: {
                                color: 'white'               //Color for percentage value
                              }
                            }, {
                              data: [100 - <?php echo number_format($userfill*100/19,2); ?>],
                              backgroundColor: "lightgrey",
                              hoverBackgroundColor: "lightgrey",
                              datalabels: {
                                color: 'lightgrey'          // Make the color of the second bar percentage value same as the color of the bar
                              }
                            }, ]
                          },
                          options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            legend: {
                              display: false
                            },
                            tooltips: {
                              enabled: false
                            },
                            scales: {
                              xAxes: [{
                                display: false,
                                stacked: true
                              }],
                              yAxes: [{
                                display: false,
                                stacked: true
                              }],
                            }, // scales
                            plugins: {                                                                  // PROVIDE PLUGINS where you can specify custom style
                              datalabels: {
                                align: "start",
                                anchor: "end",
                                backgroundColor: null,
                                borderColor: null,
                                borderRadius: 4,
                                borderWidth: 1,
                                font: {
                                  size: 14,
                                  weight: "bold",                                           //Provide Font family for fancier look
                                },
                                offset: 10,
                                formatter: (value, ctx) => {
                                    let sum = 0;
                                    let dataArr = ctx.chart.data.datasets[0].data;
                                    dataArr.map(data => {
                                        sum += data;
                                    });
                                    let percentage = (value*19/100).toFixed(0)+" org";
                                    return percentage;
                                },
                              },
                            },
                          }, // options


                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="card card-small mb-4">
            <div class="card-header border-bottom">
                <h6 class="m-0">Total Kegiatan Hari Ini</h6>
            </div>
            <div class="stats-small stats-small--1 card card-small">
                <div class="card-body">
                    <div class="d-flex flex-column m-auto">
                        <div class="stats-small__data text-center">
                            <span class="stats-small__label text-uppercase">Total Kegiatan</span>
                            <h6 class="stats-small__value count my-3">{{ $act_count_today }}</h6>
                        </div>
                        <div class="stats-small__data">
                            @if ((($act_count_today - $act_count_yesterday))<0)
                            <span class="stats-small__percentage stats-small__percentage--decrease">{{$act_count_today - $act_count_yesterday}}</span>
                            @else
                            <span class="stats-small__percentage stats-small__percentage--increase">{{$act_count_today - $act_count_yesterday}} </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <div class="card card-small mb-4">
            <div class="card-header border-bottom">
                <h6 class="m-0">Top 3 Pegawai Kurang Aktif Bulan ini</h6>
            </div>
            <div class="card-body d-flex flex-column">
                <div class="chart-container">
                    <div class="bar-chart-container">
                        <canvas id="bar-chart-min-5"></canvas>
                    </div>
                </div>
                <script>
                    $(function(){
                        //get the bar chart canvas
                        var cData = <?php echo $leastEmployeesDataJson; ?>;
                        cData = cData.slice(0, 3);  // Ambil 3 pegawai terendah
                        var labels = cData.map(employee => employee.nama); // Nama pegawai
                        var dataValues = cData.map(employee => employee.jumlah_hari_tdk_mengisi); // Data pekerjaan yang selesai
                        var ctx = $("#bar-chart-min-5");

                        //bar chart data
                        var data = {
                            labels: labels, // Nama pegawai
                            datasets: [
                                {
                                    label: "Jumlah Hari Kerja Tidak Mengisi Kegiatan di KHI",
                                    data: dataValues, // Data pekerjaan yang selesai
                                    backgroundColor: [
                                        "#FF6384",
                                        "#36A2EB",
                                        "#FFCE56"
                                    ],
                                    borderColor: [
                                        "#FF6384",
                                        "#36A2EB",
                                        "#FFCE56"
                                    ],
                                    borderWidth: 1
                                }
                            ]
                        };

                        //options
                        var options = {
                            responsive: true,
                            title: {
                                display: true,
                                position: "top",
                                text: "Top Pegawai Kurang Aktif",
                                fontSize: 18,
                                fontColor: "#111"
                            },
                            legend: {
                                display: false,
                                position: "bottom",
                                labels: {
                                    fontColor: "#333",
                                    fontSize: 16
                                }
                            },
                            scales: {
                                xAxes: [{
                                    ticks: {
                                        callback: function(value) {
                                            return value.length > 15 ? value : value;
                                        },
                                        autoSkip: false,
                                        maxRotation: 45,
                                        minRotation: 45
                                    }
                                }],
                                yAxes: [{
                                    ticks: {
                                        beginAtZero: true
                                    }
                                }]
                            }};

                        //create Bar Chart class object
                        var chart1 = new Chart(ctx, {
                            type: "bar",
                            data: data,
                            options: options
                        });
                    });
                </script>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="card card-small mb-4">
            <div class="card-header border-bottom">
                <h6 class="m-0">Top 5 Pegawai Rajin Mengisi KHI Bulan ini (Kondisi per Hari ini) </h6>
            </div>
            <div class="card-body d-flex flex-column">
                <div class="chart-container">
                    <div class="bar-chart-container">
                        <canvas id="bar-chart-top-5"></canvas>
                    </div>
                </div>
                <script>
                    $(function(){
                        //get the bar chart canvas
                        var cData = <?php echo $topEmployeesDataJson; ?>;
                        var labels = cData.map(employee => employee.nama); // Nama pegawai
                        var dataValues = cData.map(employee => employee.jumlah_kegiatan); // Data pekerjaan yang selesai
                        var ctx = $("#bar-chart-top-5");

                        //bar chart data
                        var data = {
                            labels: labels, // Nama pegawai
                            datasets: [
                                {
                                    label: "Jumlah Pekerjaan",
                                    data: dataValues, // Data pekerjaan yang selesai
                                    backgroundColor: [
                                        "#FF6384",
                                        "#36A2EB",
                                        "#FFCE56",
                                        "#4BC0C0",
                                        "#9966FF"
                                    ],
                                    borderColor: [
                                        "#FF6384",
                                        "#36A2EB",
                                        "#FFCE56",
                                        "#4BC0C0",
                                        "#9966FF"
                                    ],
                                    borderWidth: 1
                                }
                            ]
                        };

                        //options
                        var options = {
                            responsive: true,
                            title: {
                                display: true,
                                position: "top",
                                text: "Top 5 Pegawai Terajin",
                                fontSize: 18,
                                fontColor: "#111"
                            },
                            legend: {
                                display: false,
                                position: "bottom",
                                labels: {
                                    fontColor: "#333",
                                    fontSize: 16
                                }
                            },
                            scales: {
                                xAxes: [{
                                    ticks: {
                                        callback: function(value) {
                                            return value.length > 15 ? value : value;
                                        },
                                        autoSkip: false,
                                        maxRotation: 45,
                                        minRotation: 45
                                    }
                                }],
                                yAxes: [{
                                    ticks: {
                                        beginAtZero: true
                                    }
                                }]
                            }};

                        //create Bar Chart class object
                        var chart1 = new Chart(ctx, {
                            type: "bar",
                            data: data,
                            options: options
                        });
                    });
                </script>
            </div>
        </div>
    </div>
</div>

<div class="row">
	<div class="col">
		<div class="card card-small mb-4">
			<div class="card-header border-bottom">
				<h6 class="m-0">Tabel Aktivitas Hari Ini</h6>
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
