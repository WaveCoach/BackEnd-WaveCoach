@extends('layouts.default')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Ubah Status</h5>
            <p class="card-description">Halaman ini memungkinkan admin untuk mengubah status </p>

            <form method="POST" action="{{ route('reschedule.update', $reschedules->id) }}" id="scheduleForm">
                @csrf
                @method('PUT')

                <div class="row mb-4">
                    <!-- Pilihan Status -->
                    <div class="col-6 mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" required name="status" id="status-select">
                            <option value="rejected" {{ strtolower($reschedules->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="approved" {{ strtolower($reschedules->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="pending" {{ strtolower($reschedules->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                    </div>



                    <!-- Email Coach (Selalu Ditampilkan) -->
                    <div class="col-6 mb-3" id="email-container">
                        <label class="form-label">Admin Message</label>
                        <input type="text" class="form-control email-input"
                               value="{{ $reschedules->response_message ?? '' }}"
                               name="response_message" placeholder="ketik disini">
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('reschedule.index') }}" class="btn btn-warning">Kembali</a>
            </form>
        </div>
    </div>
@endsection

@push('custom-style')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
    <style>
        .select2-container { width: 100% !important; }
    </style>
@endpush

@push('custom-scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
@endpush
