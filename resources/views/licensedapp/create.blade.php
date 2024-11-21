@extends('layouts.template')

@section('content')

<div class="page-header row no-gutters py-4">
    <div class="col-12 col-sm-4 text-center text-sm-left mb-0">
        <span class="text-uppercase page-subtitle">KHI</span>
        <h3 class="page-title">Input Lisensi Aplikasi</h3>
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
                            <form action="{{ route('licensedapp.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label for="kegiatan">Nama Aplikasi:</label>
                                    <input type="text" class="form-control" name="nama_aplikasi" required/>
                                </div>
                                <div class="form-group">
                                    <label for="keterangan">Keterangan Aplikasi:</label>
                                    <div id="keterangan" style="height: 200px;"></div>
                                </div>
                                <div class="form-group">
                                    <label for="awal_lisensi">Awal Lisensi</label>
                                    <input type="date" class="form-control form-control-lg mb-3" name="awal_lisensi" required>
                                </div>
                                <div class="form-group">
                                    <label for="akhir_lisensi">Akhir Lisensi</label>
                                    <input type="date" class="form-control form-control-lg mb-3" name="akhir_lisensi" required>
                                </div>
                                <div class="form-group">
                                    <label for="username">Username:</label>
                                    <input type="text" class="form-control" name="username" required/>
                                </div>
                                <div class="form-group">
                                    <label for="password">Password:</label>
                                    <input type="text" class="form-control" name="password" required/>
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

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<!-- Add Quill CSS and JS -->
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>

<script>
    // Initialize Quill editor for the "Keterangan Kegiatan" field
    var quill = new Quill('#keterangan', {
        theme: 'snow', // Theme for the editor
        placeholder: 'Masukkan keterangan kegiatan berupa nama proses detail atau rincian tahapan kegiatan, field input ini bisa juga diabaian jika nama kegiatan sudah unik dan tidak perlu pemisahan', // Placeholder text
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline', 'strike'],
                ['blockquote', 'code-block'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['link'],
                [{ 'align': [] }],
                ['clean']
            ]
        }
    });

    // Optional: If you need to capture the content of the editor to save it in a hidden field
    $('form').submit(function() {
        // Assign Quill editor content to a hidden input field before form submission
        var keterangan = quill.root.innerHTML;
        $('<input>').attr({
            type: 'hidden',
            name: 'keterangan',
            value: keterangan
        }).appendTo('form');
    });
</script>

@endsection
