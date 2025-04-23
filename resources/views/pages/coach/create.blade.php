@extends('layouts.default')

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Tambah Coach Baru</h5>
        <p class="card-description">Halaman ini memungkinkan admin untuk menambahkan Coach baru</p>
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <form method="POST" action="{{ route('coach.store') }}" id="jobPositionForm">
            @csrf
            <div class="row mb-4">
                <div class="col-6 mb-3">
                    <label for="name" class="form-label">Nama</label>
                    <input type="text" class="form-control" required name="name" id="name">
                </div>
                <div class="col-6 mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" required name="email" id="email">
                </div>
                <div class="col-6 mb-3">
                    <label for="tanggal_bergabung" class="form-label">Tanggal Bergabung</label>
                    <input type="date" class="form-control" required name="tanggal_bergabung" id="tanggal_bergabung">
                </div>
                <div class="col-6 mb-3">
                    <label for="package_id" class="form-label">Packages</label>
                    <select class="select2 form-control"  name="package_id[]" id="coach-select" multiple>
                        {{-- <option value="">Pilih Student</option> --}}
                        @foreach ($packages as $p)
                            <option value="{{ $p->id }}">
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 mb-3">
                    <label for="specialization" class="form-label">Role</label>
                    <select class="form-select" required name="role_id" id="specialization">
                        <option value="" selected disabled>Pilih Role</option>
                        <option value="2">Coach</option>
                        <option value="3">mastercoach</option>
                    </select>
                </div>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#coach-select').select2({
                placeholder: "Pilih Package",
                allowClear: true
            });
        });
    </script>
@endpush
