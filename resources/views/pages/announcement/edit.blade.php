@extends('layouts.default')

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Edit Pengumuman</h5>
        <form action="{{ route('announcement.update', $announcement->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT') <!-- Menandakan bahwa ini adalah request untuk update -->
            <div class="mb-3">
                <label for="title" class="form-label">Judul</label>
                <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $announcement->title) }}" required>
            </div>
            <div class="mb-3">
                <label for="content" class="form-label">Konten</label>
                <textarea class="form-control" id="content" name="content" rows="4" required>{{ old('content', $announcement->content) }}</textarea>
            </div>
            <div class="mb-3">
                <label for="published_at" class="form-label">Tanggal Publikasi</label>
                <input type="datetime-local" class="form-control" id="published_at" name="published_at" value="{{ old('published_at', \Carbon\Carbon::parse($announcement->published_at)->format('Y-m-d\TH:i')) }}">
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Gambar</label>
                <input type="file" class="form-control" id="image" name="image">
                @if($announcement->image)
                    <div class="mt-2">
                        <img src="{{ asset('storage/' . $announcement->image) }}" alt="Image" width="150">
                    </div>
                @endif
            </div>
            <div class="mb-3">
                <label for="student_id" class="form-label">Ditunjukkan Kepada</label>
                <select class="select2 form-control" required name="student_id[]" id="student-select" multiple>
                    @foreach ($users as $student)
                        <option value="{{ $student->id }}"
                            @if($announcement->users->contains($student->id)) selected @endif>
                            {{ $student->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
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
