@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-md-items-center gap-2 mb-4">
        <div>
            <h4 class="fw-bold mb-0">Dashboard</h4>
            <p class="text-muted small mb-0">Ringkasan inventaris kantor - PT Telkomsel</p>
        </div>
    </div>

    {{-- Notifikasi stok menipis (bonus fitur) --}}
    @if ($lowStockProducts->isNotEmpty())
        <div class="alert alert-warning d-flex align-items-start gap-2">
            <i class="bi bi-exclamation-triangle-fill mt-1 flex-shrink-0"></i>
            <div class="flex-grow-1">
                <strong>Peringatan Stok Menipis!</strong>
                Ada {{ $lowStockProducts->count() }} barang dengan stok &le; {{ $lowStockThreshold }}:
                <span class="d-block mt-1 flex-wrap">
                    @foreach ($lowStockProducts->take(6) as $item)
                        <span class="badge text-bg-warning text-dark me-1 mb-1">{{ $item->name }}
                            ({{ $item->stock }})</span>
                    @endforeach
                    @if ($lowStockProducts->count() > 6)
                        <span class="text-muted small">+{{ $lowStockProducts->count() - 6 }} lainnya</span>
                    @endif
                </span>
            </div>
        </div>
    @endif

    <!-- Stat Cards -->
    <div class="row g-2 g-md-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="stat-card bg-blue">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="small opacity-75">Total Barang</div>
                        <div class="fs-4 fs-md-3 fw-bold">{{ number_format($totalBarang) }}</div>
                        <div class="small opacity-75">{{ $totalJenisBarang }} jenis</div>
                    </div>
                    <i class="bi bi-box-seam stat-icon flex-shrink-0"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card bg-green">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="small opacity-75">Tersedia</div>
                        <div class="fs-4 fs-md-3 fw-bold">{{ number_format($barangTersedia) }}</div>
                    </div>
                    <i class="bi bi-check-circle stat-icon flex-shrink-0"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card bg-orange">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="small opacity-75">Dipinjam</div>
                        <div class="fs-4 fs-md-3 fw-bold">{{ number_format($barangDipinjam) }}</div>
                    </div>
                    <i class="bi bi-arrow-left-right stat-icon flex-shrink-0"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card bg-red">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="small opacity-75">Stok Menipis</div>
                        <div class="fs-4 fs-md-3 fw-bold">{{ $lowStockProducts->count() }}</div>
                    </div>
                    <i class="bi bi-exclamation-triangle stat-icon flex-shrink-0"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts & Recent Borrowings -->
    <!-- Charts & Recent Borrowings -->
    <div class="row g-3">
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom">
                    <h6 class="fw-bold mb-0 text-danger">Grafik Peminjaman per Bulan ({{ $chartYear }})</h6>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 300px;">
                        <canvas id="borrowingChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom">
                    <h6 class="fw-bold mb-0 text-danger"><i class="bi bi-clock-history"></i> Peminjaman Terbaru</h6>
                </div>
                <div class="card-body p-0">
                    <div style="max-height: 350px; overflow-y: auto;">
                        @forelse($recentBorrowings as $borrowing)
                            <div class="d-flex justify-content-between align-items-center border-bottom px-3 py-2">
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-semibold small text-truncate">{{ $borrowing->borrower_name }}</div>
                                    <div class="text-muted small">{{ $borrowing->borrow_date->translatedFormat('d M Y') }}
                                    </div>
                                </div>
                                <span
                                    class="badge text-bg-{{ $borrowing->statusBadgeColor() }} text-uppercase flex-shrink-0 ms-2"
                                    style="font-size: 0.7rem;">{{ $borrowing->status }}</span>
                            </div>
                        @empty
                            <p class="text-muted small mb-0 p-3">Belum ada data peminjaman.</p>
                        @endforelse
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top">
                    <a href="{{ route('borrowings.index') }}" class="btn btn-sm btn-outline-danger w-100"><i
                            class="bi bi-arrow-right"></i> Lihat Semua</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script>
        const ctx = document.getElementById('borrowingChart');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [{
                    label: 'Jumlah Peminjaman',
                    data: @json($monthlyBorrowings),
                    backgroundColor: '#e2001a',
                    borderRadius: 6,
                    maxBarThickness: 40,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    </script>
@endpush
