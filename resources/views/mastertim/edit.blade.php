@extends('layouts.template')

@section('content')

<div class="page-header row no-gutters py-4">
	<div class="col-12 col-sm-4 text-center text-sm-left mb-0">
		<span class="text-uppercase page-subtitle">KHI</span>
		<h3 class="page-title">Edit Nama Tim</h3>
	</div>
</div>

<!-- Content -->
<div class="row">
	<div class="col-lg-12 col-md-12">
		<div class="card card-small mb-4">
			<div class="card-header border-bottom">
				<h6 class="m-0">Form Edit</h6>
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
							<form action="{{ route('timkerja.update', $timkerja->id) }}" method="POST" enctype="multipart/form-data">
								@csrf
								@method('PUT')
								<div class="form-group">
                                    <label for="nama_tim_kerja">Nama Tim:</label>
                                    <input type="text" class="form-control" name="nama_tim_kerja" value="{{ $timkerja->nama_tim_kerja }}"/>
                                </div>                                
                                <div class="form-group">
                                    <label for="nip_ketua_tim">Ketua Tim Kerja:</label>
                                    <select class="form-control" id="nip_ketua_tim" name="nip_ketua_tim" required>
                                    	<option value="" selected disabled>Pilih</option>
                                    	@foreach($candidate as $item)
	                                    	<option value="{{ $item->nip }}" {{ $timkerja->nip_ketua_tim == $item->nip ? 'selected' : '' }}>
	                                    		{{ $item->fullname }}
	                                    	</option>
                                    	@endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="tahun_kerja">Tahun</label>
                                     <input type="text" class="form-control" name="tahun_kerja" min="2023" max="2027" value="{{$timkerja->tahun_kerja}}" required/>
                                </div>
                                <br>
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
@endsection
