@extends('layouts.default')

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Edit Inventory</h5>
        <p class="card-description">Halaman ini memungkinkan admin untuk melakukan perubahan pada inventory</p>

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('inventory.update', $inventory->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row mb-4">
                <div class="col-6 mb-3">
                    <label for="name" class="form-label">Nama</label>
                    <input type="text" class="form-control"  value="{{ $inventory->name }}" name="name" id="name">
                </div>

                <div class="col-6 mb-3">
                    <label for="inventory_image" class="form-label">Gambar Inventaris</label>
                    <input type="file" class="form-control" name="inventory_image" id="imageInput">

                    {{-- Tempat menampilkan gambar (lama atau baru) --}}
                    <div class="mt-3">
                        <img id="previewImage" src="{{ asset('storage/' . $inventory->inventory_image) }}" alt="Inventory Image" width="150">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>
@endsection

@push('custom-scripts')
<script>
    document.getElementById('imageInput').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const preview = document.getElementById('previewImage');
        const oldImage = "{{ asset('storage/' . $inventory->inventory_image) }}";

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
            };
            reader.readAsDataURL(file);
        } else {
            preview.src = oldImage; // Kembali ke gambar lama jika tidak ada file baru
        }
    });
</script>
@endpush
