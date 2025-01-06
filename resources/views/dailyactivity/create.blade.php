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
    <div class="col-lg-12 col-md-12 text-right">        
        <a href="{{ route('act.createdbyteam') }}" class="btn btn-primary">
            <i class="fa fa-plus"></i> Penugasan Anggota
        </a>
    </div>
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
                                    <label for="tgl">Tanggal</label>
                                    <input type="date" class="form-control form-control-lg mb-3" name="tgl" required>
                                </div>
                                <div class="form-group">
                                    <label for="wfo_wfh">WFO/WFH:</label>
                                    <select class="form-control" id="wfo_wfh" name="wfo_wfh" required>
                                        <option value="WFO" selected>WFO - Work From Office</option>
                                        <option value="TL">Tugas Luar</option>
                                        <option value="Lainnya">Lainnya (Cuti, Sakit, Izin)</option>
                                    </select>
                                </div>

                                @if (Auth::user()->id == 2)
                                <div class="form-group">
                                    <label for="jenis_kegiatan">Pekerjaan Utama/Tambahan</label>
                                    <select class="form-control" name="jenis_kegiatan" required>
                                        <option value="UTAMA" selected>Pekerjaan Utama</option>
                                        <option value="TAMBAHAN">Pekerjaan Tambahan</option>
                                    </select>
                                </div>

                                @else
                                <div class="form-group">
                                    <label for="jenis_kegiatan">Pekerjaan Utama/Tambahan</label>
                                    <select class="form-control" id="jenis_kegiatan" name="jenis_kegiatan" required>
                                        <option value="UTAMA" selected>Pekerjaan Utama</option>
                                        <option value="TAMBAHAN">Pekerjaan Tambahan</option>
                                    </select>
                                </div>
                                <div class="form-group" id="tim_kerja_field">
                                    <label for="">Tim Kerja</label>                                    
                                    <select class="form-control" id="tim_kerja_id" name="tim_kerja_id">
                                        <option value="" selected disabled>Pilih</option>
                                        @foreach($TimKerja as $nama_tim_kerja => $tim_kerja_id)
                                            <option value="{{ $tim_kerja_id }}">{{ $nama_tim_kerja }}</option>
                                        @endforeach
                                    </select>
                                </div>                                
                                <div class="form-group" id="project_field">
                                    <label for="">Project</label>                                    
                                    <select class="form-control" id="project" name="project_id">
                                        <option value="">Pilih Project</option>
                                    </select>                                        
                                </div>
                                <div class="form-group" id="kegiatan_utama_field">
                                    <label for="">Kegiatan Utama</label>                                    
                                    <select class="form-control" id="kegiatan_utama" name="kegiatan_utama_id">
                                        <option value="">Pilih Kegiatan Utama</option>
                                    </select>                                        
                                </div>

                                @endif
                                
                                <div class="form-group">
                                    <label for="kegiatan">Nama Kegiatan:</label>
                                    <input list="kegiatan-options" class="form-control" name="kegiatan" id="kegiatan" autocomplete="off" required/>
                                    <datalist id="kegiatan-options">
                                        <!-- Options will be populated via JavaScript -->
                                    </datalist>
                                </div>
                                <div class="form-group">
                                    <label for="kuantitas">Jumlah:</label>
                                    <input type="number" class="form-control" name="kuantitas" required/>
                                </div>
                                <div class="form-group">
                                    <label for="satuan">Satuan:</label>
                                    <input type="text" class="form-control" name="satuan" required/>
                                </div>
                                <div class="form-group">
                                    <label for="keterangan_kegiatan">Keterangan Kegiatan:</label>
                                    <div id="keterangan_kegiatan" style="height: 200px;"></div>
                                </div>                                
                                
                                <div class="form-group">
                                    <label for="is_done"><b>Status Penyelesaian</b></label>
                                    <select id="is_done" class="form-control" name="is_done">
                                        <option value="1">Sudah Selesai</option>
                                        <option value="2">Belum Selesai</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="berkas0"><b>Bukti Kegiatan:<b></label>
                                        <br>
                                        <input type="checkbox" id="toggleCheckbox" onclick="toggleForm()">
                                        <label for="toggleCheckbox">Ceklist Jika Ingin Menggunakan Opsi Pencantuman Link</label>
                                        <div id="formContainer" class="hidden">
                                            <label for="link">Link Bukti Kegiatan:</label>
                                            <input type="link" class="form-control form-control-lg mb-3" name="link">
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
<script>
    $(document).ready(function(){
        $('#kegiatan').on('input', function(){
            let query = $(this).val();

            if(query.length > 1) {
                $.ajax({
                    url: "{{ route('autocomplete.search') }}",
                    type: "GET",
                    data: {'query': query},
                    success: function(data){
                        let options = '';
                        data.forEach(function(item){
                            options += '<option value="'+item.kegiatan+'">';
                        });
                        $('#kegiatan-options').html(options);
                    }
                });
            } else {
                $('#kegiatan-options').html('');
            }
        });
    });
</script>
<!-- Add Quill CSS and JS -->
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>

<script>
    // Initialize Quill editor for the "Keterangan Kegiatan" field
    var quill = new Quill('#keterangan_kegiatan', {
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
            name: 'keterangan_kegiatan',
            value: keterangan
        }).appendTo('form');
    });
</script>

@endsection
