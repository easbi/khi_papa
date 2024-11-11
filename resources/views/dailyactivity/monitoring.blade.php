@extends('layouts.template')

@section('content')

<!-- Page Header -->
<div class="page-header row no-gutters py-4">
	<div class="col-12 col-sm-4 text-center text-sm-left mb-0">
		<span class="text-uppercase page-subtitle">Dashboard</span>
		<h3 class="page-title">Monitoring</h3>
	</div>
</div>
<!-- End Page Header -->

<!-- Content -->
<!-- Default Light Table -->
<div class="row">
    <div class="col">
        <div class="card card-small mb-4">
            <div class="card-header border-bottom">
                <h6 class="m-0">Filter Berdasarkan Bulan/Tahun</h6>
            </div>
            <div class="card-body d-flex flex-column">
                <div class="col-sm-12 col-md-12">
                    <form action="{{ route('act.filterMonthYear2')}}" method="GET" enctype="multipart/form-data">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="bulan">Bulan</label>
                                <select class="form-control" name="bulan" id="bulan">
                                    <option value="" selected disabled>Select</option>
                                    @foreach($months as $m)
                                        @if ($m['value'] == $bulan)
                                            <option value="{{$m['value']}}" selected> {{$m['name']}} </option>
                                        @else
                                            <option value="{{$m['value']}}"> {{$m['name']}} </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="tahun">Tahun</label>
                                <select class="form-control" name="tahun" id="tahun">
                                    <option value="" selected disabled>Select</option>
                                    @foreach($years as $y)
                                    @if ($y->year == $tahun)
                                        <option value="{{$y->year}}" selected>{{$y->year}}</option>
                                    @else
                                        <option value="{{$y->year}}">{{$y->year}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-success">Filter</button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Default Pencarian -->

<!-- Default Light Table -->
<div class="row">
	<div class="col">
		<div class="card card-small mb-4">
			<div class="card-header border-bottom d-flex items-align-center h-100">
                <div class="col-sm-6">
                    <h6 class="m-0">
                        Ranking Per 
                        @if(!$bulan || !$tahun || (\Carbon\Carbon::createFromDate($tahun, $bulan, 1)->isToday()))
                            {{ \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->isoFormat('MMMM YYYY') }}
                        @else
                            {{ \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->timezone('Asia/Jakarta')->isoFormat('MMMM YYYY, HH:mm') }} WIB
                        @endif                        
                    </h6>
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
            <div class="card-body d-flex flex-column">
                <table id="example"  class="display responsive nowrap" width="100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Pegawai</th>
                            <th>Indeks Keaktifan KHI</th>
                            <th>Jumlah Kegiatan</th>
                            <th>Jumlah Hari Mengisi</th>
                            <th>Jumlah Hari Tidak Mengisi</th>
                        </tr>
                    </thead>
                    @php $i = 0; @endphp
                    <tbody>
                        @foreach ($rankTodayEmployees as $rankTodayEmployee)
                        <tr>
                            <td>{{ ++$i }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($rankTodayEmployee->fullname , 17) }}</td>
                            <td>{{ number_format($rankTodayEmployee->score, 2) }}</td>   
                            <td>{{ $rankTodayEmployee->jumlah_pengisian }}</td>
                            <td>{{ $rankTodayEmployee->filled_days }}</td>
                            <td>{{ $rankTodayEmployee->missed_days }}</td>   
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

  // Get references to the select element and the button
    const selectElement1 = document.getElementById('bulan');
    const selectElement2 = document.getElementById('tahun');
    const buttonElement = document.getElementById('export');

    // Add event listener to the select element
    selectElement.addEventListener('change', function() {
        // Check the selected option's value
        if ((selectElement1.value === '') || (selectElement2.value === '') ) {
            // Disable the button if option 1 is selected
            // event.preventDefault();
            link.setAttribute('disabled', 'disabled');
        } else {
            // Enable the button for other options
            link.removeAttribute('disabled');
        }
    });

</script>
@endpush
