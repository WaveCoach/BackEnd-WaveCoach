@extends('layouts.default')

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Daftar student</h5>
        <p>Menu "student" memungkinkan admin untuk mengelola, memantau, dan memperbarui informasi student secara efisien</p>

        <div class="d-flex">
            <a href="{{route('student.create')}}" class="btn btn-success btn-sm mb-4">
                <i class="fas fa-plus"></i> Tambah
            </a>
            <a href="{{route('students.export')}}" class="btn btn-primary btn-sm mb-4 mx-1">
                <i class="fas fa-file-export"></i> Export
            </a>
            <a href="{{route('students.formimport')}}" class="btn btn-primary btn-sm mb-4 mx-1">
                <i class="fas fa-file-import"></i> Import
            </a>
        </div>

        <table id="zero-conf" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>NIS</th>
                    <th>aksi</th>
                </tr>
            </thead>
            <tbody>
                    @foreach ($students as $item)

                    <tr>
                        <td>{{$loop->iteration}}</td>
                        <td>{{$item -> name}}</td>
                        <td>{{$item-> email}}</td>
                        <td>{{$item->student->nis}}</td>
                        <td class="d-flex">
                            <a href="{{route('student.edit', $item->id)}}" class="btn btn-warning btn-sm ">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{route('student.show', $item->id)}}" class="btn btn-info btn-sm mx-2">
                                <i class="fas fa-eye"></i>
                            </a>
                            <form action="{{route('student.destroy', $item->id)}}" method="POST" class="delete-form" style="display:inline;">
                                @method('delete')
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm" >
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach

            </tbody>
            <tfoot>
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>NIS</th>
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
    // Notifikasi sukses setelah menambah data
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{{ session('success') }}",
            showConfirmButton: false,
            timer: 2000
        });
    @endif

    // Notifikasi error jika gagal menghapus
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: "{{ session('error') }}",
            showConfirmButton: true
        });
    @endif


    // Konfirmasi sebelum menghapus data
    $(document).on('submit', 'form.delete-form', function(e) {
        e.preventDefault();
        let form = this;

        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: "Data yang dihapus tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
</script>

@endpush
