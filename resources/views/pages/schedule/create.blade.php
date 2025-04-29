@extends('layouts.default')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Tambah Schedule Baru</h5>
            <p class="card-description">Halaman ini memungkinkan admin untuk menambahkan Schedule baru</p>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('schedule.store') }}" id="jobPositionForm">
                @csrf
                <div class="row mb-4">
                    {{-- Package --}}
                    <div class="col-6 mb-3">
                        <label for="package_id" class="form-label">Package</label>
                        <select class="select2 form-control" name="package_id" id="package-select">
                            <option value="" disabled selected>Pilih Package</option>
                            @foreach ($packages as $package)
                                <option value="{{ $package->id }}">{{ $package->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Coach --}}
                    <div class="col-6 mb-3">
                        <label for="coach_id" class="form-label">Coach</label>
                        <select class="select2 form-control" name="coach_id" id="coachSelect">
                            <option value="" disabled selected>Pilih Coach</option>
                        </select>
                    </div>

                    {{-- Email coach baru --}}
                    <div class="col-6 mb-3" id="emailField" style="display: none;">
                        <label for="email" class="form-label">Email Coach Baru</label>
                        <input type="email" class="form-control" name="email" id="emailInput" placeholder="Masukkan email">
                    </div>

                    {{-- Tanggal --}}
                    <div class="col-6 mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control" name="date">
                    </div>

                    {{-- Jam mulai --}}
                    <div class="col-6 mb-3">
                        <label for="start_time" class="form-label">Start Time</label>
                        <input type="time" class="form-control" name="start_time">
                    </div>

                    {{-- Jam selesai --}}
                    <div class="col-6 mb-3">
                        <label for="end_time" class="form-label">End Time</label>
                        <input type="time" class="form-control" name="end_time">
                    </div>

                    {{-- Student --}}
                    <div class="col-6 mb-3">
                        <label for="student_id" class="form-label">Student</label>
                        <select class="select2 form-control" name="student_id[]" id="student-select" multiple>
                            <option value="">Pilih Student</option>
                        </select>
                    </div>

                    {{-- Location --}}
                    <div class="col-6 mb-3">
                        <label for="location_id" class="form-label">Location</label>
                        <select class="select2 form-control" name="location_id" id="location-select">
                            <option value="" disabled selected>Pilih Location</option>
                            @foreach ($location as $loc)
                                <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                            @endforeach
                        </select>

                        {{-- Kolam baru --}}
                        <div id="address-fields" style="display: none; margin-top: 10px;">
                            <input type="text" class="form-control mb-2" name="address" placeholder="Masukkan Alamat Kolam">
                            <input type="text" class="form-control" name="maps" placeholder="Masukkan URL Alamat Kolam">
                        </div>
                    </div>

                    {{-- Penilaian --}}
                    <div class="col-6 mb-3">
                        <label for="is_assessed" class="form-label">Penilaian</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_assessed" id="is_assessed" value="1">
                            <label class="form-check-label" for="is_assessed">Apakah ada penilaian?</label>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script>
        $(document).ready(function () {
            $('.select2').select2({
                width: '100%',
                placeholder: "Pilih atau Tambah Opsi",
                allowClear: true,
                tags: true,
                createTag: function (params) {
                    var term = $.trim(params.term);
                    return term === '' ? null : {
                        id: term,
                        text: term,
                        newTag: true
                    };
                }
            });

            $('#location-select').on('select2:select', function (e) {
                var data = e.params.data;
                if (data.newTag) {
                    $('#address-fields').fadeIn();
                } else {
                    $('#address-fields').fadeOut();
                }
            });

            $('#coachSelect').on('select2:select', function (e) {
                var data = e.params.data;
                if (data.newTag) {
                    $('#emailField').show();
                    $('#emailInput').attr('required', true);
                } else {
                    $('#emailField').hide();
                    $('#emailInput').removeAttr('required');
                }
            });

            $('#package-select').on('change', function () {
                const packageId = $(this).val();
                $('#student-select').empty().trigger('change');
                $('#coachSelect').empty().trigger('change');
                $('#emailField').hide();
                $('#emailInput').removeAttr('required');

                if (!packageId) return;

                $.ajax({
                    url: `/get-students-by-package/${packageId}`,
                    type: 'GET',
                    success: function (data) {
                        if (data.students) {
                            let studentOptions = data.students.map(function (student) {
                                return new Option(student.student_name, student.user_id, false, false);
                            });
                            $('#student-select').append(studentOptions).trigger('change');
                        }

                        if (data.coaches) {
                            let coachOptions = data.coach.map(function (coach) {
                                return new Option(coach.coach_name, coach.coach_id, false, false);
                            });
                            $('#coachSelect').append(coachOptions).trigger('change');
                        }
                    },
                    error: function () {
                        alert('Gagal mengambil data package!');
                    }
                });
            });
        });
    </script>
@endpush
