@extends('layouts.app')

@section('title', 'Detail Peminjaman')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-md-items-center gap-2 mb-4">
        <div>
            <h4 class="fw-bold mb-0">Detail Peminjaman</h4>
            <p class="text-muted small mb-0">{{ $borrowing->borrower_name }}</p>
        </div>
        <a href="{{ route('borrowings.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i>
            Kembali</a>
    </div>

    <div class="row g-3">
        <!-- Informasi Peminjaman -->
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-transparent border-bottom">
                    <h6 class="fw-bold mb-0">Informasi Peminjaman</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-12 col-sm-6">
                            <label class="text-muted small">Nama Peminjam</label>
                            <p class="fw-semibold mb-0">{{ $borrowing->borrower_name }}</p>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="text-muted small">Dicatat Oleh</label>
                            <p class="fw-semibold mb-0">{{ $borrowing->user->name ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12 col-sm-6">
                            <label class="text-muted small">Tanggal Pinjam</label>
                            <p class="fw-semibold mb-0">{{ $borrowing->borrow_date->translatedFormat('d F Y') }}</p>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="text-muted small">Batas Kembali</label>
                            <p class="fw-semibold mb-0">{{ $borrowing->due_date?->translatedFormat('d F Y') ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <label class="text-muted small">Status</label>
                            <p class="mb-0">
                                <span
                                    class="badge text-bg-{{ $borrowing->statusBadgeColor() }} text-uppercase">{{ $borrowing->status }}</span>
                            </p>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="text-muted small">Tgl Dikembalikan</label>
                            <p class="fw-semibold mb-0">{{ $borrowing->return_date?->translatedFormat('d F Y') ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Barang -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom">
                    <h6 class="fw-bold mb-0">Detail Barang yang Dipinjam</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 table-sm">
                        <thead >
                            <tr>
                                <th class="text-nowrap">Barang</th>
                                <th class="text-center text-nowrap">Jumlah</th>
                                <th class="text-center text-nowrap d-none d-md-table-cell">Kondisi Saat Kembali</th>
                                <th class="text-center text-nowrap d-none d-lg-table-cell">Tgl Kembali</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($borrowing->details as $detail)
                                <tr>
                                    <td class="fw-semibold small">
                                        {{ $detail->product->name ?? '-' }}
                                        <br>
                                        <span class="text-muted small">Kategori:
                                            {{ $detail->product->category->name ?? '-' }}</span>
                                    </td>
                                    <td class="text-center">{{ $detail->quantity }}</td>
                                    <td class="text-center d-none d-md-table-cell text-nowrap">
                                        <small>
                                            @if ($detail->condition_on_return)
                                                <span
                                                    class="badge text-bg-
                                                    @if ($detail->condition_on_return === 'baik') success
                                                    @elseif($detail->condition_on_return === 'rusak_ringan') warning
                                                    @else danger @endif
                                                ">
                                                    {{ ucfirst(str_replace('_', ' ', $detail->condition_on_return)) }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </small>
                                    </td>
                                    <td class="text-center d-none d-lg-table-cell text-nowrap small">
                                        {{ $detail->returned_at?->translatedFormat('d M Y') ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Tidak ada barang</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="col-12 col-lg-4">
            <!-- Catatan -->
            @if ($borrowing->notes)
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-transparent border-bottom">
                        <h6 class="fw-bold mb-0"><i class="bi bi-sticky"></i> Catatan</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0 small">{{ $borrowing->notes }}</p>
                    </div>
                </div>
            @endif

            <!-- Aksi -->
            @if ($borrowing->status !== 'dikembalikan')
                <div class="card border-0 shadow-sm border-warning">
                    <div class="card-header bg-warning bg-opacity-10 border-warning">
                        <h6 class="fw-bold mb-0"><i class="bi bi-exclamation-triangle"></i> Pending Pengembalian</h6>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-2">Barang masih dalam status dipinjam. Silakan proses pengembalian.
                        </p>
                        @auth
                            @if (auth()->user()->hasRole(['admin', 'staff']))
                                <button class="btn btn-success w-100 btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#returnModal">
                                    <i class="bi bi-box-arrow-in-down"></i> Proses Pengembalian
                                </button>
                            @endif
                        @endauth
                    </div>
                </div>
            @else
                <div class="card border-0 shadow-sm border-success">
                    <div class="card-header bg-success bg-opacity-10 border-success">
                        <h6 class="fw-bold mb-0"><i class="bi bi-check-circle"></i> Sudah Dikembalikan</h6>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-0">Peminjaman ini telah selesai pada tanggal
                            <strong>{{ $borrowing->return_date?->translatedFormat('d F Y') }}</strong></p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Return Modal -->
    @if ($borrowing->status !== 'dikembalikan')
        <div class="modal fade" id="returnModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST" action="{{ route('borrowings.return', $borrowing) }}">
                        @csrf @method('PATCH')
                        <div class="modal-header">
                            <h5 class="modal-title">Pengembalian Barang</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Kembali <span class="text-danger">*</span></label>
                                <input type="date" name="return_date" value="{{ now()->format('Y-m-d') }}"
                                    class="form-control" required>
                            </div>
                            <label class="form-label mb-2">Kondisi Barang Saat Dikembalikan</label>
                            @forelse($borrowing->details as $detail)
                                <div class="mb-3">
                                    <small class="text-muted d-block mb-2">{{ $detail->product->name }}
                                        (x{{ $detail->quantity }})</small>
                                    <select name="conditions[{{ $detail->id }}]" class="form-select form-select-sm">
                                        <option value="baik">Baik</option>
                                        <option value="rusak_ringan">Rusak Ringan</option>
                                        <option value="rusak_berat">Rusak Berat</option>
                                    </select>
                                </div>
                            @empty
                                <p class="text-muted small">Tidak ada barang untuk dikembalikan.</p>
                            @endforelse
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary btn-sm"
                                data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="bi bi-check-circle"></i> Proses Pengembalian
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection
