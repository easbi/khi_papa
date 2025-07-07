@extends('layouts.template')

@section('content')

<div class="page-header row no-gutters py-4">
    <div class="col-12 col-sm-4 text-center text-sm-left mb-0">
        <span class="text-uppercase page-subtitle">KHI</span>
        <h3 class="page-title">Penugasan oleh Ketua Tim</h3>
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
                            <form action="{{ route('act.storebyteam') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <!-- Multiple Team Member Selection -->
                                <div class="form-group">
                                    <label for="">Anggota Tim Kerja</label>
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="selectAll">
                                                <label class="form-check-label" for="selectAll">
                                                    <strong>Pilih Semua</strong>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="card-body" style="max-height: 200px; overflow-y: auto;">
                                            @foreach($teammember as $index => $member)
                                                <div class="form-check">
                                                    <input class="form-check-input member-checkbox"
                                                           type="checkbox"
                                                           name="anggota_nip[]"
                                                           value="{{ $member->nip }}"
                                                           id="member_{{ $index }}">
                                                    <label class="form-check-label" for="member_{{ $index }}">
                                                        {{ $member->fullname }} ({{ $member->nip }})
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">Pilih satu atau lebih anggota tim kerja</small>
                                </div>

                                <div class="form-group">
                                    <label for="tgl">Tanggal Mulai</label>
                                    <input type="date" class="form-control form-control-lg mb-3" name="tgl" required>
                                </div>

                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_repeated" name="is_repeated" value="1">
                                        <label class="form-check-label" for="is_repeated">
                                            <strong>Kegiatan Berulang (Hari Kerja)</strong>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Centang jika kegiatan ini berlangsung dalam rentang waktu tertentu (tidak termasuk Sabtu-Minggu)</small>
                                </div>

                                <div class="form-group" id="tgl_akhir_field" style="display: none;">
                                    <label for="tgl_akhir">Tanggal Akhir</label>
                                    <input type="date" class="form-control form-control-lg mb-3" name="tgl_akhir">
                                    <small class="form-text text-muted">Kegiatan akan dibuat untuk setiap hari kerja (Senin-Jumat) dalam rentang ini</small>
                                </div>

                                <div class="form-group">
                                    <label for="wfo_wfh">WFO/WFH:</label>
                                    <select class="form-control" id="wfo_wfh" name="wfo_wfh" required>
                                        <option value="WFO" selected>WFO - Work From Office</option>
                                        <option value="TL">Tugas Luar</option>
                                        <option value="Lainnya">Lainnya (Cuti, Sakit, Izin)</option>
                                    </select>
                                </div>

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
                                        @foreach($TimKerja as $TimKerja)
                                            <option value="{{ $TimKerja->tim_kerja_id }}">{{ $TimKerja->nama_tim_kerja }}</option>
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
        // Kegiatan berulang toggle
        $('#is_repeated').change(function(){
            if($(this).is(':checked')){
                $('#tgl_akhir_field').show();
                $('input[name="tgl_akhir"]').attr('required', true);
            } else {
                $('#tgl_akhir_field').hide();
                $('input[name="tgl_akhir"]').attr('required', false);
                $('input[name="tgl_akhir"]').val('');
            }
        });

        // Validasi tanggal akhir
        $('input[name="tgl_akhir"]').change(function(){
            var tglMulai = $('input[name="tgl"]').val();
            var tglAkhir = $(this).val();

            if(tglMulai && tglAkhir && tglAkhir <= tglMulai) {
                alert('Tanggal akhir harus lebih besar dari tanggal mulai');
                $(this).val('');
            }
        });

        // Select All functionality
        $('#selectAll').change(function(){
            if($(this).is(':checked')){
                $('.member-checkbox').prop('checked', true);
            } else {
                $('.member-checkbox').prop('checked', false);
            }
        });

        // Individual checkbox change
        $('.member-checkbox').change(function(){
            if($('.member-checkbox:checked').length === $('.member-checkbox').length){
                $('#selectAll').prop('checked', true);
            } else {
                $('#selectAll').prop('checked', false);
            }
        });

        // Kegiatan autocomplete
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
        theme: 'snow',
        placeholder: 'Masukkan keterangan kegiatan berupa nama proses detail atau rincian tahapan kegiatan, field input ini bisa juga diabaian jika nama kegiatan sudah unik dan tidak perlu pemisahan',
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

    // Form submission handler
    $('form').submit(function(e) {
        // Check if at least one member is selected
        if($('.member-checkbox:checked').length === 0) {
            e.preventDefault();
            alert('Silakan pilih minimal satu anggota tim kerja');
            return false;
        }

        // Assign Quill editor content to a hidden input field before form submission
        var keterangan = quill.root.innerHTML;
        $('<input>').attr({
            type: 'hidden',
            name: 'keterangan_kegiatan',
            value: keterangan
        }).appendTo('form');
    });
</script>

<script>
    $(document).ready(function(){
        $('#jenis_kegiatan').change(function() {
            var jenis_kegiatan = $(this).val();
            if(jenis_kegiatan == 'TAMBAHAN') {
                $('#tim_kerja_field').hide();
                $('#project_field').hide();
                $('#kegiatan_utama_field').hide();
            } else {
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
                $.ajax({
                    url: '{{ url("temp/getProject") }}/' + tim_kerja_id,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
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
                        console.error("Error:", xhr.responseText);
                        alert('Gagal mengambil data. Silakan coba lagi.');
                    }
                });
            } else {
                $('#project').empty().append('<option value="" selected disabled>Pilih Project</option>');
            }
        });

        $('#project').change(function() {
            var project_id = $(this).val();
            $("#kegiatan_utama").html('');
            if (project_id) {
                $.ajax({
                    url: '{{ url("temp/getKegiatanutama") }}/' + project_id,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        $('#kegiatan_utama').empty().append('<option value="" selected disabled>Pilih Kegiatan Utama</option>');
                        if ($.isEmptyObject(data)) {
                            alert('Tidak ada kegiatan_utama untuk Project yang dipilih.');
                        } else {
                            $.each(data, function(key, value) {
                                $('#kegiatan_utama').append('<option value="'+ key +'">'+ value +'</option>');
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error:", xhr.responseText);
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
