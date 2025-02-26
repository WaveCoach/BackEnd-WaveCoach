@extends('layouts.default')

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Edit Coach</h5>
        <p class="card-description">Halaman ini memungkinkan admin untuk melakukan perubahan pada coach</p>
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <form method="POST" action="{{ route('coach.update', $coach->id) }}" id="jobPositionForm">
            @csrf
            @method('PUT')
            <div class="row mb-4">
                <div class="col-6 mb-3">
                    <label for="name" class="form-label">Nama</label>
                    <input type="text" class="form-control" required value="{{ $coach->name }}" name="name" id="name">
                </div>
                <div class="col-6 mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="text" class="form-control" required value="{{ $coach->email }}" name="email" id="email">
                </div>
                <div class="col-6 mb-3">
                    <label for="specialization" class="form-label">Role</label>
                    <select class="form-select" required name="role_id" id="specialization">
                        <option value="" disabled>Pilih Role</option>
                        <option value="2" {{ $coach->role_id == 2 ? 'selected' : '' }}>Coach</option>
                        <option value="3" {{ $coach->role_id == 3 ? 'selected' : '' }}>Master Coach</option>
                    </select>
                </div>
                <div class="col-6 mb-3">
                    <label for="specialization" class="form-label">Status</label>
                    <select class="form-select" required name="status" id="specialization">
                        <option value="" disabled>Pilih Role</option>
                        <option value="active" {{ $coach->coach->status == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $coach->coach->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
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
