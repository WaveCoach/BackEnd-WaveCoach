@extends('layouts.default')

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Tambah Aspek Penilaian Baruuuuuuuu</h5>
        <p class="card-description">Halaman ini memungkinkan admin untuk menambahkan Aspek Penilaian baru</p>
        <form method="POST" action="{{ route('assesment-aspect.store') }}" id="jobPositionForm">
            @csrf
            <div class="row mb-4">
                <!-- Select2 untuk Kategori -->
                <div class="col-6 mb-3">
                    <label for="assesment_categories_id" class="form-label">Kategori</label>
                    <select class="select2" required name="assesment_categories_id" id="myselect">
                        <option value="" disabled selected>Pilih Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Input Aspek Penilaian -->
                <div class="col-6 mb-3">
                    <label for="name" class="form-label">Aspek Penilaian</label>
                    <input type="text" class="form-control" required name="name[]" id="name">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>
@endsection

@push('custom-style')
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
    <style>
        /* Supaya Select2 tidak kepotong dalam card */
        .select2-container {
            width: 100% !important;
        }
    </style>
@endpush

@push('custom-scripts')
    <!-- Load jQuery (Hanya jika belum ada di layout) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Load Select2 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>

    <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">


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

    </script>

    <script>
       document.addEventListener("DOMContentLoaded", function () {
    var input = document.querySelector("#name");
    var hiddenInput = document.querySelector("#name-hidden");

    var tagify = new Tagify(input, {
        delimiters: ",",
        maxTags: 10,
        trim: true,
        dropdown: {
            enabled: 0
        }
    });

    // Simpan hanya teks tanpa format JSON
    tagify.on('change', function(e) {
        var values = tagify.value.map(tag => tag.value);
        hiddenInput.value = JSON.stringify(values); // Simpan sebagai array string
    });
});

    </script>

@endpush
