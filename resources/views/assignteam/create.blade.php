@extends('layouts.template')

@section('content')

<div class="page-header row no-gutters py-4">
    <div class="col-12 col-sm-4 text-center text-sm-left mb-0">
        <span class="text-uppercase page-subtitle">KHI</span>
        <h3 class="page-title">Alokasi Anggota Tim {{ date('Y') }}</h3>
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
                            <form action="{{ route('assigntim.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label for="">Tim Kerja</label>
                                    <select class="form-control" id="tim_kerja_id" name="tim_kerja_id" required>
                                        <option value="" selected disabled>Pilih</option>
                                        @foreach($timkerja as $item)
                                        <option value="{{ $item->id }}">{{ $item->nama_tim_kerja }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">Project</label>
                                    <select class="form-control" id="project" name="project_id" required>
                                        <option value="">Pilih Project</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">Kegiatan Utama</label>
                                    <select class="form-control" id="kegiatan_utama" name="kegiatan_utama_id" required>
                                        <option value="">Pilih Kegiatan Utama</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">Anggota Tim Kerja</label>
                                    <div id="dynamic-input">
                                        <div class="input-group mb-2">
                                            <select class="form-control" name="anggota_nip[]" required>
                                                <option value="" selected disabled>Pilih</option>
                                                @foreach($candidate as $item)
                                                <option value="{{ $item->nip }}">{{ $item->fullname }}</option>
                                                @endforeach
                                            </select>
                                            <button type="button" class="btn btn-danger remove-field">Remove</button>
                                        </div>
                                    </div>
                                    <button type="button" id="add-field" class="btn btn-primary">Tambah Anggota</button>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#tim_kerja_id').change(function() {
            var tim_kerja_id = $(this).val();
            $("#project").html('');
            if (tim_kerja_id) {
                // var url = '{{ url("kegiatanutama/getProject") }}/' + tim_kerja_id;
                // console.log('Project:', url);
                $.ajax({
                    url: '{{ url("kegiatanutama/getProject") }}/' + tim_kerja_id,
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
                    url: '{{ url("assigntim/getKegiatanutama") }}/' + project_id,
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
<script type="text/javascript">
    $(document).ready(function() {
    // Menambahkan input anggota tim kerja
        $('#add-field').click(function() {
            var inputField = `
            <div class="input-group mb-2">
            <select class="form-control" name="anggota_nip[]" required>
            <option value="" selected disabled>Pilih</option>
            @foreach($candidate as $item)
            <option value="{{ $item->nip }}">{{ $item->fullname }}</option>
            @endforeach
            </select>
            <button type="button" class="btn btn-danger remove-field">Remove</button>
            </div>
            `;
            $('#dynamic-input').append(inputField);
        });

    // Menghapus input anggota tim kerja
        $(document).on('click', '.remove-field', function() {
            $(this).closest('.input-group').remove();
        });
    });

</script>

@endsection
