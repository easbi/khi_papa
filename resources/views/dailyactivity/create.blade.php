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
                                            value="{{ isset($activity) ? $activity->tgl : '' }}" required>
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
                                            <option value="WFO" {{ isset($activity) && $activity->wfo_wfh == 'WFO' ? 'selected' : (isset($activity) ? '' : 'selected') }}>WFO - Work From Office</option>
                                            <option value="WFA" {{ isset($activity) && $activity->wfo_wfh == 'WFA' ? 'selected' : '' }}>WFA - Work From Anywhere</option>
                                            <option value="Lembur" {{ isset($activity) && $activity->wfo_wfh == 'Lembur' ? 'selected' : '' }}>Lembur (Official)</option>
                                            <option value="Adhoc" {{ isset($activity) && $activity->wfo_wfh == 'Adhoc' ? 'selected' : '' }}>Tugas Adhoc / Genting / Prioritas</option>
                                            <option value="TL" {{ isset($activity) && $activity->wfo_wfh == 'TL' ? 'selected' : '' }}>Tugas Luar</option>
                                            <option value="Lainnya" {{ isset($activity) && $activity->wfo_wfh == 'Lainnya' ? 'selected' : '' }}>Lainnya (Cuti, Sakit, Izin)</option>
                                        </select>
                                    </div>

                                    @if (Auth::user()->id == 2)
                                        <div class="form-group">
                                            <label for="jenis_kegiatan">Pekerjaan Utama/Tambahan</label>
                                            <select class="form-control" name="jenis_kegiatan" required>
                                                <option value="UTAMA" {{ isset($activity) && $activity->jenis_kegiatan == 'UTAMA' ? 'selected' : 'selected' }}>Pekerjaan Utama</option>
                                                <option value="TAMBAHAN" {{ isset($activity) && $activity->jenis_kegiatan == 'TAMBAHAN' ? 'selected' : '' }}>Pekerjaan Tambahan</option>
                                            </select>
                                        </div>
                                    @else
                                        <div class="form-group">
                                            <label for="jenis_kegiatan">Pekerjaan Utama/Tambahan</label>
                                            <select class="form-control" id="jenis_kegiatan" name="jenis_kegiatan" required>
                                                <option value="UTAMA" {{ isset($activity) && $activity->jenis_kegiatan == 'UTAMA' ? 'selected' : 'selected' }}>Pekerjaan Utama</option>
                                                <option value="TAMBAHAN" {{ isset($activity) && $activity->jenis_kegiatan == 'TAMBAHAN' ? 'selected' : '' }}>Pekerjaan Tambahan</option>
                                            </select>
                                        </div>
                                        <div class="form-group" id="tim_kerja_field">
                                            <label for="">Tim Kerja</label>
                                            <select class="form-control" id="tim_kerja_id" name="tim_kerja_id">
                                                <option value="" selected disabled>Pilih</option>
                                                @foreach ($TimKerja as $nama_tim_kerja => $tim_kerja_id)
                                                    <option value="{{ $tim_kerja_id }}" {{ isset($activity) && $activity->tim_kerja_id == $tim_kerja_id ? 'selected' : '' }}>{{ $nama_tim_kerja }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group" id="project_field">
                                            <label for="">Project</label>
                                            <select class="form-control" id="project" name="project_id">
                                                <option value="">Pilih Project</option>
                                                @if(isset($projects) && $isDuplicate)
                                                    @foreach ($projects as $project_id => $project_name)
                                                        <option value="{{ $project_id }}" {{ $activity->project_id == $project_id ? 'selected' : '' }}>{{ $project_name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="form-group" id="kegiatan_utama_field">
                                            <label for="">Kegiatan Utama</label>
                                            <select class="form-control" id="kegiatan_utama" name="kegiatan_utama_id">
                                                <option value="">Pilih Kegiatan Utama</option>
                                                @if(isset($kegiatanUtamas) && $isDuplicate)
                                                    @foreach ($kegiatanUtamas as $kegiatan_id => $kegiatan_name)
                                                        <option value="{{ $kegiatan_id }}" {{ $activity->kegiatan_utama_id == $kegiatan_id ? 'selected' : '' }}>{{ $kegiatan_name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    @endif

                                    <div class="form-group">
                                        <label for="kegiatan">Nama Kegiatan:</label>
                                        <input list="kegiatan-options" class="form-control" name="kegiatan"
                                            id="kegiatan" autocomplete="off" value="{{ isset($activity) ? $activity->kegiatan : '' }}" required />
                                        <datalist id="kegiatan-options">
                                            <!-- Options will be populated via JavaScript -->
                                        </datalist>
                                    </div>
                                    <div class="form-group" id="kuantitas" style="display: block;">
                                        <label for="kuantitas">Jumlah:</label>
                                        <input type="number" class="form-control" name="kuantitas" value="{{ isset($activity) ? $activity->kuantitas : '' }}" />
                                    </div>
                                    <div class="form-group" id="satuan" style="display: block;">
                                        <label for="satuan">Satuan:</label>
                                        <select class="form-control" name="satuan" id="satuanSelect" required>
                                            <option value="">-- Pilih Satuan --</option>
                                            <option value="Kegiatan" {{ isset($activity) && $activity->satuan == 'Kegiatan' ? 'selected' : 'selected' }}>Kegiatan</option>
                                            <option value="Hari" {{ isset($activity) && $activity->satuan == 'Hari' ? 'selected' : '' }}>Hari</option>
                                            <option value="Dokumen" {{ isset($activity) && $activity->satuan == 'Dokumen' ? 'selected' : '' }}>Dokumen</option>
                                            <option value="Laporan" {{ isset($activity) && $activity->satuan == 'Laporan' ? 'selected' : '' }}>Laporan</option>
                                            <option value="Publikasi" {{ isset($activity) && $activity->satuan == 'Publikasi' ? 'selected' : '' }}>Publikasi</option>
                                            <option value="Blok Sensus" {{ isset($activity) && $activity->satuan == 'Blok Sensus' ? 'selected' : '' }}>Blok Sensus</option>
                                            <option value="Paket" {{ isset($activity) && $activity->satuan == 'Paket' ? 'selected' : '' }}>Paket</option>
                                            <option value="Sampel" {{ isset($activity) && $activity->satuan == 'Sampel' ? 'selected' : '' }}>Sampel</option>
                                            <option value="Tabel" {{ isset($activity) && $activity->satuan == 'Tabel' ? 'selected' : '' }}>Tabel</option>
                                            <option value="File" {{ isset($activity) && $activity->satuan == 'File' ? 'selected' : '' }}>File</option>
                                            <option value="Daftar" {{ isset($activity) && $activity->satuan == 'Daftar' ? 'selected' : '' }}>Daftar</option>
                                            <option value="Responden" {{ isset($activity) && $activity->satuan == 'Responden' ? 'selected' : '' }}>Responden</option>
                                            <option value="Jam Pelatihan" {{ isset($activity) && $activity->satuan == 'Jam Pelatihan' ? 'selected' : '' }}>Jam Pelatiha (JP)</option>
                                            <option value="Transaksi" {{ isset($activity) && $activity->satuan == 'Transaksi' ? 'selected' : '' }}>Transaksi</option>
                                            <option value="Jam" {{ isset($activity) && $activity->satuan == 'Jam' ? 'selected' : '' }}>Jam</option>
                                            <option value="Petugas" {{ isset($activity) && $activity->satuan == 'Petugas' ? 'selected' : '' }}>Petugas</option>
                                            <option value="Kali" {{ isset($activity) && $activity->satuan == 'Kali' ? 'selected' : '' }}>Kali</option>
                                            <option value="E-Form" {{ isset($activity) && $activity->satuan == 'E-Form' ? 'selected' : '' }}>E-Form</option>
                                            <option value="Buku" {{ isset($activity) && $activity->satuan == 'Buku' ? 'selected' : '' }}>Buku</option>
                                            <option value="Konten" {{ isset($activity) && $activity->satuan == 'Konten' ? 'selected' : '' }}>Konten</option>
                                            <option value="Rumah Tangga" {{ isset($activity) && $activity->satuan == 'Rumah Tangga' ? 'selected' : '' }}>Rumah Tangga</option>
                                            <option value="Pertemuan" {{ isset($activity) && $activity->satuan == 'Pertemuan' ? 'selected' : '' }}>Pertemuan</option>
                                            <option value="Bab" {{ isset($activity) && $activity->satuan == 'Bab' ? 'selected' : '' }}>Bab</option>
                                            <option value="Blok" {{ isset($activity) && $activity->satuan == 'Blok' ? 'selected' : '' }}>Blok</option>
                                            <option value="Segmen" {{ isset($activity) && $activity->satuan == 'Segmen' ? 'selected' : '' }}>Segmen</option>
                                        </select>
                                    </div>


                                    <div class="form-group">
                                        <label for="keterangan_kegiatan">Keterangan Kegiatan:</label>
                                        <div id="keterangan_kegiatan" style="height: 200px;"></div>
                                    </div>

                                    <div class="form-group">
                                        <label for="is_done"><b>Status Penyelesaian</b></label>
                                        <select id="is_done" class="form-control" name="is_done">
                                            <option value="2" {{ isset($activity) && $activity->is_done == 2 ? 'selected' : (isset($activity) ? '' : 'selected') }}>Belum Selesai</option>
                                            <option value="1" {{ isset($activity) && $activity->is_done == 1 ? 'selected' : '' }}>Sudah Selesai</option>
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

        // Prefill keterangan kegiatan jika mode duplicate
        @if(isset($activity) && $isDuplicate)
            setTimeout(function() {
                var keterangan = `{!! $activity->keterangan ?? '' !!}`;
                quill.root.innerHTML = keterangan;
            }, 100);
        @endif
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
                        url: '{{ url('temp/getProject') }}/' + tim_kerja_id,
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
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
                            console.error("Error:", xhr.responseText);
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
                            $('#kegiatan_utama').empty().append(
                                '<option value="" selected disabled>Pilih Kegiatan Utama</option>');
                            if ($.isEmptyObject(data)) {
                                alert('Tidak ada kegiatan_utama untuk Project yang dipilih.');
                            } else {
                                $.each(data, function(key, value) {
                                    $('#kegiatan_utama').append('<option value="' +
                                        key + '">' + value + '</option>');
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error:", xhr.responseText);
                            alert('Gagal mengambil data. Silakan coba lagi.');
                        }
                    });
                } else {
                    $('#kegiatan_utama').empty().append(
                        '<option value="" selected disabled>Pilih Kegiatan Utama</option>');
                }
            });
        });
    </script>

@endsection
