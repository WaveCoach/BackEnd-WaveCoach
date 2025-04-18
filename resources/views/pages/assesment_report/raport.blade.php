<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Raport Renang - Gaya Bebas</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            margin: 30px;
            color: #333;
        }
        h2, h3 {
            text-align: center;
            margin-bottom: 10px;
        }
        table.meta {
            margin-bottom: 20px;
            width: 100%;
            font-size: 14px;
        }
        table.meta td {
            padding: 5px;
            vertical-align: top;
        }
        table.penilaian {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-bottom: 30px;
        }
        table.penilaian th, table.penilaian td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        table.penilaian th {
            background-color: #f0f0f0;
        }
        .footer {
            margin-top: 40px;
        }
    </style>
</head>
<body>

    <h2>Laporan Penilaian Renang</h2>
    <h3>{{$assessment->category->name}}</h3>

    <table class="meta">
        <tr>
            <td><strong>Nama Siswa</strong></td>
            <td>: {{$assessment->student->name}}</td>
            <td><strong>Kelas</strong></td>
            <td>: {{$assessment->package->name}}</td>
        </tr>
        <tr>
            <td><strong>ID Siswa</strong></td>
            <td>: {{$student->nis}}</td>
            <td><strong>Tanggal Penilaian</strong></td>
            <td>: {{$assessment->created_at->format('d F Y')}}</td>
        </tr>
        <tr>
            <td><strong>Pelatih</strong></td>
            <td>: {{$assessment->coach->name}}</td>
            <td><strong>Lokasi</strong></td>
            <td>: {{ $assessment->schedule->location->name ?? '-' }} </td>
        </tr>
    </table>

    <table class="penilaian">
        <thead>
            <tr>
                <th>No</th>
                <th>Aspek Penilaian</th>
                <th>Deskripsi</th>
                <th>Nilai</th>
                <th>Komentar</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($nilai as $item)
            <tr>
                <td>{{$loop->iteration}}</td>
                <td>{{$item->aspect->name}}</td>
                <td>{{$item->aspect->desc}}</td>
                <td>{{$item->score}}</td>
                <td>{{$item->remarks}}</td>
            </tr>
            @endforeach


        </tbody>
    </table>

    <div class="footer">
        <strong>Catatan Pelatih:</strong>
        <p>
            Siswa telah menunjukkan usaha yang baik selama sesi pelatihan. Diharapkan terus konsisten dan semangat dalam latihan ke depannya.
        </p>

        <p style="margin-top: 50px;">Tanda Tangan Pelatih: _______________________</p>
    </div>

</body>
</html>
