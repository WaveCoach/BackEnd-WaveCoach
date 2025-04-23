@extends('layouts.default')

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Tambah Package Baru</h5>
        <p class="card-description">Halaman ini memungkinkan admin untuk menambahkan package baru</p>
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
        <form method="POST" action="{{ route('package.store') }}">
            @csrf
            <div class="row mb-4">
                <!-- Input Name -->
                <div class="col-6 mb-3">
                    <label for="name" class="form-label">Nama Package</label>
                    <input type="text" class="form-control"  name="name" value="{{ old('name') }}">
                </div>

                <!-- Input Deskripsi -->
                <div class="col-6 mb-3">
                    <label for="desc" class="form-label">Deskripsi</label>
                    <textarea class="form-control" name="desc" rows="3">{{ old('desc') }}</textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>
@endsection
