@extends('layouts.app')

@section('title', 'Peminjaman Barang')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Peminjaman Barang</h4>
            <p class="text-muted small mb-0">Riwayat & status peminjaman barang</p>
        </div>
        @auth
        @if(auth()->user()->hasRole(['admin', 'staff']))
        <a href="{{ route('borrowings.create') }}" class="btn btn-danger" style="background-color:#e2001a;border-color:#e2001a;">
            <i class="bi bi-plus-lg"></i> Tambah Peminjaman
        </a>
        @endif
        @endauth
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2">
                <div class="col-md-4">
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama peminjam...">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                        <option value="dikembalikan" {{ request('status') == 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                        <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-secondary w-100" type="submit"><i class="bi bi-search"></i> Cari</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead >
                    <tr>
                        <th>Peminjam</th>
                        <th>Barang</th>
                        <th>Tgl Pinjam</th>
                        <th>Tgl Kembali</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($borrowings as $borrowing)
                        <tr>
                            <td>{{ $borrowing->borrower_name }}</td>
                            <td>
                                @foreach($borrowing->details as $detail)
                                    <div class="small">{{ $detail->product->name ?? '-' }} <span class="text-muted">x{{ $detail->quantity }}</span></div>
                                @endforeach
                            </td>
                            <td>{{ $borrowing->borrow_date->translatedFormat('d M Y') }}</td>
                            <td>{{ $borrowing->return_date?->translatedFormat('d M Y') ?? '-' }}</td>
                            <td><span class="badge text-bg-{{ $borrowing->statusBadgeColor() }} text-uppercase">{{ $borrowing->status }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('borrowings.show', $borrowing) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
                                @auth
                                @if(auth()->user()->hasRole(['admin', 'staff']) && $borrowing->status !== 'dikembalikan')
                                <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#returnModal{{ $borrowing->id }}">
                                    <i class="bi bi-box-arrow-in-down"></i> Kembalikan
                                </button>
                                @endif
                                @endauth
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data peminjaman.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-transparent">
            <div class="d-flex justify-content-end">
                {{ $borrowings->links() }}
            </div>
        </div>
    </div>

    @foreach($borrowings as $borrowing)
        @if($borrowing->status !== 'dikembalikan')
        <div class="modal fade" id="returnModal{{ $borrowing->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="{{ route('borrowings.return', $borrowing) }}">
                        @csrf @method('PATCH')
                        <div class="modal-header">
                            <h5 class="modal-title">Pengembalian Barang - {{ $borrowing->borrower_name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Kembali</label>
                                <input type="date" name="return_date" value="{{ now()->format('Y-m-d') }}" class="form-control" required>
                            </div>
                            @foreach($borrowing->details as $detail)
                                <div class="mb-2">
                                    <label class="form-label small">Kondisi saat kembali - {{ $detail->product->name ?? '-' }}</label>
                                    <select name="conditions[{{ $detail->id }}]" class="form-select form-select-sm">
                                        <option value="baik">Baik</option>
                                        <option value="rusak_ringan">Rusak Ringan</option>
                                        <option value="rusak_berat">Rusak Berat</option>
                                    </select>
                                </div>
                            @endforeach
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Konfirmasi Pengembalian</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    @endforeach
@endsection
