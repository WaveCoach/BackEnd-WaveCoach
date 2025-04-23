@extends('layouts.default')

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Edit Aspek Penilaian</h5>
        <p class="card-description">Halaman ini memungkinkan admin untuk mengedit Aspek Penilaian berdasarkan kategori</p>

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('assesmentaspect.update', $category->id) }}">
            @csrf
            @method('PUT')

            <div class="row mb-4">
                <div class="col-4 mb-3">
                    <label for="assesment_categories_id" class="form-label">Kategori</label>
                    <input type="text" class="form-control" name="category_name" value="{{ $category->name }}" >
                    <input type="text" name="assessment_categories_id" class="form-control" value="{{ $category->id }}" hidden>
                </div>

                <div class="col-4 mb-3">
                    <label for="kkm" class="form-label">Nilai KKM</label>
                    <input type="number" name="kkm" class="form-control" value="{{ $category->kkm }}" required>
                </div>

                <div class="col-4 mb-3">
                    <label for="package_id" class="form-label">Package</label>
                    <select class="select2 form-control" name="package_id[]" id="package-select" multiple required>
                        @foreach($packages as $package)
                            <option value="{{ $package->id }}" {{ in_array($package->id, $selectedPackages) ? 'selected' : '' }}>
                                {{ $package->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div id="aspek-container">
                @foreach ($aspects as $index => $aspect)
                <div class="row aspek-group mb-3">
                    <div class="col-md-5">
                        <label>Aspek Penilaian</label>
                        <input type="text" class="form-control" name="name[]" value="{{ $aspect->name }}" required>
                    </div>
                    <div class="col-md-5">
                        <label>Deskripsi</label>
                        <input type="text" class="form-control" name="description[]" value="{{ $aspect->desc }}" required>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        @if ($loop->first)
                            <button type="button" class="btn btn-success add-aspek w-100"><strong>+</strong></button>
                        @else
                            <button type="button" class="btn btn-danger remove-aspek w-100"><strong>-</strong></button>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            <button type="submit" class="btn btn-primary mt-3">Update</button>
        </form>
    </div>
</div>
@endsection

@push('custom-style')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
@endpush

@push('custom-scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#package-select').select2({ width: '100%', placeholder: "Pilih Package" });

            $('#aspek-container').on('click', '.add-aspek', function () {
                let newRow = `
                    <div class="row aspek-group mb-3">
                        <div class="col-md-5">
                            <input type="text" class="form-control" name="name[]" placeholder="Aspek Penilaian" required>
                        </div>
                        <div class="col-md-5">
                            <input type="text" class="form-control" name="description[]" placeholder="Deskripsi" required>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-danger remove-aspek w-100"><strong>-</strong></button>
                        </div>
                    </div>
                `;
                $('#aspek-container').append(newRow);
            });

            $('#aspek-container').on('click', '.remove-aspek', function () {
                $(this).closest('.aspek-group').remove();
            });
        });
    </script>
@endpush
