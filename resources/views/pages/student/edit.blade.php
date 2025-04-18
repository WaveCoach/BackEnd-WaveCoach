@extends('layouts.default')

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Edit student</h5>
        <p class="card-description">Halaman ini memungkinkan admin untuk melakukan perubahan pada student</p>

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('student.update', $student->id) }}" id="jobPositionForm">
            @csrf
            @method('PUT')

            <div class="row mb-4">
                <div class="col-6 mb-3">
                    <label for="name" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" required value="{{ $student->name }}" name="name" id="name">
                </div>

                <div class="col-6 mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="text" class="form-control" required value="{{ $student->email }}" name="email" id="email">
                </div>

                <div class="col-6 mb-3">
                    <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                    <input type="date" class="form-control" value="{{ $student->student->tanggal_lahir }}" name="tanggal_lahir" id="tanggal_lahir">
                </div>

                <div class="col-6 mb-3">
                    <label for="tanggal_bergabung" class="form-label">Tanggal bergabung</label>
                    <input type="date" class="form-control" value="{{ $student->student->tanggal_bergabung }}" name="tanggal_bergabung" id="tanggal_bergabung">
                </div>

                <div class="col-6 mb-3">
                    <label for="package_id" class="form-label">Packages</label>
                    <select class="select2 form-control" name="package_id[]" id="student-select" multiple>
                        @foreach ($allPackages as $p)
                            <option value="{{ $p->id }}"
                                {{ in_array($p->id, $packageSelected) ? 'selected' : '' }}>
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>

                </div>


                <div class="col-6 mb-3">
                    <label class="form-label">Jenis Kelamin</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="jenis_kelamin" id="laki-laki" value="L"
                                   {{ $student->student->jenis_kelamin == 'L' ? 'checked' : '' }}>
                            <label class="form-check-label" for="laki-laki">Laki-laki</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="jenis_kelamin" id="perempuan" value="P"
                                   {{ $student->student->jenis_kelamin == 'P' ? 'checked' : '' }}>
                            <label class="form-check-label" for="perempuan">Perempuan</label>
                        </div>
                    </div>
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
        $(document).ready(function () {
            $('#student-select').select2({
                placeholder: "Pilih Package",
                allowClear: true
            });
        });
    </script>
@endpush
