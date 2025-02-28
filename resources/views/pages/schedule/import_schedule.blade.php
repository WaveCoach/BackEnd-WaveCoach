@extends('layouts.default')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Import Schedule</h5>
            <p class="card-description">Silakan unggah file jadwal yang ingin diimport.</p>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('import.schedule') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="file" class="form-label">Pilih File</label>
                    <input type="file" class="form-control" name="file" required>
                </div>
                <button type="submit" class="btn btn-primary">Import</button>
            </form>
        </div>
    </div>
@endsection

@push('custom-style')
    {{-- <style>
        .card {
            max-width: 500px;
            margin: auto;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-radius: 10px;
        }
        .btn-primary {
            width: 100%;
        }
    </style> --}}
@endpush
