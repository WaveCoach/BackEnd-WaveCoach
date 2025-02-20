@extends('layouts.default')

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Tambah admin Baru</h5>
        <p class="card-description">Halaman ini memungkinkan admin untuk menambahkan admin baru</p>
        {{-- {{ dd(route('admin.store')) }} --}}

        <form method="POST" action="{{ route('admin.store') }}" id="jobPositionForm">
            @csrf
            <div class="row mb-4">
                <div class="col-4 mb-3">
                    <label for="name" class="form-label">Nama </label>
                    <input type="text" class="form-control" required name="name" id="name">
                </div>
                <div class="col-4 mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="text" class="form-control" required name="email" id="email">
                </div>
                <div class="col-4 mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" required name="password" id="password">
                </div>
                <div class="col-4 mb-3">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                    <input type="password" class="form-control" required name="password_confirmation" id="password_confirmation">
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

