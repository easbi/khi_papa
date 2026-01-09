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
                                        <label for="tgl">Tanggal Mulai Kegiatan</label>
                                        <input type="date" id="tgl" class="form-control form-control-lg mb-3" name="tgl"
                                            required>
                                    </div>

                                    <div class="form-group custom-control custom-checkbox mb-1">
                                        <input type="checkbox" class="custom-control-input" id="is_repeated"
                                            name="is_repeated" onclick="toggleEndDate()" value="1">
                                        <label class="custom-control-label" for="is_repeated">Kegiatan Berulang? (dihitung
                                            dalam hari)</label>
                                    </div>

                                    <div class="form-group" id="end_date_group" style="display: none;">
                                        <label for="tgl_akhir">Tanggal Akhir Kegiatan</label>
                                        <input type="date" id="tgl_akhir" class="form-control form-control-lg mb-3" name="tgl_akhir">
                                    </div>

                                    <script>
                                        function toggleEndDate() {
                                            const isRepeated = document.getElementById("is_repeated").checked;
                                            const endDateGroup = document.getElementById("end_date_group");
                                            const kuantitas = document.getElementById("kuantitas");
                                            const satuan = document.getElementById("satuan");

                                            // Tampilkan atau sembunyikan elemen berdasarkan checkbox
                                            if (isRepeated) {
                                                endDateGroup.style.display = "block";
                                                kuantitas.style.display = "none";
                                                satuan.style.display = "none";
                                            } else {
                                                endDateGroup.style.display = "none";
                                                kuantitas.style.display = "block";
                                                satuan.style.display = "block";
                                            }
                                        }
                                    </script>

                                    <!-- Checkbox Reminder -->
                                    <div class="form-group custom-control custom-checkbox mb-2">
                                        <input type="checkbox" class="custom-control-input" id="enable_reminder"
                                            onclick="toggleReminder()" name="enable_reminder" value="1">
                                        <label class="custom-control-label" for="enable_reminder">Ingin diingatkan?</label>
                                    </div>

                                    <!-- Form Input Reminder Time -->
                                    <div class="form-group" id="reminder_time_group" style="display: none;">
                                        <label for="reminder_time">Waktu Reminder</label>
                                        <input type="time" name="reminder_time" class="form-control">
                                    </div>

                                    <script>
                                        function toggleReminder() {
                                            const reminderEnabled = document.getElementById("enable_reminder").checked;
                                            const reminderGroup = document.getElementById("reminder_time_group");

                                            reminderGroup.style.display = reminderEnabled ? "block" : "none";
                                        }
                                    </script>



                                    <div class="form-group">
                                        <label for="wfo_wfh">WFO/WFH:</label>
                                        <select class="form-control" id="wfo_wfh" name="wfo_wfh" required>
                                            <option value="WFO" selected>WFO - Work From Office</option>
                                            <option value="WFA">WFA - Work From Anywhere</option>
                                            <option value="Lembur">Lembur (Official)</option>
                                            <option value="Adhoc">Tugas Adhoc / Genting / Prioritas</option>
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
                                                @foreach ($TimKerja as $nama_tim_kerja => $tim_kerja_id)
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
                                        <input list="kegiatan-options" class="form-control" name="kegiatan"
                                            id="kegiatan" autocomplete="off" required />
                                        <datalist id="kegiatan-options">
                                            <!-- Options will be populated via JavaScript -->
                                        </datalist>
                                    </div>
                                    <div class="form-group" id="kuantitas" style="display: block;">
                                        <label for="kuantitas">Jumlah:</label>
                                        <input type="number" class="form-control" name="kuantitas" />
                                    </div>
                                    <div class="form-group" id="satuan" style="display: block;">
                                        <label for="satuan">Satuan:</label>
                                        <select class="form-control" name="satuan" id="satuanSelect" required>
                                            <option value="">-- Pilih Satuan --</option>
                                            <option value="Kegiatan" selected>Kegiatan</option>
                                            <option value="Hari">Hari</option>
                                            <option value="Dokumen">Dokumen</option>
                                            <option value="Laporan">Laporan</option>
                                            <option value="Publikasi">Publikasi</option>
                                            <option value="Blok Sensus">Blok Sensus</option>
                                            <option value="Paket">Paket</option>
                                            <option value="Sampel">Sampel</option>
                                            <option value="Tabel">Tabel</option>
                                            <option value="File">File</option>
                                            <option value="Daftar">Daftar</option>
                                            <option value="Responden">Responden</option>
                                            <option value="Jam Pelatihan">Jam Pelatiha (JP)</option>
                                            <option value="Transaksi">Transaksi</option>
                                            <option value="Jam">Jam</option>
                                            <option value="Petugas">Petugas</option>
                                            <option value="Kali">Kali</option>
                                            <option value="E-Form">E-Form</option>
                                            <option value="Buku">Buku</option>
                                            <option value="Konten">Konten</option>
                                            <option value="Rumah Tangga">Rumah Tangga</option>
                                            <option value="Pertemuan">Pertemuan</option>
                                            <option value="Bab">Bab</option>
                                            <option value="Blok">Blok</option>
                                            <option value="Segmen">Segmen</option>
                                        </select>
                                    </div>


                                    <div class="form-group">
                                        <label for="keterangan_kegiatan">Keterangan Kegiatan:</label>
                                        <div id="keterangan_kegiatan" style="height: 200px;"></div>
                                    </div>

                                    <div class="form-group">
                                        <label for="is_done"><b>Status Penyelesaian</b></label>
                                        <select id="is_done" class="form-control" name="is_done">
                                            <option value="2" selected>Belum Selesai</option>
                                            <option value="1">Sudah Selesai</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="berkas0"><b>Bukti Kegiatan:<b></label>
                                        <br>
                                        <input type="checkbox" id="toggleCheckbox" onclick="toggleForm()">
                                        <label for="toggleCheckbox">Ceklist Jika Ingin Menggunakan Opsi Pencantuman
                                            Link</label>
                                        <div id="formContainer" class="hidden">
                                            <label for="link">Link Bukti Kegiatan:</label>
                                            <input type="link" class="form-control form-control-lg mb-3"
                                                name="link">
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
        $(document).ready(function() {
            $('#kegiatan').on('input', function() {
                let query = $(this).val();

                if (query.length > 1) {
                    $.ajax({
                        url: "{{ route('autocomplete.search') }}",
                        type: "GET",
                        data: {
                            'query': query
                        },
                        success: function(data) {
                            let options = '';
                            data.forEach(function(item) {
                                options += '<option value="' + item.kegiatan + '">';
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
                    [{
                        'list': 'ordered'
                    }, {
                        'list': 'bullet'
                    }],
                    ['link'],
                    [{
                        'align': []
                    }],
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


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const jenisKegiatan = document.getElementById('jenis_kegiatan');
            const timKerjaField = document.getElementById('tim_kerja_field');
            const projectField = document.getElementById('project_field');
            const kegiatanUtamaField = document.getElementById('kegiatan_utama_field');

            // Fungsi untuk validasi sebelum submit form
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                const selectedJenisKegiatan = jenisKegiatan.value;

                // Validasi jika "Pekerjaan Utama" dipilih
                if (selectedJenisKegiatan === 'UTAMA') {
                    const timKerja = document.getElementById('tim_kerja_id').value;
                    const project = document.getElementById('project').value;
                    const kegiatanUtama = document.getElementById('kegiatan_utama').value;

                    if (!timKerja || !project || !kegiatanUtama) {
                        e.preventDefault(); // Mencegah pengiriman form
                        alert(
                            'Untuk Pekerjaan Utama, Anda harus mengisi semua kolom: Tim Kerja, Project, dan Kegiatan Utama.'
                        );
                        return;
                    }
                }

                // Validasi aturan fitur berulang
                const isRepeated = document.getElementById('is_repeated').checked;
                if (isRepeated) {
                    const kegiatanName = (document.getElementById('kegiatan').value || '').toString();
                    const containsPelatihan = kegiatanName.toLowerCase().includes('pelatihan');

                    const tglVal = document.getElementById('tgl').value;
                    const tglAkhirVal = document.getElementById('tgl_akhir').value;

                    if (!tglVal || !tglAkhirVal) {
                        e.preventDefault();
                        alert('Jika memilih Kegiatan Berulang, isi tanggal mulai dan tanggal akhir.');
                        return;
                    }

                    const start = new Date(tglVal);
                    const end = new Date(tglAkhirVal);

                    if (isNaN(start.getTime()) || isNaN(end.getTime())) {
                        e.preventDefault();
                        alert('Tanggal tidak valid.');
                        return;
                    }

                    if (end < start) {
                        e.preventDefault();
                        alert('Tanggal akhir harus sama atau setelah tanggal mulai.');
                        return;
                    }

                    // Hitung jumlah hari kerja (Senin-Jumat) dalam rentang inklusif
                    let countWorkDays = 0;
                    let cursor = new Date(start);
                    while (cursor <= end) {
                        const day = cursor.getDay(); // 0 = Sun, 6 = Sat
                        if (day !== 0 && day !== 6) countWorkDays++;
                        cursor.setDate(cursor.getDate() + 1);
                    }

                    const maxDays = containsPelatihan ? 7 : 3;

                    if (countWorkDays > maxDays) {
                        e.preventDefault();
                        if (containsPelatihan) {
                            alert('Nama kegiatan mengandung kata "pelatihan" — rentang tanggal berulang maksimal ' + maxDays + ' hari kerja (Senin-Jumat).');
                        } else {
                            alert('Untuk kegiatan tanpa kata "pelatihan", rentang tanggal berulang maksimal ' + maxDays + ' hari kerja (Senin-Jumat).');
                        }
                        return;
                    }
                }
            });

            // Fungsi untuk menampilkan atau menyembunyikan field berdasarkan pilihan "Jenis Kegiatan"
            jenisKegiatan.addEventListener('change', function() {
                if (jenisKegiatan.value === 'UTAMA') {
                    // Wajib diisi
                    timKerjaField.style.display = 'block';
                    projectField.style.display = 'block';
                    kegiatanUtamaField.style.display = 'block';

                    // Tambahkan atribut required
                    document.getElementById('tim_kerja_id').setAttribute('required', 'required');
                    document.getElementById('project').setAttribute('required', 'required');
                    document.getElementById('kegiatan_utama').setAttribute('required', 'required');
                } else if (jenisKegiatan.value === 'TAMBAHAN') {
                    // Tidak wajib diisi
                    timKerjaField.style.display = 'none';
                    projectField.style.display = 'none';
                    kegiatanUtamaField.style.display = 'none';

                    // Hapus atribut required
                    document.getElementById('tim_kerja_id').removeAttribute('required');
                    document.getElementById('project').removeAttribute('required');
                    document.getElementById('kegiatan_utama').removeAttribute('required');
                }
            });

            // Trigger initial state
            jenisKegiatan.dispatchEvent(new Event('change'));
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#tim_kerja_id').change(function() {
                var tim_kerja_id = $(this).val();
                $("#project").html('');
                if (tim_kerja_id) {
                    // var url = '{{ url('kegiatanutama/getProject') }}/' + tim_kerja_id;
                    // console.log('Project:', url);
                    $.ajax({
                        url: '{{ url('temp/getProject') }}/' + tim_kerja_id,
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
                            // console.log("Respons JSON:", data); // Debugging respons
                            $('#project').empty().append(
                                '<option value="" selected disabled>Pilih Project</option>');
                            if ($.isEmptyObject(data)) {
                                alert('Tidak ada project untuk Tim Kerja yang dipilih.');
                            } else {
                                $.each(data, function(key, value) {
                                    $('#project').append('<option value="' + key +
                                        '">' + value + '</option>');
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error:", xhr.responseText); // Debugging error
                            alert('Gagal mengambil data. Silakan coba lagi.');
                        }
                    });
                } else {
                    $('#project').empty().append(
                        '<option value="" selected disabled>Pilih Project</option>');
                }
            });

            $('#project').change(function() {
                var project_id = $(this).val();
                $("#kegiatanutama").html('');
                if (project_id) {
                    $.ajax({
                        url: '{{ url('temp/getKegiatanutama') }}/' + project_id,
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
                            // console.log("Respons JSON:", data); // Debugging respons
                            $('#kegiatan_utama').empty().append(
                                '<option value="" selected disabled>Pilih Project</option>');
                            if ($.isEmptyObject(data)) {
                                alert('Tidak ada kegiatan_utama untuk Tim Kerja yang dipilih.');
                            } else {
                                $.each(data, function(key, value) {
                                    $('#kegiatan_utama').append('<option value="' +
                                        key + '">' + value + '</option>');
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error:", xhr.responseText); // Debugging error
                            alert('Gagal mengambil data. Silakan coba lagi.');
                        }
                    });
                } else {
                    $('#kegiatan_utama').empty().append(
                        '<option value="" selected disabled>Pilih kegiatan utama</option>');
                }
            });
        });
    </script>

@endsection
