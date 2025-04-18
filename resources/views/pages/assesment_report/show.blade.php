@extends('layouts.default')

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Laporan Penilaian {{$student->name ?? ''}}</h5>

        <table id="zero-conf" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>nama coach</th>
                    <th>Tanggal Penilaian</th>
                    <th>Gaya Renang</th>
                    <th>Keterangan</th>
                    <th>Dokumen</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($assesment as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->coach->name }}</td>
                    <td>{{ date('d-m-Y', strtotime($item->created_at)) }}</td>
                    <td>{{ $item->category->name }}</td>
                    <td>
                        @php
                            $average = $item->details->avg('score');
                            $kkm = $item->category->kkm;
                            $status = $average >= $kkm ? 'Lulus' : 'Tidak Lulus';
                        @endphp
                        <span>Rata-rata: {{ number_format($average, 2) }}<br>Status: <strong class="{{ $status == 'Lulus' ? 'text-success' : 'text-danger' }}">{{ $status }}</strong></span>
                    </td>
                    <td>
                        <a href="{{ route('assesment-report.pdf', $item->id) }}">pdf</a>
                        <form action="{{ route('assesment-report.send', $item->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fas fa-envelope"></i>
                            </button>
                        </form>

                    </td>
                </tr>
                @endforeach
            </tbody>

            <tfoot>
                <tr>
                    <th>No</th>
                    <th>nama coach</th>
                    <th>Tanggal Penilaian</th>
                    <th>Gaya Renang</th>
                    <th>Keterangan</th>
                    <th>Dokumen</th>
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

@push('custom-script')
<!-- Javascripts -->
<script src="{{asset('assets/plugins/jquery/jquery-3.4.1.min.js')}}"></script>
<script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="{{asset('assets/plugins/bootstrap/js/bootstrap.min.js')}}"></script>
<script src="https://unpkg.com/feather-icons"></script>
<script src="{{asset('assets/plugins/perfectscroll/perfect-scrollbar.min.js')}}"></script>
<script src="{{asset('assets/plugins/DataTables/datatables.min.js')}}"></script>
<script src="{{asset('assets/js/main.min.js')}}"></script>
<script src="{{asset('assets/js/pages/datatables.js')}}"></script>

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
