@extends('layouts.default')

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('student.update', $student->id) }}" id="jobPositionForm">
            @csrf
            @method('PUT')
            <div class="row mb-4">
                <div class="col-6 mb-3">
                    <label for="name" class="form-label">Nis</label>
                    <input type="text" class="form-control" required value="{{ $student->student->nis ?? '-' }}" name="name" id="name" disabled>
                </div>
                <div class="col-6 mb-3">
                    <label for="name" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" required value="{{ $student->name }}" name="name" id="name" disabled>
                </div>
                <div class="col-6 mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="text" class="form-control" required value="{{ $student->email }}" name="email" id="email" disabled>
                </div>
                <div class="col-6 mb-3">
                    <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                    <input type="text" class="form-control" value="{{ $student->student->tanggal_lahir }}" name="usia" id="usia" disabled>
                </div>
                <div class="col-6 mb-3">
                    <label for="tanggal_lahir" class="form-label">Tanggal Bergabung</label>
                    <input type="text" class="form-control" value="{{ $student->student->tanggal_bergabung }}" name="tanggal_bergabung" id="tanggal_bergabung" disabled>
                </div>

                <div class="col-6 mb-3">
                    <label for="usia" class="form-label">Usia</label>
                    <input type="text" class="form-control"
                        value="{{ \Carbon\Carbon::parse($student->student->tanggal_lahir)->diff(\Carbon\Carbon::now())->y < 1 ? \Carbon\Carbon::parse($student->student->tanggal_lahir)->diff(\Carbon\Carbon::now())->m . ' bulan' : \Carbon\Carbon::parse($student->student->tanggal_lahir)->age . ' tahun' }}"
                        name="usia" id="usia" disabled>
                </div>
                <div class="col-12 mb-3">
                    <label for="packages" class="form-label">Paket yang Diikuti</label>
                    @if ($package->isEmpty())
                        <p class="text-muted">Belum memilih paket</p>
                    @else
                        <ul class="list-group">
                            @foreach ($package as $item)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $item->package->name }}
                                    {{-- Kalo mau tambahin aksi kayak view detail atau lain-lain, taruh di sini --}}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>


                <div class="col-6 mb-3">
                    <label class="form-label">Jenis Kelamin</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="jenis_kelamin" id="laki-laki" value="L"
                                   {{ $student->student->jenis_kelamin == 'L' ? 'checked' : '' }} disabled>
                            <label class="form-check-label" for="laki-laki">Laki-laki</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="jenis_kelamin" id="perempuan" value="P"
                                   {{ $student->student->jenis_kelamin == 'P' ? 'checked' : '' }} disabled>
                            <label class="form-check-label" for="perempuan">Perempuan</label>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Tombol Submit juga bisa dinonaktifkan jika diperlukan -->
            <a href="{{route('student.index')}}" class="btn btn-warning">Kembali</a>
        </form>
    </div>
</div>
@endsection

@push('custom-style')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.0/classic/ckeditor.js"></script>
@endpush
