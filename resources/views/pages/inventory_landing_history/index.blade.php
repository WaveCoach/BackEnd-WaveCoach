@extends('layouts.default')

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">History Inventory</h5>
        <p>Menu "History inventory" memungkinkan admin  memantau informasi daftar History Inventory secara efisien</p>

        <table id="zero-conf" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Waktu</th>
                    <th>Nama Barang</th>
                    <th>Mastercoach</th>
                    <th>Coach Peminjam</th>
                    <th>Status</th>
                    <th>Qty</th>
                </tr>
            </thead>
            <tbody>
                    @foreach ($inventorys as $inventory)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{$inventory->created_at}}</td>
                        <td>{{$inventory->inventory->name}}</td>
                        <td>{{$inventory->coach->name}}</td>
                        <td>{{$inventory->mastercoach->name}}</td>
                        <td>
                            @if ($inventory->status == 'dipinjam')
                                <span class="badge bg-warning">Dipinjam</span>
                            @elseif ($inventory->status == 'dikembalikan')
                                <span class="badge bg-success">Dikembalikan</span>
                            @endif
                        </td>
                        <td>
                            @if ($inventory->status == 'dipinjam')
                                 {{ $inventory->qty_out }}
                            @elseif ($inventory->status == 'dikembalikan')
                                {{ $inventory->qty_in }}
                            @endif
                        </td>
                    </tr>
                    @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>No</th>
                    <th>Waktu</th>
                    <th>Nama Barang</th>
                    <th>Mastercoach</th>
                    <th>Coach Peminjam</th>
                    <th>Status</th>
                    <th>Qty</th>
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
    @if(session('success'))
        Swal.fire({
            title: "Berhasil!",
            text: "{{ session('success') }}",
            icon: "success",
            confirmButtonText: "OK"
        });
    @endif
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".delete-btn").forEach(button => {
            button.addEventListener("click", function () {
                let form = this.closest(".delete-form");
                let inventoryId = form.getAttribute("data-id");

                Swal.fire({
                    title: "Apakah Anda yakin?",
                    text: "Data inventory ini akan dihapus!",
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
    });
</script>

@endpush
