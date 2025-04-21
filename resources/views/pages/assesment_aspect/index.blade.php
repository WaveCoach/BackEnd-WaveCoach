@extends('layouts.default')

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Daftar Assessment Aspect</h5>
        <p>Menu "Assessment Aspect" memungkinkan admin untuk mengelola, memantau, dan memperbarui informasi assessment aspect secara efisien</p>

        <a href="{{ route('assesment-aspect.create') }}" class="btn btn-success btn-sm mb-4">
            <i class="fas fa-plus"></i> Tambah
        </a>

        <table id="zero-conf" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Gaya Renang</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $index => $category)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $category->name }}</td>
                        <td class="d-flex">
                            <a href="{{ route('assesment-aspect.show', $category->id) }}" class="btn btn-primary btn-sm me-2">
                                <i class="fas fa-eye"></i>
                            </a>

                            <a href="{{ route('assesmentaspect.edit', $category->id) }}" class="btn btn-warning btn-sm me-2">
                                <i class="fas fa-edit"></i>
                            </a>


                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>No</th>
                    <th>Gaya Renang</th>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Alert sukses saat menambahkan data baru
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Sukses!',
                text: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 2000
            });
        @endif

        // Event listener untuk tombol hapus
        document.querySelectorAll(".delete-btn").forEach(button => {
            button.addEventListener("click", function() {
                let form = this.closest("form"); // Dapatkan form terdekat dari tombol yang ditekan
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
                        form.submit(); // Submit form jika dikonfirmasi
                    }
                });
            });
        });
    });
</script>
@endpush
