@extends('layouts.template')

@section('content')

<!-- Page Header -->
<div class="page-header row no-gutters py-4">
	<div class="col-12 col-sm-4 text-center text-sm-left mb-0">
		<span class="text-uppercase page-subtitle">Dashboard</span>
		<h3 class="page-title">Rekap Harian Ku</h3>
	</div>
</div>
<!-- End Page Header -->

<!-- Content -->
<!-- Default Light Table -->
<div class="row">
    <div class="col">
        <div class="card card-small mb-4">
            <div class="card-header border-bottom">
                <h6 class="m-0">Filter Berdasarkan Waktu</h6>
            </div>
            <div class="card-body d-flex flex-column">
                <div class="col-sm-12 col-md-12">
                    <form action="{{ route('act.filterMonthYear')}}" method="GET" enctype="multipart/form-data">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="bulan">Bulan</label>
                                <select class="form-control" name="bulan" id="bulan">
                                    <option value="" selected disabled>Select</option>
                                    @foreach($months as $m)
                                        @if ($m['value'] == $bulan)
                                            <option value="{{$m['value']}}" selected> {{$m['name']}} </option>
                                            {{-- <option value="{{$y->year}}">{{$y->year}}</option> --}}
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
                    <h6 class="m-0">List Kegiatan Ku</h6>
                </div>
                @if ($bulan <> "")
                <div class="col-sm-6 d-flex justify-content-end">
                    <div class="form-group m-0">
                        {{-- <h1>Export User Activities</h1> --}}
                        <a href="{{ route('export.activities') }}" class="btn btn-primary" id="export">Export to Excel</a>
                    </div>
                </div>
                @endif
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
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th>Kegiatan</th>
                            <th>Progres</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($activities as $act)
                        <tr>
                            <td>{{ ++$i }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($act->fullname , 17) }}</td>
                            <td>{{ Carbon\Carbon::parse($act->tgl)->format('d-M-Y')  }}</td>
                            <td>{{ Carbon\Carbon::parse($act->tgl_selesai)->format('d-M-Y')  }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($act->kegiatan , 40) }}</td>
                            <td>
                                @if($act->is_done == 2)
                                    <span class="badge badge-warning">Selesai?</span>
                                @else
                                    <span class="badge badge-success">Selesai</span>
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('act.destroy',$act->id) }}" method="POST">

                                    <a class="btn btn-info btn-sm" href="{{ route('act.show',$act->id) }}">Show</a>
                                    <a class="btn btn-primary btn-sm" href="{{ route('act.edit',$act->id) }}">Edit</a>
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Delete</button>
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
