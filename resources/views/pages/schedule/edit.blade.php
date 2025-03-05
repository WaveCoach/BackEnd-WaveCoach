@extends('layouts.default')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Edit Jadwal</h5>
            <p class="card-description">Halaman ini memungkinkan admin untuk mengedit jadwal yang ada</p>
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <form method="POST" action="{{ route('schedule.update', $schedule->id) }}" id="scheduleForm">
                @csrf
                @method('PUT')

                <div class="row mb-4">
                    <div class="col-6 mb-3">
                        <label for="coach_id" class="form-label">Coach</label>
                        <select class="select2" required name="coach_id" id="coach-select">
                            @foreach ($coaches as $c)
                                <option value="{{ $c->id }}" {{ $schedule->coach_id == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 mb-3" id="email-container" style="display: none;">
                        <label class="form-label">Email Coach</label>
                        <div id="email-list">
                            <input type="email" class="form-control email-input" name="email" placeholder="Masukkan Email Coach">
                        </div>
                    </div>

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
                        <div id="address-fields" style="display: none; margin-top: 10px;">
                            <input type="text" class="form-control mb-2" name="address" placeholder="Masukkan Alamat Kolam">
                            <input type="url" class="form-control" name="url" placeholder="Masukkan URL Alamat Kolam">
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
            $('#coach-select').select2({
                width: '100%',
                placeholder: "Pilih atau Tambah Opsi",
                allowClear: true,
                tags: true,
                createTag: function(params) {
                    var term = $.trim(params.term);
                    if (term === '') {
                        return null;
                    }
                    return {
                        id: term,
                        text: term,
                        newTag: true
                    };
                }
            }).on('select2:select', function(e) {
                var data = e.params.data;
                if (data.newTag) {
                    $('#email-container').show();
                    $('#email-list').html('<input type="email" class="form-control email-input" name="email" placeholder="Masukkan Email Coach">');
                } else {
                    $('#email-container').hide();
                    $('#email-list').empty();
                }
            });

            $('#email-list').on('keypress', '.email-input', function(e) {
                if (e.which === 13) {
                    e.preventDefault(); // Mencegah input email bertambah terus saat tekan enter
                }
            });

            $('#location-select').select2({
                width: '100%',
                placeholder: "Pilih atau Tambah Opsi",
                allowClear: true,
                tags: true,
                createTag: function(params) {
                    var term = $.trim(params.term);
                    if (term === '') {
                        return null;
                    }
                    return {
                        id: term,
                        text: term,
                        newTag: true
                    };
                }
            }).on('select2:select', function(e) {
                var data = e.params.data;
                if (data.newTag) {
                    $('#address-fields').show();
                } else {
                    $('#address-fields').hide();
                }
            });

            $('#student-select').select2({
                width: '100%',
                placeholder: "Pilih Opsi",
                allowClear: true
            });
        });
    </script>
@endpush
