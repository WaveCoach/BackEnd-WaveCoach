@extends('layouts.default')

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Edit Aspek Penilaian</h5>
        <p class="card-description">Halaman ini memungkinkan admin untuk melakukan perubahan pada Aspek Penilaian</p>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('assesmentaspect.update', $categories->id) }}" id="jobPositionForm">
            @csrf
            @method('PUT')

            <div class="row mb-4">
                <!-- Kategori -->
                <div class="col-6 mb-3">
                    <label class="form-label">Kategori</label>
                    <select name="assessment_categories_id" class="form-select" required>
                        <option value="" disabled selected>Pilih Kategori</option>
                        @foreach ($allCategories as $category)
                            <option value="{{ $category->id }}"
                                {{ $category->id == $categories->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- KKM -->
                <div class="col-6 mb-3">
                    <label for="kkm" class="form-label">Nilai KKM</label>
                    <input type="number" name="kkm" id="kkm" class="form-control"
                        placeholder="Masukkan Nilai KKM" value="{{ old('kkm', $categories->kkm) }}" required>
                </div>
            </div>

            <!-- Kontainer Aspek dan Deskripsi -->
            <div id="aspek-container">
                @forelse ($categories->aspects as $item)
                    <div class="row aspek-group mb-3">
                        <div class="col-md-5">
                            <label>Aspek Penilaian</label>
                            <input type="text" class="form-control" name="name[]" value="{{ $item->name }}" required>
                        </div>
                        <div class="col-md-5">
                            <label>Deskripsi</label>
                            <input type="text" class="form-control" name="description[]" value="{{ $item->desc }}" required>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-danger remove-aspek w-100"><strong>-</strong></button>
                        </div>
                    </div>
                @empty
                    <p class="text-muted">Belum ada aspek. Tambahkan aspek baru.</p>
                @endforelse
            </div>

            <!-- Tombol Tambah -->
            <div class="text-end mb-3">
                <button type="button" class="btn btn-success" id="add-aspek"><strong>+</strong> Tambah Aspek</button>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Update</button>
        </form>
    </div>
</div>
@endsection

@push('custom-style')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
    <style>
        .select2-container {
            width: 100% !important;
        }
    </style>
@endpush

@push('custom-scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script>
        $(document).ready(function () {
            // Hapus baris aspek
            $('#aspek-container').on('click', '.remove-aspek', function () {
                $(this).closest('.aspek-group').remove();
            });

            // Tambah baris aspek baru
            $('#add-aspek').on('click', function () {
                const newGroup = `
                    <div class="row aspek-group mb-3">
                        <div class="col-md-5">
                            <label>Aspek Penilaian</label>
                            <input type="text" class="form-control" name="name[]" required>
                        </div>
                        <div class="col-md-5">
                            <label>Deskripsi</label>
                            <input type="text" class="form-control" name="description[]" required>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-danger remove-aspek w-100"><strong>-</strong></button>
                        </div>
                    </div>`;
                $('#aspek-container').append(newGroup);
            });
        });
    </script>
@endpush
