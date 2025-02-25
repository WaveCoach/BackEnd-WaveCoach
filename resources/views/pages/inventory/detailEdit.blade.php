@extends('layouts.default')

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Edit Inventory</h5>
        <p class="card-description">Halaman ini memungkinkan admin untuk mengedit inventory</p>
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
        <form method="POST" action="{{ route('inventory.detailupdate', $pemegang->id) }}" id="jobPositionForm">
            @csrf
            @method('PUT')

            <div class="row mb-4">
                <div class="col-6 mb-3">
                    <label for="inventory_id" class="form-label">Inventaris</label>
                    <select class="select2" required name="inventory_id" id="inventorySelect">
                        @foreach($inventories as $category)
                            <option value="{{ $category->id }}" {{ $pemegang->inventory_id == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 mb-3">
                    <label for="total_quantity" class="form-label">Qty</label>
                    <input type="number" class="form-control" required name="qty" id="total_quantity" min="1" value="{{ old('qty', $pemegang->qty) }}">
                </div>

                <div class="col-6 mb-3">
                    <label for="mastercoach_id" class="form-label">Mastercoach</label>
                    <select class="select2" required name="mastercoach_id" id="mastercoachSelect">
                        @foreach($mastercoaches as $category)
                            <option value="{{ $category->id }}" {{ $pemegang->mastercoach_id == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 mb-3" id="emailField" style="display: none;">
                    <label for="email" class="form-label">Email Mastercoach Baru</label>
                    <input type="email" class="form-control" name="email" id="emailInput" placeholder="Masukkan email">
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
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

            $('#mastercoachSelect').on('select2:select', function(e) {
                var data = e.params.data;
                if (data.newTag) {
                    $('#emailField').show();
                    $('#emailInput').attr('required', true);
                } else {
                    $('#emailField').hide();
                    $('#emailInput').removeAttr('required');
                }
            });
        });
    </script>
@endpush
