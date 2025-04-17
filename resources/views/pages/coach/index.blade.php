@extends('layouts.default')

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Daftar Coach</h5>
        <p>Menu "Coach" memungkinkan admin untuk mengelola, memantau, dan memperbarui informasi Coach secara efisien</p>

        <div class="d-flex">
            <a href="{{route('coach.create')}}" class="btn btn-success btn-sm mb-4">
                <i class="fas fa-plus"></i> Tambah
            </a>
            <a href="{{route('coach.export')}}" class="btn btn-primary btn-sm mb-4">
                <i class="fas fa-file-export"></i> Export
            </a>
            <a href="{{route('coach.import')}}" class="btn btn-primary btn-sm mb-4">
                <i class="fas fa-file-import"></i> Import
            </a>
        </div>


        <table id="zero-conf" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal Terdaftar</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Role</th>
                    <th>aksi</th>
                </tr>
            </thead>
            <tbody>
                    @foreach ($coaches as $item)

                    <tr>
                        <td>{{$loop->iteration}}</td>
                        <td>{{ $item->coach->tanggal_bergabung ?? $item->created_at->format('Y-m-d') }}</td>
                        <td>{{$item -> name}}</td>
                        <td>{{$item-> email}}</td>
                        <td>
                            <span class="badge @if ($item->coach && $item->coach->status == 'active')
                            bg-success
                            @elseif ($item->coach && $item->coach->status == 'inactive')
                                bg-danger
                            @else
                                bg-secondary
                            @endif">
                                @if ($item->coach && $item->coach->status == 'active')
                                    active
                                @elseif ($item->coach && $item->coach->status == 'inactive')
                                    inactive
                                @else
                                    unknown
                                @endif
                            </span>
                        </td>

                        <td><span class="badge @if ($item->role_id == 2)
                            bg-primary
                        @else
                            bg-success
                        @endif "> @if ($item->role_id == 2)
                            coach
                        @else
                            mastercoach
                        @endif</span></td>
                        <td class="d-flex">
                            <a href="{{route('coach.edit', $item->id)}}" class="btn btn-warning btn-sm ">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{route('coach.show', $item->id)}}" class="btn btn-info btn-sm mx-2">
                                <i class="fas fa-eye"></i>
                            </a>
                            <form action="{{route('coach.updatePassword', $item->id)}}" method="POST" style="display:inline;">
                                @method('put')
                                @csrf
                                <button type="submit" class="btn btn-warning btn-sm btn-delete" >
                                    <i class="fas fa-key"></i>

                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach

            </tbody>
            <tfoot>
                <tr>
                    <th>No</th>
                    <th>Tanggal Masuk</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Role</th>
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
                    text: "Password dari coach ini akan direset!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Ya, reset!",
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
