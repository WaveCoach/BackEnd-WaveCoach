@extends('layouts.default')

@section('content')
<div class="card">
    <div class="card-body">
        <form id="jobPositionForm">
            <div class="row mb-4">
                <div class="col-6 mb-3">
                    <label for="name" class="form-label">Nama Kategori</label>
                    <input type="text" class="form-control" disabled value="{{ $category->name }}" name="name" id="name">
                </div>
                <div class="col-6 mb-3">
                    <label for="aspects" class="form-label">Aspek</label>
                    @if ($category->aspects->isEmpty())
                        <p class="text-muted">Tidak ada aspek</p>
                    @else
                        <ul class="list-group">
                            @foreach ($category->aspects as $aspect)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $aspect->name }}
                                    <div>
                                        <a href="{{ route('assesment-aspect.edit', $aspect->id) }}" class="btn btn-warning btn-sm me-2">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        {{-- <form action="{{ route('assesment-aspect.destroy', $aspect->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form> --}}
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
            <a href="{{ route('assesment-aspect.index') }}" class="btn btn-warning">Kembali</a>
        </form>
    </div>
</div>
@endsection

@push('custom-style')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.0/classic/ckeditor.js"></script>
@endpush
