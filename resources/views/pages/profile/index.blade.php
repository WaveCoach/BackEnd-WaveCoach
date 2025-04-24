@extends('layouts.default')

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Profile</h5>

        <form action="{{ route('mastercoach.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Upload Image -->
            <div class="d-flex justify-content-center mb-3">
                <div class="position-relative">
                    <img id="preview-image"
                    src="{{ Auth::user()->profile_image ? Auth::user()->profile_image : asset('assets/images/profile.jpeg') }}"
                    class="rounded-circle border profile-border {{ Auth::user()->profile_image ? 'filled' : '' }}"
                    style="width: 120px; height: 120px; object-fit: cover;">
                </div>
            </div>


            <!-- Input Username -->
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" id="username" disabled value="{{Auth::user()->name}}" class="form-control" required>
            </div>

            <!-- Input Email -->
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" disabled value="{{Auth::user()->email}}" class="form-control" required>
            </div>



            <!-- Tombol Submit -->
            <a href="{{ url()->previous() }}" class="btn btn-primary mt-3">Kembali</a>
            <a href="{{route('profile.edit', Auth::user()->id)}}" class="btn btn-warning mt-3">Edit</a>
        </form>
    </div>
</div>
@endsection

@push('custom-script')
<script>
    // Preview gambar saat upload
    document.getElementById('profile-image').addEventListener('change', function(event) {
        let reader = new FileReader();
        reader.onload = function() {
            document.getElementById('preview-image').src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    });
</script>
@endpush
