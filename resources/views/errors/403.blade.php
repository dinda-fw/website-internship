@extends('layouts.guest')

@section('title', 'Akses Ditolak')

@section('content')
    <div class="text-center">
        <i class="bi bi-shield-lock" style="font-size:3rem;color:#e2001a;"></i>
        <h5 class="fw-bold mt-3">Akses Ditolak</h5>
        <p class="text-muted small">{{ $exception->getMessage() ?: 'Anda tidak memiliki izin untuk mengakses halaman ini.' }}</p>
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
    </div>
@endsection
