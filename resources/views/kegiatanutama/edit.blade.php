@extends('layouts.template')

@section('content')

<div class="page-header row no-gutters py-4">
	<div class="col-12 col-sm-4 text-center text-sm-left mb-0">
		<span class="text-uppercase page-subtitle">KHI</span>
		<h3 class="page-title">Edit Kegiatan Utama</h3>
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
							<form action="{{ route('kegiatanutama.update', $kegiatanutama->id) }}" method="POST" enctype="multipart/form-data">
								@csrf
								@method('PUT')
								<div class="form-group">
                                    <label for="">Tim Kerja</label>                                    
                                    <select class="form-control" id="tim_kerja_id" name="tim_kerja_id" required disabled>
                                        <option value="" selected disabled>Pilih</option>
                                    	@foreach($timkerja as $item)
	                                    	<option value="{{ $item->id }}" {{ $kegiatanutama->tim_kerja_id == $item->id ? 'selected' : '' }}>
	                                    		{{ $item->nama_tim_kerja }}
	                                    	</option>
                                    	@endforeach
                                    </select>
                                </div>       
								<div class="form-group">
                                    <label for="">Proyek</label>                                    
                                    <select class="form-control" id="project_id" name="tim_kerja_id" required disabled>
                                    	@foreach($projects as $project)
	                                    	<option value="{{ $project->id }}" {{ $kegiatanutama->project_id == $project->id ? 'selected' : '' }} disabled>
	                                    		{{ $project->nama_project }}
	                                    	</option>
                                    	@endforeach
                                    </select>
                                </div>                           
                                <div class="form-group">                                	
                                    <label for="nama_kegiatan_utama">Nama Kegiatan Utama</label>
                                    <input type="text" name="nama_kegiatan_utama" class="form-control form-control-lg mb-3" value="{{ $kegiatanutama->nama_kegiatan_utama }}">
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
