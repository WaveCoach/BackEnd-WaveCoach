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
            <form method="POST" action="{{ route('schedule.store') }}" id="jobPositionForm">
                @csrf
                <div class="row mb-4">
                    <div class="col-6 mb-3">
                        <label for="package_id" class="form-label">Package</label>
                        <select class="select2 form-control" name="package_id" id="package-select">
                            <option value="" disabled selected>Pilih Package</option>
                            @foreach ($packages as $package)
                                <option value="{{ $package->id }}">{{ $package->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Pilihan Coach -->
                    <div class="col-6 mb-3">
                        <label for="mastercoach_id" class="form-label">coach</label>
                        <select class="select2"  name="coach_id" id="coachSelect">
                            <option value="" disabled selected>Pilih coach</option>
                            @foreach($coach as $i)
                                <option value="{{ $i->id }}">{{ $i->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 mb-3" id="emailField" style="display: none;">
                        <label for="email" class="form-label">Email coach Baru</label>
                        <input type="email" class="form-control" name="email" id="emailInput" placeholder="Masukkan email">
                    </div>

                    <!-- Pilihan Tanggal -->
                    <div class="col-6 mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control"  name="date">
                    </div>

                    <!-- Pilihan Waktu Mulai -->
                    <div class="col-6 mb-3">
                        <label for="start_time" class="form-label">Start Time</label>
                        <input type="time" class="form-control"  name="start_time">
                    </div>

                    <!-- Pilihan Waktu Selesai -->
                    <div class="col-6 mb-3">
                        <label for="end_time" class="form-label">End Time</label>
                        <input type="time" class="form-control"  name="end_time">
                    </div>

                    <!-- Pilihan Student -->
                    <div class="col-6 mb-3">
                        <label for="student_id" class="form-label">Student</label>
                        <select class="select2 form-control"  name="student_id[]" id="student-select" multiple>
                            {{-- <option value="">Pilih Student</option> --}}
                            @foreach ($students as $student)
                                <option value="{{ $student->id }}"
>
                                    {{ $student->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Pilihan Lokasi -->
                    <div class="col-6 mb-3">
                        <label for="location_id" class="form-label">Location</label>
                        <select class="select2 form-control"  name="location_id" id="location-select">
                            <option value="" disabled selected>Pilih Location</option>
                            @foreach ($location as $loc)
                                <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                            @endforeach
                        </select>

                        <!-- Input Lokasi Baru -->
                        <div id="address-fields" style="display: none; margin-top: 10px;">
                            <input type="text" class="form-control mb-2" name="address" placeholder="Masukkan Alamat Kolam">
                            <input type="text" class="form-control" name="maps" placeholder="Masukkan URL Alamat Kolam">
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
            // Select2 untuk lokasi dengan opsi custom
            $('#location-select').select2({
                tags: true,
                createTag: function(params) {
                    var term = $.trim(params.term);
                    if (term === '') return null;
                    return { id: term, text: term, newTag: true };
                }
            }).on('select2:select', function(e) {
                var data = e.params.data;
                if (data.newTag) {
                    $('#address-fields').fadeIn();
                } else {
                    $('#address-fields').fadeOut();
                }
            });
        });
    </script>

<script>
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%',
            placeholder: "Pilih atau Tambah Opsi",
            allowClear: true,
            tags: true,
            createTag: function(params) {
                var term = $.trim(params.term);
                return term === '' ? null : { id: term, text: term, newTag: true };
            }
        });

        $('#coachSelect').on('select2:select', function(e) {
            var data = e.params.data;
            if (data.newTag) {
                $('#emailField').show();
                $('#emailInput').attr('required', true);
            } else {
                $('#emailField').hide();
                $('#emailInput').removeAttr('required');
            }
        });
    });
</script>

@endpush
