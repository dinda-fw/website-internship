@extends('layouts.guest')

@section('title', 'Lupa Password')

@section('content')
    <p class="text-muted small mb-3">Masukkan email Anda. Kami akan mengirimkan link untuk mereset password.</p>

    @if(session('status'))
        <div class="alert alert-success small">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required autofocus>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <button type="submit" class="btn btn-danger w-100" style="background-color:#e2001a;border-color:#e2001a;">Kirim Link Reset Password</button>

        <p class="text-center small text-muted mt-3 mb-0">
            <a href="{{ route('login') }}"><i class="bi bi-arrow-left"></i> Kembali ke login</a>
        </p>
    </form>
@endsection
