@extends('layouts.default')

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Student Attendances</h5>
        <p>Menu "student attendances" memungkinkan admin untuk  memantau presensi student secara efisien</p>

        @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 2000
                });
            });
        </script>
        @endif

        <form method="GET" action="{{ route('attendance.student.show', request()->route('id')) }}" class="d-flex mb-3 mt-3">
            <input type="date" name="date_start" value="{{ request('date_start') }}" class="form-control" placeholder="Start Date" style="width: 200px; margin-right: 10px;">
            <input type="date" name="date_end" value="{{ request('date_end') }}" class="form-control" placeholder="End Date" style="width: 200px; margin-right: 10px;">
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>

        <table id="zero-conf" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Tanggal Jadwal</th>
                    <th>Tanggal Presensi</th>
                    <th>Jam</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($schedule as $item)
                <tr>
                    <td>{{$loop->iteration}}</td>
                    <td>{{$item->student->name}}</td>
                    <td>
                        @if ($item->schedule)
                            {{ \Carbon\Carbon::parse($item->schedule->date)->translatedFormat('d F Y') }}
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>

                    <td>{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d F Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('H:i') }}</td>
                    <td>
                        @if($item->attendance_status == 'Hadir')
                            <span class="badge bg-success">Hadir</span>
                        @else
                            <span class="badge bg-danger">Tidak Hadir</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{route('schedule.show', $item->schedule_id)}}" class="btn btn-info btn-sm mx-2">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Tanggal Jadwal</th>
                    <th>Tanggal Presensi</th>
                    <th>Jam</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection

@push('custom-style')
<link href="https://fonts.googleapis.com/css?family=Poppins:400,500,700,800&display=swap" rel="stylesheet">
<link href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/plugins/font-awesome/css/all.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/plugins/perfectscroll/perfect-scrollbar.css') }}" rel="stylesheet">
<link href="{{ asset('assets/plugins/DataTables/datatables.min.css') }}" rel="stylesheet">

<!-- Theme Styles -->
<link href="{{ asset('assets/css/main.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet">
@endpush

@push('custom-scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".delete-btn").forEach(button => {
            button.addEventListener("click", function() {
                const form = this.closest(".delete-form");
                Swal.fire({
                    title: "Apakah Anda yakin?",
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Ya, hapus!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endpush
