@extends('auth.layout')
@section('title', 'Masuk')

@section('content')
    <h1 class="title">Masuk Akun</h1>
    <p class="sub">Selamat datang di Mathventure! Masuk dulu yuk, petualangan matematika sudah menunggu.</p>

    <form method="POST" action="/login">
        @csrf
        <div class="field">
            <input type="text" name="username" placeholder="Username"
                   value="{{ old('username') }}" required autofocus>
            @error('username') <p class="error">{{ $message }}</p> @enderror
        </div>

        <div class="field">
            <input type="password" name="password" placeholder="Password" required>
        </div>

        <div class="row">
            <label><input type="checkbox" name="remember"> Ingatkan saya</label>
            <a href="{{ route('register') }}">Belum punya akun?</a>
        </div>

        <button type="submit">Masuk</button>
    </form>
@endsection