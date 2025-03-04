@extends('layouts.default')

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Tambah Student Baru</h5>
        <p class="card-description">Halaman ini memungkinkan admin untuk menambahkan student baru</p>
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <form method="POST" action="{{ route('student.store') }}" id="jobPositionForm">
            @csrf
            <div class="row mb-4">
                <div class="col-6 mb-3">
                    <label for="name" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" name="name" id="name">
                </div>
                <div class="col-6 mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" id="email">
                </div>
                <div class="col-6 mb-3">
                    <label for="usia" class="form-label">Tanggal Lahir</label>
                    <input type="date" class="form-control" name="tanggal_lahir" id="usia">
                </div>
                {{-- <div class="col-6 mb-3">
                    <label for="type" class="form-label">Type</label>
                    <input type="text" class="form-control" name="type" id="type">
                </div> --}}
                <!-- Tambahan Radio Button untuk Jenis Kelamin -->
                <div class="col-6 mb-3">
                    <label class="form-label">Jenis Kelamin</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="jenis_kelamin" id="laki-laki" value="L">
                            <label class="form-check-label" for="laki-laki">Laki-laki</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="jenis_kelamin" id="perempuan" value="P">
                            <label class="form-check-label" for="perempuan">Perempuan</label>
                        </div>
                    </div>
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
