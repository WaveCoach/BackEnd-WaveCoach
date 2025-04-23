@extends('layouts.default')

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Tambah Aspek Penilaian Baru</h5>
        <p class="card-description">Halaman ini memungkinkan admin untuk menambahkan Aspek Penilaian baru</p>

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('assesment-aspect.store') }}" id="jobPositionForm">
            @csrf

            <div class="row mb-4">
                <!-- Select2 untuk Kategori -->
                <div class="col-4 mb-3">
                    <label for="assesment_categories_id" class="form-label">Kategori</label>
                    <select class="select2" name="assessment_categories_id" id="myselect">
                        <option value="" disabled selected>Pilih Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Input KKM (muncul kalau kategori baru) -->
                <div id="kkm-input-container" class="col-4 mb-3 d-none">
                    <label for="kkm" class="form-label">Nilai KKM</label>
                    <input type="number" name="kkm" id="kkm" class="form-control" placeholder="Masukkan Nilai KKM">
                </div>

               <!-- Input Package -->
                <div class="col-4 mb-3">
                    <label for="package_id" class="form-label">Package</label>
                    <select class="select2 form-control" name="package_id[]" id="package-select" multiple required>
                        @foreach($packages as $package)
                            <option value="{{ $package->id }}">{{ $package->name }}</option>
                        @endforeach
                    </select>
                </div>

            </div>

            <!-- Kontainer Dinamis untuk Aspek + Deskripsi -->
            <div id="aspek-container">
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
                        <button type="button" class="btn btn-success add-aspek w-100"><strong>+</strong></button>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Submit</button>
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
    <!-- jQuery + Select2 -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script>
        $(document).ready(function () {
            let $select = $('#myselect');
            let $kkmContainer = $('#kkm-input-container');

            $select.select2({
                width: '100%',
                placeholder: "Pilih atau Tambah Opsi",
                allowClear: true,
                tags: true,
                createTag: function (params) {
                    var term = $.trim(params.term);
                    if (term === '') return null;

                    return {
                        id: term,
                        text: term,
                        newOption: true
                    };
                }
            });

            $select.on('select2:select', function (e) {
                let isNew = e.params.data.newOption;

                if (isNew) {
                    $kkmContainer.removeClass('d-none');
                    $('#kkm').attr('required', true);
                } else {
                    $kkmContainer.addClass('d-none');
                    $('#kkm').removeAttr('required');
                }
            });

            // ✅ Select2 untuk multi-select Package
            $('#package-select').select2({
                width: '100%',
                placeholder: "Pilih Package",
                allowClear: true
            });

            // Tambah Aspek Penilaian
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

            // Hapus Aspek Penilaian
            $('#aspek-container').on('click', '.remove-aspek', function () {
                $(this).closest('.aspek-group').remove();
            });
        });
    </script>
@endpush
