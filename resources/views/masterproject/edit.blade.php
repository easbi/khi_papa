@extends('layouts.template')

@section('content')

<div class="page-header row no-gutters py-4">
	<div class="col-12 col-sm-4 text-center text-sm-left mb-0">
		<span class="text-uppercase page-subtitle">KHI</span>
		<h3 class="page-title">Edit Nama Project</h3>
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
							<form action="{{ route('project.update', $project->id) }}" method="POST" enctype="multipart/form-data">
								@csrf
								@method('PUT')
								<div class="form-group">
                                    <label for="">Tim Kerja</label>                                    
                                    <select class="form-control" id="tim_kerja_id" name="tim_kerja_id" required>
                                        <option value="" selected disabled>Pilih</option>
                                    	@foreach($timkerja as $item)
	                                    	<option value="{{ $item->id }}" {{ $project->tim_kerja_id == $item->id ? 'selected' : '' }}>
	                                    		{{ $item->nama_tim_kerja }}
	                                    	</option>
                                    	@endforeach
                                    </select>
                                </div>                                
                                <div class="form-group">                                	
                                    <label for="nama_project">Nama Project</label>
                                    <input type="text" name="nama_project" class="form-control form-control-lg mb-3" value="{{ $project->nama_project }}">
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
