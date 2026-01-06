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
                                            @foreach($candidate as $index => $member)
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
<script>
    $(document).ready(function() {
        // Select All functionality for anggota checkboxes
        $('#selectAll').change(function(){
            if($(this).is(':checked')){
                $('.member-checkbox').prop('checked', true);
            } else {
                $('.member-checkbox').prop('checked', false);
            }
        });

        // Individual checkbox change updates selectAll state
        $(document).on('change', '.member-checkbox', function(){
            if($('.member-checkbox:checked').length === $('.member-checkbox').length){
                $('#selectAll').prop('checked', true);
            } else {
                $('#selectAll').prop('checked', false);
            }
        });

        // Form submit validation: require at least one member
        $('form').submit(function(e) {
            if($('.member-checkbox:checked').length === 0) {
                e.preventDefault();
                alert('Silakan pilih minimal satu anggota tim kerja');
                return false;
            }
        });
    });
</script>

@endsection
