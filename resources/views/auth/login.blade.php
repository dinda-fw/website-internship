@extends('layouts.guest')

@section('title', 'Login')

@section('content')
    @if(session('status'))
        <div class="alert alert-success small">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login.attempt') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required autofocus>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label small" for="remember">Ingat saya</label>
            </div>
            <a href="{{ route('password.request') }}" class="small">Lupa password?</a>
        </div>

        <button type="submit" class="btn btn-danger w-100" style="background-color:#e2001a;border-color:#e2001a;">Login</button>

        <p class="text-center small text-muted mt-3 mb-0">
            Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a>
        </p>

        <hr>
    </form>
@endsection
