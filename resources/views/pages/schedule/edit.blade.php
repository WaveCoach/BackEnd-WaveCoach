@extends('layouts.default')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Edit Jadwal</h5>
            <p class="card-description">Halaman ini memungkinkan admin untuk mengedit jadwal yang ada</p>
            <form method="POST" action="{{ route('schedule.update', $schedule->id) }}" id="scheduleForm">
                @csrf
                @method('PUT')

                <div class="row mb-4">
                    <div class="col-6 mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control" required name="date" value="{{ $schedule->date }}">
                    </div>

                    <div class="col-6 mb-3">
                        <label for="start_time" class="form-label">Start Time</label>
                        <input type="time" class="form-control" required name="start_time"
                               value="{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}">
                    </div>

                    <div class="col-6 mb-3">
                        <label for="end_time" class="form-label">End Time</label>
                        <input type="time" class="form-control" required name="end_time"
                               value="{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}">
                    </div>

                    <div class="col-6 mb-3">
                        <label for="student_id" class="form-label">Student</label>
                        <select class="select2 form-control" required name="student_id[]" id="student-select" multiple>
                            @foreach ($students as $student)
                                <option value="{{ $student->id }}"
                                    {{ in_array($student->id, $schedule->students->pluck('id')->toArray()) ? 'selected' : '' }}>
                                    {{ $student->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Select2 untuk Lokasi -->
                    <div class="col-6 mb-3">
                        <label for="location_id" class="form-label">Location</label>
                        <select class="select2" required name="location_id" id="location-select">
                            @foreach ($locations as $loc)
                                <option value="{{ $loc->id }}" {{ $schedule->location_id == $loc->id ? 'selected' : '' }}>
                                    {{ $loc->name }}
                                </option>
                            @endforeach
                        </select>
                        <a href="#" id="add-address">Tambahkan Kolam</a>
                        <div id="address-input" style="display: none; margin-top: 10px;">
                            <input type="text" class="form-control" name="address" placeholder="Masukkan Alamat Kolam">
                        </div>
                    </div>

                    <!-- Select2 untuk Coach -->
                    <div class="col-6 mb-3">
                        <label for="coach_id" class="form-label">Coach</label>
                        <select class="select2" required name="coach_id" id="coach-select">
                            @foreach ($coaches as $c)
                                <option value="{{ $c->id }}" {{ $schedule->coach_id == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                        <a href="#" id="add-email">Tambahkan Email</a>
                        <div id="email-input" style="display: none; margin-top: 10px;">
                            <input type="email" class="form-control" name="email" placeholder="Masukkan Email Coach">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('schedule.index') }}" class="btn btn-warning">Kembali</a>
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
            $('#coach-select, #location-select, #student-select').select2({
                width: '100%',
                placeholder: "Pilih atau Tambah Opsi",
                allowClear: true,
                tags: true
            });

            $('#add-email').on('click', function(e) {
                e.preventDefault();
                $('#email-input').toggle();
            });

            $('#add-address').on('click', function(e) {
                e.preventDefault();
                $('#address-input').toggle();
            });
        });
    </script>
@endpush
