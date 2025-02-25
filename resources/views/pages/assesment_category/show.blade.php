@extends('layouts.default')

@section('content')
<div class="card">
    <div class="card-body">
        <form method="" action="" id="jobPositionForm">
            <div class="row mb-4">
                <div class="col-6 mb-3">
                    <label for="name" class="form-label">Nama </label>
                    <input type="text" class="form-control" disabled value="{{$category->name}}" name="name" id="name">
                </div>


            </div>
            <a href="{{route('assesment-category.index')}}" class="btn btn-warning">Kembali</a>
        </form>
    </div>
</div>
@endsection

@push('custom-style')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.0/classic/ckeditor.js"></script>
@endpush

