@extends('layouts.default')

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Daftar Coach</h5>
        <p>Menu "Coach" memungkinkan admin untuk mengelola, memantau, dan memperbarui informasi Coach secara efisien</p>

        <a href="{{route('coach.create')}}" class="btn btn-success btn-sm mb-4">
            <i class="fas fa-plus"></i> Tambah
        </a>
        <table id="zero-conf" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>coach</th>
                    <th>date</th>
                    <th>time start</th>
                    <th>reason</th>
                    <th>Admin Reason</th>
                    <th>Status</th>
                    <th>aksi</th>
                </tr>
            </thead>
            <tbody>
                    @foreach ($reschedules as $item)
                    <tr>
                        <td>{{$loop->iteration}}</td>
                        <td>{{$item->coach->name}}</td>
                        <td>{{$item->requested_date}}</td>
                        <td>{{$item->requested_time}}</td>
                        <td>{{$item->reason}}</td>
                        <td>{{$item->response_message}}</td>
                        <td>
                            @if ($item->status === 'pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                            @elseif ($item->status === 'approved')
                                <span class="badge bg-success">Approved</span>
                            @elseif ($item->status === 'rejected')
                                <span class="badge bg-danger">Rejected</span>
                            @else
                                <span class="badge bg-secondary">Unknown</span>
                            @endif
                        </td>

                        <td>
                            <div class="d-flex">
                                <a href="{{route('reschedule.edit', $item->id)}}" class="btn btn-info btn-sm mx-2">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <a href="{{route('schedule.edit', $item->schedule_id)}}" class="btn btn-info btn-sm mx-2">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </td>

                    </tr>
                    @endforeach

            </tbody>
            <tfoot>
                <tr>
                    <th>No</th>
                    <th>coach</th>
                    <th>date</th>
                    <th>time start</th>
                    <th>reason</th>
                    <th>Admin Reason</th>
                    <th>Status</th>
                    <th>aksi</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection

@push('custom-style')
<link href="https://fonts.googleapis.com/css?family=Poppins:400,500,700,800&display=swap" rel="stylesheet">
<link href="{{asset('assets/plugins/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
<link href="{{asset('assets/plugins/font-awesome/css/all.min.css')}}" rel="stylesheet">
<link href="{{asset('assets/plugins/perfectscroll/perfect-scrollbar.css')}}" rel="stylesheet">
<link href="{{asset('assets/plugins/DataTables/datatables.min.css')}}" rel="stylesheet">

<!-- Theme Styles -->
<link href="{{asset('assets/css/main.min.css')}}" rel="stylesheet">
<link href="{{asset('assets/css/custom.css')}}" rel="stylesheet">
@endpush

@push('custom-scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // SweetAlert konfirmasi sebelum hapus
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');

                Swal.fire({
                    title: "Apakah Anda yakin?",
                    text: "Data role yang sudah diubah tidak bisa dikembalikan!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Ya, hapus!",
                    cancelButtonText: "Batal"
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        // SweetAlert untuk notifikasi sukses
        @if(session('success'))
            Swal.fire({
                title: "Berhasil!",
                text: "{{ session('success') }}",
                icon: "success",
                confirmButtonText: "OK"
            });
        @endif
    });
</script>
@endpush
