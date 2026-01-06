@extends('layouts.template')
@section('content')

<!-- Page Header -->
<div class="page-header row no-gutters py-4">
    <div class="col-12 col-sm-4 text-center text-sm-left mb-0">
        <span class="text-uppercase page-subtitle">Project</span>
        <h3 class="page-title">BPS Kota Padang Panjang</h3>
    </div>
</div>
<!-- End Page Header -->

<div class="row mb-4 align-items-center">
    <div class="col-8 col-sm-6">
        @php $currentYear = date('Y'); @endphp
        <form method="GET" action="{{ route('project.index') }}" class="form-inline">
            <div class="input-group">
                <select name="tahun" class="form-control form-control-sm">
                    @for($y = $currentYear - 5; $y <= $currentYear; $y++)
                        <option value="{{ $y }}" {{ request('tahun', $currentYear) == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <div class="input-group-append">
                    <button class="btn btn-secondary btn-sm" type="submit">Filter</button>
                    <a href="{{ route('project.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                </div>
            </div>
        </form>
    </div>
    <div class="col-4 col-sm-6 text-right">
        <a href="{{ route('project.create') }}" class="btn btn-primary btn-sm">
            Tambahkan Project
        </a>
    </div>
</div>

<!-- Default Light Table -->
<div class="row">
    <div class="col">
        <div class="card card-small mb-4">
            <div class="card-header border-bottom">
                <h6 class="m-0">Tabel Daftar Project Tim Kerja</h6>
            </div>
            @if ($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <i class="fa fa-check mx-2"></i>
                <strong>Success!</strong> {{ $message }}
            </div>
            @endif
            <div class="card-body d-flex flex-column">
                <table id="example" class="display responsive nowrap" width="100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Ketua Tim Kerja</th>
                            <th>Nama Tim Kerja</th>
                            <th>Project</th>
                            <th>Tahun Kerja</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($project as $tk)
                        <tr>
                            <td>{{ ++$i }}</td>
                            <td>{{ $tk->nama_ketua_tim }}</td>
                            <td>{{ $tk->nama_tim_kerja }}</td>
                            <td>{{ $tk->nama_project }}</td>
                            <td>{{ $tk->tahun_kerja }}</td>
                            <td>
                                <form action="{{ route('project.destroy',$tk->id) }}" method="POST">
                                    @if (Auth::user()->id == 1 || Auth::user()->id == 17 || Auth::user()->id == 20 ||Auth::user()->nip == $tk->nip_ketua_tim)
                                    <a class="btn btn-primary btn-sm" href="{{ route('project.edit',$tk->id) }}">Edit</a>
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Delete</button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- End Default Light Table -->

@endsection

@push('scripts')
<script type="text/javascript">
$(document).ready(function() {
    // DataTable setup
    $('#example').DataTable({
        scrollX: true,
        responsive: true,
        searching: true,
        ordering: true,
        autoWidth: false
    });
});
</script>
@endpush
