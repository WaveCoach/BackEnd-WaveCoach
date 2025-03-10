@extends('layouts.default')

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Edit mastercoach</h5>
        <p class="card-description">Halaman ini memungkinkan admin untuk melakukan perubahan pada mastercoach</p>
        <form method="POST" action="{{ route('mastercoach.update', $mastercoach -> id) }}" id="jobPositionForm">
            @csrf
            @method('PUT')
            <div class="row mb-4">
                <div class="col-6 mb-3">
                    <label for="name" class="form-label">Nama </label>
                    <input type="text" class="form-control" required value="{{$mastercoach->name}}" name="name" id="name">
                </div>
                <div class="col-6 mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="text" class="form-control" required value="{{$mastercoach->email}}" name="email" id="email">
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

