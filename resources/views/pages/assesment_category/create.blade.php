@extends('layouts.default')

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Tambah assesment-category Baru</h5>
        <p class="card-description">Halaman ini memungkinkan admin untuk menambahkan assesment-category baru</p>
        <form method="POST" action="{{ route('assesment-category.store') }}" id="jobPositionForm">
            @csrf
            <div class="row mb-4">
                <div class="col-12 mb-3">
                    <label for="name" class="form-label">Nama </label>
                    <input type="text" class="form-control" required name="name" id="name">
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

