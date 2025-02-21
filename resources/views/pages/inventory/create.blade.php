@extends('layouts.default')

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Tambah inventory Baru</h5>
        <p class="card-description">Halaman ini memungkinkan admin untuk menambahkan inventory baru</p>
        <form method="POST" action="{{ route('inventory.store') }}" id="jobPositionForm">
            @csrf
            <div class="row mb-4">
                <div class="col-6 mb-3">
                    <label for="name" class="form-label">Nama Barang</label>
                    <input type="text" class="form-control" required name="name" id="name">
                </div>
                <div class="col-6 mb-3">
                    <label for="qty" class="form-label">qty</label>
                    <input type="text" class="form-control" required name="total_quantity" id="qty">
                </div>

            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>
@endsection

@push('custom-style')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.0/classic/ckeditor.js"></script>
@endpush

