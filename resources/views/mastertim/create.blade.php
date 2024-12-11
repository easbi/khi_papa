@extends('layouts.template')

@section('content')

<div class="page-header row no-gutters py-4">
    <div class="col-12 col-sm-4 text-center text-sm-left mb-0">
        <span class="text-uppercase page-subtitle">KHI</span>
        <h3 class="page-title">Entri Tim</h3>
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
                            <form action="{{ route('timkerja.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label for="nama_tim_kerja">Nama Tim Kerja</label>
                                    <input type="text" name="nama_tim_kerja" class="form-control form-control-lg mb-3">
                                </div>
                                <div class="form-group">
                                    <label for="">Ketua Tim Kerja</label>                                    
                                    <select class="form-control" id="nip_ketua_tim" name="nip_ketua_tim" required>
                                        <option value="" selected disabled>Pilih</option>
                                        @foreach($candidate as $item)
                                            <option value="{{ $item->nip }}">{{ $item->fullname }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="tahun_kerja">Tahun Kerja:</label>
                                    <input type="number" class="form-control" name="tahun_kerja" min="2023" max="2027" placeholder="Masukkan Tahun" value="{{ old('tahun_kerja') }}" required />
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

@endsection
