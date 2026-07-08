@extends('layouts.app')

@section('title', 'Detail Peminjaman')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Detail Peminjaman</h4>
            <p class="text-muted small mb-0">{{ $borrowing->borrower_name }}</p>
        </div>
        <a href="{{ route('borrowings.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <tr><th width="200" class="text-nowrap">Nama Peminjam</th><td class="text-nowrap">{{ $borrowing->borrower_name }}</td></tr>
                    <tr><th class="text-nowrap">Dicatat Oleh</th><td class="text-nowrap">{{ $borrowing->user->name ?? '-' }}</td></tr>
                    <tr><th class="text-nowrap">Tanggal Pinjam</th><td class="text-nowrap">{{ $borrowing->borrow_date->translatedFormat('d M Y') }}</td></tr>
                    <tr><th class="text-nowrap">Batas Kembali</th><td class="text-nowrap">{{ $borrowing->due_date?->translatedFormat('d M Y') ?? '-' }}</td></tr>
                    <tr><th class="text-nowrap">Tanggal Kembali</th><td class="text-nowrap">{{ $borrowing->return_date?->translatedFormat('d M Y') ?? '-' }}</td></tr>
                    <tr><th class="text-nowrap">Status</th><td><span class="badge text-bg-{{ $borrowing->statusBadgeColor() }} text-uppercase">{{ $borrowing->status }}</span></td></tr>
                    <tr><th class="text-nowrap">Catatan</th><td>{{ $borrowing->notes ?? '-' }}</td></tr>
                </table>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h6 class="fw-bold mb-3">Barang yang Dipinjam</h6>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead><tr><th class="text-nowrap">Barang</th><th class="text-nowrap">Jumlah</th><th class="text-nowrap">Kondisi Saat Kembali</th></tr></thead>
                    <tbody>
                        @foreach($borrowing->details as $detail)
                            <tr>
                                <td class="text-nowrap">{{ $detail->product->name ?? '-' }}</td>
                                <td>{{ $detail->quantity }}</td>
                                <td class="text-nowrap">{{ $detail->condition_on_return ? ucwords(str_replace('_',' ', $detail->condition_on_return)) : '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
