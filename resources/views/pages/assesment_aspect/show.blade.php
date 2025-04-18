@extends('layouts.default')

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Detail Aspek Penilaian</h5>
        <p class="card-description">Halaman ini hanya menampilkan data Aspek Penilaian</p>

        <div class="row mb-4">
            <!-- Kategori -->
            <div class="col-6 mb-3">
                <label class="form-label">Kategori</label>
                <select class="form-select" disabled>
                    <option>{{ $categories->name }}</option>
                </select>
            </div>

            <!-- KKM -->
            <div class="col-6 mb-3">
                <label for="kkm" class="form-label">Nilai KKM</label>
                <input type="number" class="form-control" value="{{ $categories->kkm }}" readonly>
            </div>
        </div>

        <!-- Kontainer Aspek dan Deskripsi -->
        <div id="aspek-container">
            @forelse ($categories->aspects as $item)
                <div class="row aspek-group mb-3">
                    <div class="col-md-6">
                        <label>Aspek Penilaian</label>
                        <input type="text" class="form-control" value="{{ $item->name }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label>Deskripsi</label>
                        <input type="text" class="form-control" value="{{ $item->desc }}" readonly>
                    </div>
                </div>
            @empty
                <p class="text-muted">Belum ada aspek.</p>
            @endforelse
        </div>

        <a href="{{ route('assesment-aspect.index') }}" class="btn btn-warning mt-3">Kembali</a>
    </div>
</div>
@endsection
