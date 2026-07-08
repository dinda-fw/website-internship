@extends('layouts.app')

@section('title', 'Detail Barang')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Detail Barang</h4>
            <p class="text-muted small mb-0">{{ $product->code }}</p>
        </div>
        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    @if($product->image)
                        <img src="{{ asset('storage/'.$product->image) }}" class="img-fluid rounded mb-3" alt="{{ $product->name }}">
                    @else
                        <div class="bg-secondary-subtle rounded d-flex align-items-center justify-content-center mb-3" style="height:200px;">
                            <i class="bi bi-box-seam text-muted" style="font-size:3rem;"></i>
                        </div>
                    @endif
                    <h5 class="fw-bold mb-1">{{ $product->name }}</h5>
                    <p class="text-muted small mb-2">{{ $product->category->name ?? '-' }}</p>
                    <span class="badge text-bg-{{ $product->isAvailable() ? 'success' : 'secondary' }}">{{ $product->statusLabel() }}</span>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Informasi Barang</h6>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <tr><th width="180" class="text-nowrap">Kode Barang</th><td>{{ $product->code }}</td></tr>
                            <tr><th class="text-nowrap">Stok Tersedia</th><td>{{ $product->stock }} dari total {{ $product->total_stock }}</td></tr>
                            <tr><th class="text-nowrap">Lokasi Penyimpanan</th><td>{{ $product->location ?? '-' }}</td></tr>
                            <tr><th class="text-nowrap">Kondisi</th><td>{{ ucwords(str_replace('_', ' ', $product->condition)) }}</td></tr>
                            <tr><th class="text-nowrap">Deskripsi</th><td>{{ $product->description ?? '-' }}</td></tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Riwayat Peminjaman Barang Ini</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead><tr><th>Peminjam</th><th>Tgl Pinjam</th><th>Jumlah</th><th>Status</th></tr></thead>
                            <tbody>
                                @forelse($product->borrowingDetails as $detail)
                                    <tr>
                                        <td>{{ $detail->borrowing->borrower_name }}</td>
                                        <td>{{ $detail->borrowing->borrow_date->translatedFormat('d M Y') }}</td>
                                        <td>{{ $detail->quantity }}</td>
                                        <td><span class="badge text-bg-{{ $detail->borrowing->statusBadgeColor() }}">{{ $detail->borrowing->status }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-muted text-center">Belum pernah dipinjam.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
