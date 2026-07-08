@extends('layouts.guest')

@section('title', 'Daftar Akun')

@section('content')
    <form method="POST" action="{{ route('register.attempt') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required autofocus>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label">No. Telepon (opsional)</label>
            <input type="text" name="phone" value="{{ old('phone') }}" class="form-control @error('phone') is-invalid @enderror">
            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>

        <div class="alert alert-info small">
            <i class="bi bi-info-circle"></i> Akun baru otomatis mendapat role <strong>Staff</strong>. Hubungi admin untuk perubahan role.
        </div>

        <button type="submit" class="btn btn-danger w-100" style="background-color:#e2001a;border-color:#e2001a;">Daftar</button>

        <p class="text-center small text-muted mt-3 mb-0">
            Sudah punya akun? <a href="{{ route('login') }}">Login di sini</a>
        </p>
    </form>
@endsection
