@extends('layouts.default')

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Tambah Lokasi Baru</h5>
        <p class="card-description">Halaman ini memungkinkan admin untuk menambahkan Lokasi Latihan Renang baru</p>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('location.store') }}" id="jobPositionForm">
            @csrf
            <div class="row mb-4">
                <div class="col-6 mb-3">
                    <label for="title" class="form-label">Nama Lokasi</label>
                    <input type="text" class="form-control"  name="name" >
                </div>
                <div class="col-6 mb-3">
                    <label for="location" class="form-label">Alamat</label>
                    <input type="text" class="form-control"  name="address" >
                </div>
                <div class="col-6 mb-3">
                    <label for="location" class="form-label">Link Gmaps</label>
                    <input type="text" class="form-control"  name="maps" >
                </div>

            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>
@endsection

@push('custom-style')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.0/classic/ckeditor.js"></script>
@endpush

