@extends('layouts.default')

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Tambah Pengumuman</h5>
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <form action="{{ route('announcement.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="title" class="form-label">Judul</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="mb-3">
                <label for="content" class="form-label">Konten</label>
                <textarea class="form-control" id="content" name="content" rows="4" required></textarea>
            </div>
            <div class="mb-3">
                <label for="published_at" class="form-label">Tanggal Publikasi</label>
                <input type="datetime-local" class="form-control" id="published_at" name="published_at">
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Gambar</label>
                <input type="file" class="form-control" id="image" name="image">
            </div>
            <div class="mb-3">
                <label for="student_id" class="form-label">Kepada</label>
                <select class="select2 form-control" required name="user_id[]" id="student-select" multiple>
                    <option value="semua">Semua Orang</option>
                    <option value="mastercoach">Semua Mastercoach</option>
                    <option value="coach">Semua Coach</option>
                    @foreach ($users as $student)
                        <option value="{{ $student->id }}">
                            {{ $student->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>
@endsection

@push('custom-style')
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
@endpush

@push('custom-scripts')
    <!-- Load jQuery (Only if not already in the layout) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Load Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#student-select').select2();  // Initialize the Select2 plugin
        });
    </script>
@endpush
