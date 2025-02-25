@extends('layouts.default')

@section('content')
<div class="card">
    <div class="card-body">
        <form method="" action="" id="jobPositionForm">
            <div class="row mb-4">
                <div class="col-6 mb-3">
                    <label for="name" class="form-label">Nama </label>
                    <input type="text" class="form-control" disabled value="{{$coach->name}}" name="name" id="name">
                </div>
                <div class="col-6 mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="text" class="form-control" disabled value="{{$coach->email}}" name="email" id="email">
                </div>
                <div class="col-6 mb-3">
                    <label for="specialization" class="form-label">Role</label>
                    <select class="form-select" disabled required name="role_id" id="specialization">
                        <option value="" disabled>Pilih Role</option>
                        <option value="2" {{ $coach->role_id == 2 ? 'selected' : '' }}>Coach</option>
                        <option value="3" {{ $coach->role_id == 3 ? 'selected' : '' }}>Master Coach</option>
                    </select>
                </div>
            </div>
            <a href="{{route('coach.index')}}" class="btn btn-warning">Kembali</a>
        </form>
    </div>
</div>
@endsection

@push('custom-style')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.0/classic/ckeditor.js"></script>
@endpush

