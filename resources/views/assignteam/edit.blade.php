@extends('layouts.template')

@section('content')

<div class="page-header row no-gutters py-4">
	<div class="col-12 col-sm-4 text-center text-sm-left mb-0">
		<span class="text-uppercase page-subtitle">KHI</span>
		<h3 class="page-title">Edit Alokasi Anggota</h3>
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
							<form action="{{ route('assigntim.update', $assigntim->id) }}" method="POST" enctype="multipart/form-data">
								@csrf
								@method('PUT')
								<div class="form-group">
                                    <label for="">Tim Kerja</label>                                    
                                    <select class="form-control" id="tim_kerja_id" name="tim_kerja_id" required disabled>
                                        <option value="" selected disabled>Pilih</option>
                                    	@foreach($timkerja as $item)
	                                    	<option value="{{ $item->id }}" {{ $assigntim->tim_kerja_id == $item->id ? 'selected' : '' }}>
	                                    		{{ $item->nama_tim_kerja }}
	                                    	</option>
                                    	@endforeach
                                    </select>
                                </div>       
								<div class="form-group">
                                    <label for="">Proyek</label>                                    
                                    <select class="form-control" id="project_id" name="project_id" required disabled>
                                    	@foreach($projects as $project)
	                                    	<option value="{{ $project->id }}" {{ $assigntim->project_id == $project->id ? 'selected' : '' }} disabled>
	                                    		{{ $project->nama_project }}
	                                    	</option>
                                    	@endforeach
                                    </select>
                                </div>                           
                                <div class="form-group">                                	
                                    <label for="kegiatan_utama_id">Nama Kegiatan Utama</label>                                   
                                    <select class="form-control" id="kegiatan_utama_id" name="kegiatan_utama_id" required disabled>
                                    	@foreach($kegiatanutama as $ku)
	                                    	<option value="{{ $ku->id }}" {{ $assigntim->project_id == $ku->id ? 'selected' : '' }} disabled>
	                                    		{{ $ku->nama_kegiatan_utama }}
	                                    	</option>
                                    	@endforeach
                                    </select>
                                </div>                          
                                <div class="form-group">                                	
                                    <label for="anggota_nip">Ganti Anggota</label>                                   
                                    <select class="form-control" id="anggota_nip" name="anggota_nip" required>
                                    	@foreach($candidate as $cd)
	                                    	<option value="{{ $cd->nip }}" {{ $assigntim->anggota_nip == $cd->nip ? 'selected' : '' }}>
	                                    		{{ $cd->fullname }}
	                                    	</option>
                                    	@endforeach
                                    </select>
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
