@extends('layouts.default')

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">{{ $announcement->title }}</h5>

        <p><strong>Konten:</strong></p>
        <p>{{ $announcement->content }}</p>

        <p><strong>Tanggal Publikasi:</strong> {{ \Carbon\Carbon::parse($announcement->published_at)->format('d M Y H:i') }}</p>

        @if ($announcement->image)
            <p><strong>Gambar:</strong></p>
            <img src="{{ asset('storage/' . $announcement->image) }}" alt="Image" width="150">
        @endif

        <p><strong>Ditunjukkan Kepada :</strong></p>
        <ul>
            @foreach ($announcement->users as $user)
                <li>{{ $user->name }}</li>
            @endforeach
        </ul>

        <a href="{{ route('announcement.edit', $announcement->id) }}" class="btn btn-warning">Edit</a>
        <form action="{{ route('announcement.destroy', $announcement->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this announcement?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Delete</button>
        </form>
    </div>
</div>
@endsection
