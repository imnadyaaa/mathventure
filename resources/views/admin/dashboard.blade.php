@extends('admin.layout')
@section('title', 'Ringkasan')

@section('content')
    <h1>Ringkasan</h1>
    <p class="sub">Gambaran singkat isi dan aktivitas aplikasi.</p>

    <div class="grid">
        <div class="stat"><b>{{ $jumlahSiswa }}</b><span>Siswa terdaftar</span></div>
        <div class="stat"><b>{{ $jumlahSoal }}</b><span>Soal</span></div>
        <div class="stat"><b>{{ $jumlahMateri }}</b><span>Materi</span></div>
        <div class="stat"><b>{{ $jumlahLapor }}</b><span>Pesan chat dilaporkan</span></div>
        <div class="stat"><b>{{ $jumlahBisu }}</b><span>Siswa sedang dibisukan</span></div>
    </div>

    <div class="card">
        <strong>Mau ngapain?</strong>
        <div class="row" style="margin-top:12px">
            <a class="btn" href="{{ route('admin.levels') }}">Kelola soal &amp; materi</a>
            <a class="btn" href="{{ route('admin.students.index') }}">Lihat siswa</a>
            <a class="btn" href="{{ route('admin.chat.index') }}">Cek laporan chat</a>
        </div>
    </div>
@endsection
