@extends('layouts.default')

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Daftar Assesment Aspect</h5>
        <p>Menu "Assesment Aspect" memungkinkan admin untuk mengelola, memantau, dan memperbarui informasi assesment aspect secara efisien</p>

        <a href="{{ route('assesment-aspect.create') }}" class="btn btn-success btn-sm mb-4">
            <i class="fas fa-plus"></i> Tambah
        </a>

        <table id="zero-conf" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Category</th>
                    <th>Aspek</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $index => $category)
                    <tr>
                        <td rowspan="{{ max(1, $category->aspects->count()) }}">{{ $index + 1 }}</td>
                        <td rowspan="{{ max(1, $category->aspects->count()) }}">{{ $category->name }}</td>

                        @if ($category->aspects->isEmpty())
                            <td colspan="2">Tidak ada aspek</td>
                        @else
                            @foreach ($category->aspects as $key => $aspect)
                                @if ($key > 0) <tr> @endif
                                    <td>{{ $aspect->name }}</td>
                                    <td class="d-flex">
                                        <a href="{{ route('assesment-aspect.edit', $aspect->id) }}" class="btn btn-warning btn-sm me-2">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <form action="{{ route('assesment-aspect.destroy', $aspect->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                @if ($key > 0) </tr> @endif
                            @endforeach
                        @endif
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>No</th>
                    <th>Category</th>
                    <th>Aspek</th>
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

@push('custom-script')
<!-- Javascripts -->
<script src="{{ asset('assets/plugins/jquery/jquery-3.4.1.min.js') }}"></script>
<script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="https://unpkg.com/feather-icons"></script>
<script src="{{ asset('assets/plugins/perfectscroll/perfect-scrollbar.min.js') }}"></script>
<script src="{{ asset('assets/plugins/DataTables/datatables.min.js') }}"></script>
<script src="{{ asset('assets/js/main.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/datatables.js') }}"></script>

<script>
    // Inisialisasi DataTables
    $(document).ready(function() {
        if ($.fn.DataTable.isDataTable('#zero-conf')) {
            $('#zero-conf').DataTable().clear().destroy();
        }
        $('#zero-conf').DataTable();
    });
</script>
@endpush
