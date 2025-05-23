@extends('layouts.default')

@section('content')
<div class="card">
    <div class="card-body">
        <form>
            <div class="row mb-4">
                <div class="col-6 mb-3">
                    <label class="form-label">package</label>
                    <input type="text" class="form-control" disabled value="{{ $schedule->package->name ?? '-' }}">
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label">Tanggal</label>
                    <input type="text" class="form-control" disabled value="{{ $schedule->date }}">
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label">Lokasi</label>
                    <input type="text" class="form-control" disabled value="{{ $schedule->location->name ?? '-' }}">
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label">Pelatih</label>
                    <input type="text" class="form-control" disabled value="{{ $schedule->coach->name ?? '-' }}">
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label">Waktu</label>
                    <input type="text" class="form-control" disabled
                           value="{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} -
                                  {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}">
                </div>
                <div class="col-6 mb-3">
                    <label for="is_assessed" class="form-label">Penilaian</label>
                    <div class="form-check">
                        <input class="form-check-input" disabled type="checkbox" name="is_assessed" id="is_assessed" value="1"
                            {{ old('is_assessed', $schedule->is_assessed ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_assessed">
                            Apakah ada penilaian?
                        </label>
                    </div>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label">Peserta</label>
                    @if ($schedule->students->isEmpty())
                        <p class="text-muted">Tidak ada peserta</p>
                    @else
                        <ul class="list-group">
                            @foreach ($schedule->students as $student)
                                <li class="list-group-item">{{ $student->name }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>


            </div>
            <a href="{{ route('schedule.index') }}" class="btn btn-warning">Kembali</a>
        </form>
    </div>
</div>
@endsection
