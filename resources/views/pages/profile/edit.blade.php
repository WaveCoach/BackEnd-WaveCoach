@extends('layouts.default')

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Profile</h5>

        <form action="{{ route('profile.update', Auth::user()->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Upload Image -->
            <div class="d-flex justify-content-center mb-3">
                <label for="profile-image" class="position-relative">
                    <img id="preview-image"
                         src="{{ Auth::user()->profile_image ? asset('storage/' . Auth::user()->profile_image) : asset('assets/images/profile.jpeg') }}"
                         class="rounded-circle border profile-border"
                         style="width: 120px; height: 120px; object-fit: cover; cursor: pointer;">
                    <input type="file" name="image" id="profile-image" class="d-none" accept="image/*">

                    <!-- Ikon edit -->
                    <span class="position-absolute bottom-0 end-0 bg-dark rounded-circle d-flex align-items-center justify-content-center"
                          style="width: 30px; height: 30px; transform: translate(-5px, -5px); cursor: pointer;">
                        <i class="fas fa-pencil-alt text-white" style="font-size: 14px;"></i>
                    </span>
                </label>
            </div>

            <!-- Input Username -->
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" id="username" value="{{ Auth::user()->name }}" class="form-control" required>
            </div>

            <!-- Input Email -->
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" value="{{ Auth::user()->email }}" class="form-control" required>
            </div>

            <!-- Input Password -->
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control">
            </div>

            <!-- Input Password Confirmation -->
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
            </div>

            <!-- Tombol Submit -->
            <button type="submit" class="btn btn-success">Simpan</button>
        </form>
    </div>
</div>
@endsection

@push('custom-style')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
<style>
    .profile-border.filled {
        border: 3px solid #28a745 !important; /* Warna hijau jika gambar dipilih */
    }
</style>
@endpush

@push('custom-scripts')
<script>
    document.getElementById('profile-image').addEventListener('change', function(event) {
        let reader = new FileReader();
        reader.onload = function() {
            let imgElement = document.getElementById('preview-image');
            imgElement.src = reader.result;
            imgElement.classList.add('filled'); // Tambahkan border hijau
        };
        reader.readAsDataURL(event.target.files[0]);
    });

    // Beri efek jika user sudah punya gambar profil
    window.onload = function() {
        let imgElement = document.getElementById('preview-image');
        if (imgElement.src.includes("storage/")) {
            imgElement.classList.add('filled');
        }
    };
</script>
@endpush
