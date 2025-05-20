@extends('layouts.template')

@section('content')

<div class="page-header row no-gutters py-4">
    <div class="col-12 col-sm-4 text-center text-sm-left mb-0">
        <span class="text-uppercase page-subtitle">KHI</span>
        <h3 class="page-title">Edit Notifikasi</h3>
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
                           <form action="{{ route('notif.update', $notification->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="form-group">
                                    <label for="judul">Judul Notifikasi</label>
                                    <input type="text" name="judul" class="form-control form-control-lg mb-3" value="{{ $notification->title }}">
                                </div>
                                <div class="form-group">
                                    <label for="deskripsi">Deskripsi Notifikasi</label>
                                    <input type="text" name="deskripsi" class="form-control form-control-lg mb-3" value="{{ $notification->description }}">
                                </div>
                                <div class="form-group">
                                    <label for="tipe">Tipe Notifikasi</label>
                                    <input type="text" name="tipe" class="form-control form-control-lg mb-3" value="{{ $notification->type }}">
                                </div>
                                <div class="form-group">
                                    <label for="start_date">Tanggal Mulai</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $notification->start_date }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="end_date">Tanggal Berakhir</label>
                                    <input type="date" name="end_date" id="end_date" value="{{ $notification->end_date }}" class="form-control" required>
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
