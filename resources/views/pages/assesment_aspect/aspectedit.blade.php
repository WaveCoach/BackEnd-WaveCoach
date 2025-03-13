@extends('layouts.default')

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Edit Aspek Penilaian</h5>
        <p class="card-description">Halaman ini memungkinkan admin untuk mengedit Aspek Penilaian</p>
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <form method="POST" action="{{ route('assesmentaspect.update', $selectedCategory->id) }}" id="jobPositionForm">
            @csrf
            @method('PUT')

            <div class="row mb-4">
                <!-- Select2 untuk Kategori -->
                <div class="col-6 mb-3">
                    <label for="assessment_categories_id" class="form-label">Kategori</label>
                    <select class="select2" required name="assessment_categories_id" id="myselect">
                        <option value="" disabled>Pilih Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $selectedCategory->id == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Input Aspek Penilaian dengan Tagify -->
                <div class="col-6 mb-3">
                    <label for="name" class="form-label">Aspek Penilaian</label>
                    <input type="text" class="form-control"  name="name[]" id="name" value="{{ implode(',', $aspects->pluck('name')->toArray()) }}">
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </form>
    </div>
</div>
@endsection

@push('custom-style')
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
    <style>
        .select2-container {
            width: 100% !important;
        }
    </style>
@endpush

@push('custom-scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>

    <script>
        $(document).ready(function() {
            $('#myselect').select2({
                width: '100%',
                placeholder: "Pilih atau Tambah Opsi",
                allowClear: true,
                tags: true,
                createTag: function(params) {
                    var term = $.trim(params.term);
                    if (term === '') {
                        return null;
                    }
                    return {
                        id: term,
                        text: term,
                        newTag: true
                    };
                }
            });
        });

        document.addEventListener("DOMContentLoaded", function () {
            var input = document.querySelector("#name");
            var tagify = new Tagify(input, {
                delimiters: ",",
                maxTags: 10,
                trim: true,
                dropdown: { enabled: 0 }
            });
        });
    </script>
@endpush
