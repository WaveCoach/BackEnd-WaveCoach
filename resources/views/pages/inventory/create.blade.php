@extends('layouts.default')

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Tambah Inventory Baru</h5>
        <p class="card-description">Halaman ini memungkinkan admin untuk menambahkan inventory baru</p>
        <form method="POST" action="{{ route('inventory.store') }}" id="jobPositionForm">
            @csrf
            <div class="row mb-4">
                <div class="col-6 mb-3">
                    <label for="assesment_categories_id" class="form-label">Inventaris</label>
                    <select class="select2" required name="inventory_id" id="myselect">
                        <option value="" disabled selected>Pilih Inventaris</option>
                        @foreach($inventorys as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 mb-3">
                    <label for="qty" class="form-label">Qty</label>
                    <input type="text" class="form-control" required name="qty" id="qty">
                </div>
                <div class="col-6 mb-3">
                    <label for="mastercoach_id" class="form-label">Mastercoach</label>
                    <select class="select2" required name="mastercoach_id" id="myselect2">
                        <option value="" disabled selected>Pilih Mastercoach</option>
                        @foreach($mastercoaches as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 d-flex gap-2" id="extraInputs"></div>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
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
        $(document).ready(function() {
            $('.select2').select2({
                width: '100%',
                placeholder: "Pilih atau Tambah Opsi",
                allowClear: true,
                tags: true,
                createTag: function(params) {
                    var term = $.trim(params.term);
                    return term === '' ? null : { id: term, text: term, newTag: true };
                }
            });

            $('#myselect2').on('select2:select', function(e) {
                var data = e.params.data;
                if (data.newTag) {
                    var newInput = `
                        <div class="col-6 mb-3">
                            <label for="mastercoach_id" class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" placeholder="Input tambahan">
                        </div>
                    `;
                    $('#extraInputs').append(newInput);
                }
            });
        });
    </script>
@endpush
