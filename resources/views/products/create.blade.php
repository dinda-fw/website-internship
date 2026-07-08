@extends('layouts.app')

@section('title', 'Tambah Barang')

@section('content')
    <div class="mb-4">
        <div class="d-flex align-items-center gap-2 mb-2">
            <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-secondary"><i
                    class="bi bi-arrow-left"></i></a>
            <h4 class="fw-bold mb-0">Tambah Barang Baru</h4>
        </div>
        <p class="text-muted small mb-0">Isi form di bawah untuk menambahkan barang ke dalam sistem inventaris</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i> <strong>Validasi Gagal!</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" id="productForm">
                @csrf

                @include('products._form')

                <div class="mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-danger" style="background-color:#e2001a;border-color:#e2001a;">
                        <i class="bi bi-check-circle"></i> Simpan Barang
                    </button>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
