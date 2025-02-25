@extends('layouts.default')

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Assesment Category</h5>
        <p>Menu "Assesment Category" memungkinkan admin untuk mengelola, memantau, dan memperbarui informasi Assesment Category secara efisien</p>

        <a href="{{route('assesment-category.create')}}" class="btn btn-success btn-sm mb-4">
            <i class="fas fa-plus"></i> Tambah
        </a>
        <table id="zero-conf" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>aksi</th>
                </tr>
            </thead>
            <tbody>
                    @foreach ($categories as $item)

                    <tr>
                        <td>{{$loop->iteration}}</td>
                        <td>{{$item -> name}}</td>
                        <td class="d-flex">
                            <a href="{{route('assesment-category.edit', $item->id)}}" class="btn btn-warning btn-sm ">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{route('assesment-category.show', $item->id)}}" class="btn btn-info btn-sm mx-2">
                                <i class="fas fa-eye"></i>
                            </a>
                            <form action="{{route('assesment-category.destroy', $item->id)}}" method="POST" style="display:inline;">
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


