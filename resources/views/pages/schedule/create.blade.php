@extends('layouts.default')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Tambah Aspek Penilaian Baru</h5>
            <p class="card-description">Halaman ini memungkinkan admin untuk menambahkan Aspek Penilaian baru</p>
            <form method="POST" action="{{ route('schedule.store') }}" id="jobPositionForm">
                @csrf
                <div class="row mb-4">
                    <div class="col-6 mb-3">
                        <label for="coach_id" class="form-label">Coach</label>
                        <select class="select2" required name="coach_id" id="coach-select">
                            <option value="" disabled selected>Pilih Coach</option>
                            @foreach ($coach as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
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
                        <input type="date" class="form-control" required name="date">
                    </div>
                    <div class="col-6 mb-3">
                        <label for="start_time" class="form-label">Start Time</label>
                        <input type="time" class="form-control" required name="start_time">
                    </div>
                    <div class="col-6 mb-3">
                        <label for="end_time" class="form-label">End Time</label>
                        <input type="time" class="form-control" required name="end_time">
                    </div>
                    <div class="col-6 mb-3">
                        <label for="student_id" class="form-label">Student</label>
                        <select class="select2 form-control" required name="student_id[]" id="student-select" multiple>
                            <option value="" disabled>Pilih Student</option>
                            @foreach ($students as $student)
                                <option value="{{ $student->id }}">{{ $student->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 mb-3">
                        <label for="location_id" class="form-label">Location</label>
                        <select class="select2" required name="location_id" id="location-select">
                            <option value="" disabled selected>Pilih Location</option>
                            @foreach ($location as $loc)
                                <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                            @endforeach
                        </select>
                        <div id="address-fields" style="display: none; margin-top: 10px;">
                            <input type="text" class="form-control mb-2" name="address" placeholder="Masukkan Alamat Kolam">
                            <input type="url" class="form-control" name="url" placeholder="Masukkan URL Alamat Kolam">
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
        $(document).ready(function() {
            // Tampilkan email coach setelah memilih coach
            $('#category-select').on('change', function() {
                $('#email-container').fadeIn();
            });

            // Tambahkan input email baru saat enter ditekan
            $('#email-list').on('keypress', '.email-input', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    $(this).after('<input type="email" class="form-control email-input mt-2" name="email" placeholder="Masukkan Email Coach">');
                }
            });

            // Select2 untuk lokasi dengan opsi baru
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
                } else {
                    $('#email-container').hide();
                }
            });

            // Select2 untuk student dan coach
            $('#student-select, #category-select').select2({
                width: '100%',
                placeholder: "Pilih Opsi",
                allowClear: true
            });
        });
    </script>
@endpush
