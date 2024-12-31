@extends('layouts.template')

@section('content')
<style>
	.hidden {
		display: none;
	}
</style>


<div class="page-header row no-gutters py-4">
	<div class="col-12 col-sm-4 text-center text-sm-left mb-0">
		<span class="text-uppercase page-subtitle">KHI</span>
		<h3 class="page-title">Edit Kegiatan</h3>
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
							<form action="{{ route('act.update', $activity->id) }}" method="POST" enctype="multipart/form-data">
								@csrf
								@method('PUT')
								<div class="form-group">
									<label for="tgl"><b>Tanggal</b></label>
									<input type="date" class="form-control form-control-lg mb-3" name="tgl" value="{{ $activity->tgl }}">
								</div>
								<div class="form-group">
									<label for="wfo_wfh"><b>WFO/WFH:</label>
										<select class="form-control" id="wfo_wfh" name="wfo_wfh">
											<option value="WFO" @if($activity->wfo_wfh == "WFO") selected @endif>WFO - Work From Office</option>
											<option value="WFH" @if($activity->wfo_wfh == "WFH") selected @endif>WFH - Work From Home</option>
											<option value="TL" @if($activity->wfo_wfh == "TL") selected @endif>Tugas Luar</option>
											<option value="Lainnya" @if($activity->wfo_wfh == "Lainnya") selected @endif>Lainnya (Cuti, Sakit, Izin)</option>
										</select>
									</div>
										<div class="form-group">
											<label for="jenis_kegiatan"><b>Jenis Kegiatan:</label>
											<select class="form-control" id="jenis_kegiatan" name="jenis_kegiatan">
												<option value="UTAMA" @if($activity->jenis_kegiatan == "UTAMA") selected @endif>Utama</option>
												<option value="TAMBAHAN" @if($activity->jenis_kegiatan == "TAMBAHAN") selected @endif>Tambahan</option>
											</select>
										</div>
										<div class="form-group" id="tim_kerja_field">
											<label for="">Tim Kerja</label>                                    
											<select class="form-control" id="tim_kerja_id" name="tim_kerja_id" disabled>
										        <option value="{{ $activity->tim_kerja_id }}" selected>
										            {{ $activity->nama_tim_kerja }}
										        </option>
											</select>
										</div>                                
										<div class="form-group" id="project_field">
											<label for="">Project</label>                                    
											<select class="form-control" id="project" name="project_id" disabled>
												<option value="{{$activity->project_id}}">{{$activity->nama_project}}</option>
											</select>                                        
										</div>
										<div class="form-group" id="kegiatan_utama_field">
											<label for="">Kegiatan Utama</label>                                    
											<select class="form-control" id="kegiatan_utama" name="kegiatan_utama_id" disabled>
												<option value="{{$activity->kegiatan_utama_id}}">{{$activity->nama_kegiatan_utama}}</option>
											</select>                                        
										</div>
										<div class="form-group">
											<label for="kegiatan"><b>Nama Kegiatan:</b></label>
											<input type="text" class="form-control" name="kegiatan" value="{{ $activity->kegiatan }}" />
										</div>
										<div class="form-group">
											<label for="keterangan_kegiatan"><b>Keterangan Kegiatan:</b></label>
											<div id="editor-container"></div>
										</div>
										<div class="form-group">
											<label for="kuantitas"><b>Jumlah:</b></label>
											<input type="number" class="form-control" name="kuantitas" value="{{ $activity->kuantitas }}" />
										</div>
										<div class="form-group">
											<label for="satuan"><b>Satuan:</b></label>
											<input type="text" class="form-control" name="satuan" value="{{ $activity->satuan }}" />
										</div>
										<div class="form-group">
											<label for="is_done"><b>Status Penyelesaian</b></label>
											<select id="is_done" class="form-control" name="is_done">
												<option value="1" @if($activity->is_done == "1") selected @endif>Sudah Selesai</option>
												<option value="2" @if($activity->is_done == "2") selected @endif>Belum Selesai</option>
											</select>
										</div>
										<div class="form-group">
											<input type="checkbox" id="checkbox" name="checkbox">
											<label for="checkbox">Ceklist jika selesai pada waktu yang bukan pada saat sekarang ini</label><br>
											<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
											<script type="text/javascript">
												$(function () {
													$('input[name="tgl_selesai"]').hide();
													$('input[name="checkbox"]').on('click', function () {
														if ($(this).prop('checked')) {
															$('input[name="tgl_selesai"]').fadeIn();
														} else {
															$('input[name="tgl_selesai"]').hide();
														}
													});
												});
											</script>
											<input type="date" class="form-control form-control-lg mb-3" name="tgl_selesai" value="{{ $activity->tgl }}">
										</div>
										<div class="form-group">
											<label for="berkas0"><b>Bukti Kegiatan:<b></label>
												<br>
												<input type="checkbox" id="toggleCheckbox" onclick="toggleForm()">
												<label for="toggleCheckbox">Ceklist Jika Ingin Menggunakan Opsi Pencantuman Link</label>
												<div id="formContainer" class="hidden">
													<label for="link">Link Bukti Kegiatan:</label>
													<input type="link" class="form-control form-control-lg mb-3" name="link" value="{{ $activity->link }}">
												</div>
												<div id="formContainer2" class="hidden">
													<label for="berkas">Berkas Bukti Kegiatan:</label>
													<input type="file" name="berkas">
												</div>
												<script>
													function toggleForm() {
														var checkbox = document.getElementById("toggleCheckbox");
														var formContainer = document.getElementById("formContainer");
														var formContainer2 = document.getElementById("formContainer2");
														var textLabel = document.getElementById("textLabel");

														if (checkbox.checked) {
															formContainer.classList.remove("hidden");													
															formContainer2.classList.add("hidden");
														} else {
															formContainer.classList.add("hidden");
															formContainer2.classList.remove("hidden");
														}
													}
												</script>
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
			<!-- End of Content -->

			<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
			<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />

			<script>
				// Inisialisasi Quill
				var quill = new Quill('#editor-container', {
					theme: 'snow',
					placeholder: 'Masukkan keterangan kegiatan...',
				});
				quill.root.innerHTML = {!! json_encode($activity->keterangan) !!};
				$('form').on('submit', function () {
					var content = quill.root.innerHTML;
					$('<input>').attr({
						type: 'hidden',
						name: 'keterangan_kegiatan',
						value: content
					}).appendTo(this);
				});
			</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function(){
        $('#jenis_kegiatan').change(function() {
            var jenis_kegiatan = $(this).val();
            if(jenis_kegiatan == 'TAMBAHAN') {
                // Hide fields when 'TAMBAHAN' is selected
                $('#tim_kerja_field').hide();
                $('#project_field').hide();
                $('#kegiatan_utama_field').hide();
            } else {
                // Show fields when 'UTAMA' is selected
                $('#tim_kerja_field').show();
                $('#project_field').show();
                $('#kegiatan_utama_field').show();
            }
        });
    });
</script>
<script>
    $(document).ready(function() {
        $('#tim_kerja_id').change(function() {
            var tim_kerja_id = $(this).val();
            $("#project").html('');
            if (tim_kerja_id) {
                // var url = '{{ url("kegiatanutama/getProject") }}/' + tim_kerja_id;
                // console.log('Project:', url);
                $.ajax({
                    url: '{{ url("temp/getProject") }}/' + tim_kerja_id,                    
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        // console.log("Respons JSON:", data); // Debugging respons
                        $('#project').empty().append('<option value="" selected disabled>Pilih Project</option>');
                        if ($.isEmptyObject(data)) {
                            alert('Tidak ada project untuk Tim Kerja yang dipilih.');
                        } else {
                            $.each(data, function(key, value) {
                                $('#project').append('<option value="'+ key +'">'+ value +'</option>');
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error:", xhr.responseText); // Debugging error
                        alert('Gagal mengambil data. Silakan coba lagi.');
                    }
                });
            } else {
                $('#project').empty().append('<option value="" selected disabled>Pilih Project</option>');
            }
        });


        $('#project').change(function() {
            var project_id = $(this).val();
            $("#kegiatanutama").html('');
            if (project_id) {
                $.ajax({
                    url: '{{ url("temp/getKegiatanutama") }}/' + project_id,                    
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        // console.log("Respons JSON:", data); // Debugging respons
                        $('#kegiatan_utama').empty().append('<option value="" selected disabled>Pilih Project</option>');
                        if ($.isEmptyObject(data)) {
                            alert('Tidak ada kegiatan_utama untuk Tim Kerja yang dipilih.');
                        } else {
                            $.each(data, function(key, value) {
                                $('#kegiatan_utama').append('<option value="'+ key +'">'+ value +'</option>');
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error:", xhr.responseText); // Debugging error
                        alert('Gagal mengambil data. Silakan coba lagi.');
                    }
                });
            } else {
                $('#kegiatan_utama').empty().append('<option value="" selected disabled>Pilih kegiatan utama</option>');
            }
        });
    });

</script>


@endsection
