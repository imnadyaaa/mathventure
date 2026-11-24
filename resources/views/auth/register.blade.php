@extends('auth.layout')
@section('title', 'Daftar')

@section('content')
    <h1 class="title">Buat Akun</h1>
    <p class="sub">Daftar sekarang dan mulai belajar matematika sambil bermain!</p>

    <form method="POST" action="/register">
        @csrf
            @if ($errors->any())
        <div class="error" style="margin-bottom:14px">
            @foreach ($errors->all() as $e)
                <p>• {{ $e }}</p>
            @endforeach
        </div>
        @endif
        <div class="field">
            <input type="text" name="name" placeholder="Nama lengkap"
                   value="{{ old('name') }}" required autofocus>
            @error('name') <p class="error">{{ $message }}</p> @enderror
        </div>

        <div class="field">
            <input type="text" name="username" placeholder="Username"
                   value="{{ old('username') }}" required>
            @error('username') <p class="error">{{ $message }}</p> @enderror
        </div>

        <div class="field">
            <input type="password" name="password" placeholder="Password" required>
            @error('password') <p class="error">{{ $message }}</p> @enderror
        </div>

        <div class="field">
            <input type="password" name="password_confirmation" placeholder="Ulangi password" required>
        </div>

        <div style="height: 14px"></div>
        <button type="submit">Daftar</button>
    </form>

    <p class="alt">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>
@endsection