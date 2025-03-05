@extends('layouts.default')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Import Location</h5>
            <p class="card-description">Silakan unggah file location yang ingin diimport <br>
                contoh format file : <a href="{{ asset('assets/templates/location_format.xlsx') }}" class="">Download Format</a>.</p>


            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('import.location') }}" method="POST" enctype="multipart/form-data">
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

