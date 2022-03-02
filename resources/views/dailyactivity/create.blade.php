@extends('layouts.template')

@section('content')

<div class="page-header row no-gutters py-4">
	<div class="col-12 col-sm-4 text-center text-sm-left mb-0">
		<span class="text-uppercase page-subtitle">KHI</span>
		<h3 class="page-title">Entri Kegiatan</h3>
	</div>
</div>

<!-- Content -->
<div class="row">
	<div class="col-lg-12 col-md-12">
		<div class="card card-small mb-4">
			<div class="card-header border-bottom">
				<h6 class="m-0">Form Inputs</h6>
			</div>
			<ul class="list-group list-group-flush">
				<li class="list-group-item p-0 px-3 pt-3">
					@if ($errors->any())
	                    <div class="alert alert-danger">
	                        <strong>Whoops!</strong> There were some problems with your input.<br><br>
	                        <ul>
	                            @foreach ($errors->all() as $error)
	                                <li>{{ $error }}</li>
	                            @endforeach
	                        </ul>
	                    </div>
	                @endif
					<div class="row">
						<div class="col-sm-12 col-md-12">
							<form action="{{ route('act.store') }}" method="POST" enctype="multipart/form-data">
								@csrf
								<div class="form-group">
									<label for="tgl">Tangal</label>
									<input type="date" class="form-control form-control-lg mb-3" name="tgl">
								</div>
								<div class="form-group">
									<label for="wfo_wfh">WFO/WFH:</label>
									<select class="form-control" id="wfo_wfh" name="wfo_wfh">
										<!-- <option value="" selected disabled>Select</option> -->
										<option value="WFO" selected="">WFO - Work From Office</option>
										<!-- <option value="WFH">WFH - Work From Home</option> -->
										<option value="TL">Tugas Luar</option>
										<option value="Lainnya">Lainnya (Cuti, Sakit, Izin)</option>
									</select>
								</div>
								<div class="form-group">
									<label for="kegiatan">Detail Kegiatan:</label>
									<input type="text" class="form-control" name="kegiatan"/>
								</div>
								<div class="form-group">
									<label for="kuantitas">Jumlah:</label>
									<input type="number" class="form-control" name="kuantitas"/>
								</div>
								<div class="form-group">
									<label for="satuan">Satuan:</label>
									<input type="text" class="form-control" name="satuan"/>
								</div>
								<div class="form-group">
									<button type="submit" class="btn btn-success">Kirim</button>
								</div>
							</form>
						</div>
					</div>
				</li>
			</ul>
		</div>
	</div>
</div>
<!-- End of Content -->

@endsection
