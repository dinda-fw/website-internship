@extends('layouts.app')

@section('title', 'Master Barang')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-md-items-center gap-2 mb-4">
        <div>
            <h4 class="fw-bold mb-0">Master Data Barang</h4>
            <p class="text-muted small mb-0">Kelola data inventaris kantor</p>
        </div>
        @auth
            @if (auth()->user()->hasRole(['admin', 'staff']))
                <a href="{{ route('products.create') }}" class="btn btn-danger"
                    style="background-color:#e2001a;border-color:#e2001a;">
                    <i class="bi bi-plus-lg"></i> <span class="d-none d-sm-inline">Tambah</span> Barang
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
                        placeholder="Cari nama, kode, atau lokasi...">
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <select name="category_id" class="form-select form-select-sm">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <select name="condition" class="form-select form-select-sm">
                        <option value="">Semua Kondisi</option>
                        <option value="baik" {{ request('condition') == 'baik' ? 'selected' : '' }}>Baik</option>
                        <option value="rusak_ringan" {{ request('condition') == 'rusak_ringan' ? 'selected' : '' }}>Rusak
                            Ringan</option>
                        <option value="rusak_berat" {{ request('condition') == 'rusak_berat' ? 'selected' : '' }}>Rusak
                            Berat</option>
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
                        <th class="text-nowrap">Barang</th>
                        <th class="text-nowrap d-none d-md-table-cell">Kode</th>
                        <th class="text-nowrap d-none d-lg-table-cell">Kategori</th>
                        <th class="text-center text-nowrap" style="min-width: 130px;">Stok</th>
                        <th class="text-nowrap d-none d-lg-table-cell">Lokasi</th>
                        <th class="text-nowrap d-none d-md-table-cell">Kondisi</th>
                        <th class="text-center text-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if ($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" class="table-thumb"
                                            alt="{{ $product->name }}">
                                    @else
                                        <div class="table-thumb bg-secondary-subtle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-box-seam text-muted"></i>
                                        </div>
                                    @endif
                                    <div class="flex-grow-1">
                                        <span class="fw-semibold small d-block">{{ $product->name }}</span>
                                        <span
                                            class="text-muted small d-md-none">{{ $product->category->name ?? '-' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell text-nowrap"><code
                                    style="font-size: 0.75rem;">{{ $product->code }}</code></td>
                            <td class="d-none d-lg-table-cell small">{{ $product->category->name ?? '-' }}</td>
                            <td class="text-center text-nowrap">
                                <span class="fw-semibold small">{{ $product->stock }}</span>
                                <span class="text-muted small">/ {{ $product->total_stock }}</span>
                            </td>
                            <td class="d-none d-lg-table-cell small">{{ $product->location ?? '-' }}</td>
                            <td class="d-none d-md-table-cell text-nowrap">
                                <span
                                    class="badge text-bg-{{ $product->condition == 'baik' ? 'success' : ($product->condition == 'rusak_ringan' ? 'warning' : 'danger') }}"
                                    style="font-size: 0.75rem;">
                                    {{ ucwords(str_replace('_', ' ', $product->condition)) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('products.show', $product) }}" class="btn btn-outline-secondary"
                                        title="Lihat"><i class="bi bi-eye"></i></a>
                                    @auth
                                        @if (auth()->user()->hasRole(['admin', 'staff']))
                                            <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-primary"
                                                title="Edit"><i class="bi bi-pencil"></i></a>
                                            <form action="{{ route('products.destroy', $product) }}" method="POST"
                                                class="d-inline" onsubmit="return confirm('Hapus barang ini?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" type="button"
                                                    onclick="if(confirm('Hapus barang ini?')) this.form.submit();"
                                                    title="Hapus"><i class="bi bi-trash"></i></button>
                                            </form>
                                        @endif
                                    @endauth
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4"><i class="bi bi-inbox"></i> Tidak ada
                                data barang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-transparent">
            <div class="d-flex justify-content-end">
                {{ $products->links() }}
            </div>
        </div>
    </div>
@endsection
