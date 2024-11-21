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
		<h3 class="page-title">Edit Lisensi</h3>
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
							<form action="{{ route('licensedapp.update', $licensedapp->id) }}" method="POST" enctype="multipart/form-data">
								@csrf
								@method('PUT')
								<div class="form-group">
                                    <label for="kegiatan">Nama Aplikasi:</label>
                                    <input type="text" class="form-control" name="nama_aplikasi" value="{{ $licensedapp->nama_aplikasi }}"/>
                                </div>
                                <div class="form-group">
									<label for="keterangan"><b>Keterangan Aplikasi:</b></label>
									<div id="editor-container"></div>
								</div>
                                <div class="form-group">
                                    <label for="awal_lisensi">Awal Lisensi</label>
                                    <input type="date" class="form-control form-control-lg mb-3" name="awal_lisensi" value="{{ $licensedapp->awal_lisensi }}">
                                </div>
                                <div class="form-group">
                                    <label for="akhir_lisensi">Akhir Lisensi</label>
                                    <input type="date" class="form-control form-control-lg mb-3" name="akhir_lisensi" value="{{ $licensedapp->akhir_lisensi }}">
                                </div>
                                <div class="form-group">
                                    <label for="username">Username:</label>
                                    <input type="text" class="form-control" name="username" value="{{ $licensedapp->username }}"/>
                                </div>
                                <div class="form-group">
                                    <label for="password">Password:</label>
                                    <input type="text" class="form-control" name="password" value="{{ $licensedapp->password }}"/>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />

<script>
    // Inisialisasi Quill
    var quill = new Quill('#editor-container', {
        theme: 'snow',
        placeholder: 'Masukkan keterangan aplikasi...',
    });

    // Set konten editor jika sudah ada data keterangan
    quill.root.innerHTML = {!! json_encode($licensedapp->keterangan) !!};

    // Sebelum form disubmit, pastikan 'keterangan' ada dalam input hidden
    $('form').on('submit', function () {
        var content = quill.root.innerHTML || "";  // Ambil konten editor
        // Tambahkan input hidden untuk 'keterangan'
        $('<input>').attr({
            type: 'hidden',
            name: 'keterangan',
            value: content
        }).appendTo(this);

        // Debugging: Cek apakah input hidden berhasil ditambahkan
        console.log("Input hidden untuk keterangan berhasil ditambahkan.");
    });
</script>

@endsection
