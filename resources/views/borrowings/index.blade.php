@extends('layouts.app')

@section('title', 'Peminjaman Barang')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-md-items-center gap-2 mb-4">
        <div>
            <h4 class="fw-bold mb-0">Peminjaman Barang</h4>
            <p class="text-muted small mb-0">Riwayat & status peminjaman barang</p>
        </div>
        @auth
            @if (auth()->user()->hasRole(['admin', 'staff']))
                <a href="{{ route('borrowings.create') }}" class="btn btn-danger"
                    style="background-color:#e2001a;border-color:#e2001a;">
                    <i class="bi bi-plus-lg"></i> <span class="d-none d-sm-inline">Tambah</span> Peminjaman
                </a>
            @endif
        @endauth
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2">
                <div class="col-12 col-sm-6 col-lg-4">
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm"
                        placeholder="Cari nama peminjam...">
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                        <option value="dikembalikan" {{ request('status') == 'dikembalikan' ? 'selected' : '' }}>
                            Dikembalikan</option>
                        <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat
                        </option>
                    </select>
                </div>
                <div class="col-12 col-lg-2">
                    <button class="btn btn-outline-secondary w-100 btn-sm" type="submit"><i class="bi bi-search"></i> <span
                            class="d-none d-sm-inline">Cari</span></button>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 table-sm">
                <thead >
                    <tr>
                        <th class="text-nowrap">Peminjam</th>
                        <th class="text-nowrap d-none d-md-table-cell">Barang</th>
                        <th class="text-nowrap d-none d-lg-table-cell">Tgl Pinjam</th>
                        <th class="text-nowrap d-none d-lg-table-cell">Tgl Kembali</th>
                        <th class="text-nowrap">Status</th>
                        <th class="text-center text-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($borrowings as $borrowing)
                        <tr>
                            <td class="fw-semibold small">
                                {{ $borrowing->borrower_name }}
                                <br>
                                <span class="text-muted d-md-none small">
                                    @foreach ($borrowing->details->take(1) as $detail)
                                        {{ $detail->product->name ?? '-' }}
                                    @endforeach
                                    @if ($borrowing->details->count() > 1)
                                        <br>+{{ $borrowing->details->count() - 1 }} lainnya
                                    @endif
                                </span>
                            </td>
                            <td class="d-none d-md-table-cell small">
                                @foreach ($borrowing->details as $detail)
                                    <div class="text-nowrap"><small>{{ $detail->product->name ?? '-' }} <span
                                                class="text-muted">(x{{ $detail->quantity }})</span></small></div>
                                @endforeach
                            </td>
                            <td class="d-none d-lg-table-cell text-nowrap small">
                                {{ $borrowing->borrow_date->translatedFormat('d M Y') }}</td>
                            <td class="d-none d-lg-table-cell text-nowrap small">
                                {{ $borrowing->return_date?->translatedFormat('d M Y') ?? '-' }}</td>
                            <td class="text-nowrap">
                                <span class="badge text-bg-{{ $borrowing->statusBadgeColor() }} text-uppercase"
                                    style="font-size: 0.75rem;">{{ $borrowing->status }}</span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('borrowings.show', $borrowing) }}" class="btn btn-outline-secondary"
                                        title="Lihat"><i class="bi bi-eye"></i></a>
                                    @auth
                                        @if (auth()->user()->hasRole(['admin', 'staff']) && $borrowing->status !== 'dikembalikan')
                                            <button class="btn btn-outline-success" data-bs-toggle="modal"
                                                data-bs-target="#returnModal{{ $borrowing->id }}" title="Kembalikan">
                                                <i class="bi bi-box-arrow-in-down"></i>
                                            </button>
                                        @endif
                                    @endauth
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4"><i class="bi bi-inbox"></i> Belum ada
                                data peminjaman.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="card-footer bg-transparent">
            <div class="d-flex justify-content-end">
                {{ $borrowings->links() }}
            </div>
        </div>
    </div>

    <!-- Return Modal -->
    @foreach ($borrowings as $borrowing)
        @if ($borrowing->status !== 'dikembalikan')
            <div class="modal fade" id="returnModal{{ $borrowing->id }}" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form method="POST" action="{{ route('borrowings.return', $borrowing) }}">
                            @csrf @method('PATCH')
                            <div class="modal-header">
                                <h5 class="modal-title">Pengembalian Barang</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p class="text-muted small mb-3"><strong>Peminjam:</strong> {{ $borrowing->borrower_name }}
                                </p>
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Kembali</label>
                                    <input type="date" name="return_date" value="{{ now()->format('Y-m-d') }}"
                                        class="form-control" required>
                                </div>
                                <label class="form-label">Kondisi Barang Saat Dikembalikan</label>
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
    @endforeach
@endsection
