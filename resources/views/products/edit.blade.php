@extends('layouts.app')

@section('title', 'Edit Barang')

@section('content')
    <div class="mb-4">
        <h4 class="fw-bold mb-0">Edit Barang</h4>
        <p class="text-muted small mb-0">{{ $product->name }}</p>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('products._form')
                <div class="mt-3">
                    <button type="submit" class="btn btn-danger" style="background-color:#e2001a;border-color:#e2001a;">Simpan Perubahan</button>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
