<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $record->title ?? 'Link Publik' }}</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
</head>
<body>
<div class="container py-4">
    <h4>{{ $record->title ?? 'Link Publik' }}</h4>
    <p>Ditampilkan publik. Tidak perlu login.</p>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Jenis Kegiatan</th>
                <th>Nama Kegiatan</th>
                <th>Progres</th>
                <th>Bukti Dukung</th>
            </tr>
        </thead>
        <tbody>
            @php $n = 1; @endphp
            @foreach($activities as $act)
            <tr>
                <td>{{ $n++ }}</td>
                <td>{{ \Carbon\Carbon::parse($act->tgl)->format('d-M-Y') }}</td>
                <td>{{ $act->jenis_kegiatan }}</td>
                <td>{{ $act->kegiatan }}</td>
                <td>@if($act->is_done == 2) <span class="badge badge-warning">Selesai?</span> @else <span class="badge badge-success">Selesai</span> @endif</td>
                <td>
                    @if ($act->berkas == NULL AND $act->link == NULL )
                        <strong class="text-danger">Tidak ada!</strong>
                    @else
                        @if(!empty($act->link))
                            <div><a href="{{ $act->link }}" target="_blank" rel="noopener">Buka Link</a></div>
                        @endif
                        @if(!empty($act->berkas))
                            <div><a href="{{ asset('bukti/' . $act->berkas) }}" target="_blank" rel="noopener">Download Berkas</a></div>
                        @endif
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
</body>
</html>
