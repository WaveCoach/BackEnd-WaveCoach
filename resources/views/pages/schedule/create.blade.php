@extends('layouts.default')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Tambah Aspek Penilaian Baru</h5>
            <p class="card-description">Halaman ini memungkinkan admin untuk menambahkan Aspek Penilaian baru</p>
            <form method="POST" action="{{ route('schedule.store') }}" id="jobPositionForm">
                @csrf
                <div class="row mb-4">
                    <!-- Pilihan Coach -->
                    <div class="col-6 mb-3">
                        <label for="coach_id" class="form-label">Coach</label>
                        <select class="select2 form-control" required name="coach_id" id="coach-select">
                            <option value="" disabled selected>Pilih Coach</option>
                            @foreach ($coach as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Email Coach (Muncul jika coach baru dipilih) -->
                    <div class="col-6 mb-3" id="email-container" style="display: none;">
                        <label class="form-label">Email Coach</label>
                        <div id="email-list">
                            <input type="email" class="form-control email-input" name="email" placeholder="Masukkan Email Coach">
                        </div>
                    </div>

                    <!-- Pilihan Tanggal -->
                    <div class="col-6 mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control" required name="date">
                    </div>

                    <!-- Pilihan Waktu Mulai -->
                    <div class="col-6 mb-3">
                        <label for="start_time" class="form-label">Start Time</label>
                        <input type="time" class="form-control" required name="start_time">
                    </div>

                    <!-- Pilihan Waktu Selesai -->
                    <div class="col-6 mb-3">
                        <label for="end_time" class="form-label">End Time</label>
                        <input type="time" class="form-control" required name="end_time">
                    </div>

                    <!-- Pilihan Student -->
                    <div class="col-6 mb-3">
                        <label for="student_id" class="form-label">Student</label>
                        <select class="select2 form-control" required name="student_id[]" id="student-select" multiple>
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
                        <select class="select2 form-control" required name="location_id" id="location-select">
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
            // Inisialisasi Select2
            $('#coach-select, #student-select, #location-select').select2({
                width: '100%',
                placeholder: "Pilih Opsi",
                allowClear: true
            });

            // Event handler saat coach dipilih
            // $('#coach-select').on('change', function() {
            //     var coachId = $(this).val();
            //     var studentSelect = $('#student-select');

            //     studentSelect.empty().append('<option disabled selected>Loading...</option>');

            //     if (coachId) {
            //         $.ajax({
            //             url: "{{ url('/get-student') }}",
            //             type: "GET",
            //             data: { coach_id: coachId },
            //             success: function(response) {
            //                 console.log(response);
            //                 studentSelect.empty();

            //                 if (response.students && response.students.length > 0) {
            //                     $.each(response.students, function(key, student) {
            //                         console.log("Student ID: " + student.id + " | Name: " + student.user.name);
            //                         studentSelect.append('<option value="' + student.id + '" selected>' + student.user.name + '</option>');
            //                     });
            //                 } else {
            //                     console.log("Tidak ada student tersedia.");
            //                     studentSelect.append('<option disabled>Tidak ada student tersedia</option>');
            //                 }
            //             },
            //             error: function(xhr, status, error) {
            //                 console.error("AJAX Error: ", status, error);
            //                 console.log(xhr.responseText);
            //                 studentSelect.empty().append('<option disabled>Gagal mengambil data</option>');
            //             }
            //         });
            //     }
            // });


            // Event handler untuk menampilkan/menghilangkan input email coach
            $('#coach-select').on('select2:select', function(e) {
                var data = e.params.data;
                if (data.newTag) {
                    $('#email-container').fadeIn();
                } else {
                    $('#email-container').fadeOut();
                }
            });

            // Tambahkan input email baru saat Enter ditekan
            $('#email-list').on('keypress', '.email-input', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    $(this).after('<input type="email" class="form-control email-input mt-2" name="email" placeholder="Masukkan Email Coach">');
                }
            });

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
@endpush
