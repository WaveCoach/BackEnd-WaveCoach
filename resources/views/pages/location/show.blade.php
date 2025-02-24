@extends('layouts.default')

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Lihat Lokasi Baru</h5>
        <p class="card-description">Halaman ini memungkinkan admin untuk melihat detail Lokasi Renang Baru</p>
        <form method="POST" action="" id="jobPositionForm">
            @csrf
            <div class="row mb-4">
                <div class="col-6 mb-3">
                    <label for="title" class="form-label">Nama </label>
                    <input type="text" class="form-control" value="{{$location->name}}" disabled required name="name" id="title">
                </div>
                <div class="col-6 mb-3">
                    <label for="location" class="form-label">Alamat</label>
                    <input type="text" class="form-control" value="{{$location->address}}" disabled required name="address" id="title">
                </div>
                <div class="col-6 mb-3">
                    <label for="location" class="form-label">Link Gmaps</label>
                    <input type="text" class="form-control" disabled value="{{$location->maps}}" required name="address" id="title">
                </div>

            </div>
            <a href="{{route('location.index')}}" class="btn btn-warning">Kembali</a>
        </form>
    </div>
</div>
@endsection

@push('custom-style')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.0/classic/ckeditor.js"></script>
@endpush

