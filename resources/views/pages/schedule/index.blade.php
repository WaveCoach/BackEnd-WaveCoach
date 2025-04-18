@extends('layouts.default')

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Daftar Jadwal</h5>
        <p>Menu "Jadwal" memungkinkan admin untuk mengelola dan memantau jadwal sesi pelatihan secara efisien.</p>

        <a href="{{ route('schedule.create') }}" class="btn btn-success btn-sm mb-4">
            <i class="fas fa-plus"></i> Tambah Jadwal
        </a>

        <a href="{{ route('importSchedule.create') }}" class="btn btn-primary mb-4">
            <i class="fas fa-file"></i>
        </a>

        <table id="zero-conf" class="display table" style="width:100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Pelatih</th>
                    <th>Lokasi</th>
                    <th>Waktu</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($schedules as $index => $schedule)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $schedule->date }}</td>
                        <td>{{ $schedule->coach->name ?? '-' }}</td>
                        <td>{{ $schedule->location->name ?? '-' }}</td>
                        <td>
                            {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} -
                            {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                        </td>
                        <td>
                            <form action="{{ route('schedule.destroy', $schedule->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?');">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>

                            <a href="{{ route('schedule.show', $schedule->id) }}" class="btn btn-success btn-sm">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('schedule.edit', $schedule->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Pelatih</th>
                    <th>Lokasi</th>
                    <th>Waktu</th>
                    <th>Aksi</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection

@push('custom-style')
    <link href="{{ asset('assets/plugins/DataTables/datatables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/main.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet">
@endpush

@push('custom-scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>

    <script>
        $(document).ready(function() {
            console.log("Document Ready: Initializing DataTables");

            $('#zero-conf').DataTable();

            @if(session('success'))
                setTimeout(() => {
                    console.log("Session Success: Showing SweetAlert");
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: "{{ session('success') }}",
                        showConfirmButton: false,
                        timer: 2000
                    });
                }, 500);
            @endif
        });
    </script>
@endpush
