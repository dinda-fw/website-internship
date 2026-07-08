@extends('layouts.app')

@section('title', 'Tambah Peminjaman')

@section('content')
    <div class="mb-4">
        <div class="d-flex align-items-center gap-2 mb-2">
            <a href="{{ route('borrowings.index') }}" class="btn btn-sm btn-outline-secondary"><i
                    class="bi bi-arrow-left"></i></a>
            <h4 class="fw-bold mb-0">Catat Peminjaman Barang Baru</h4>
        </div>
        <p class="text-muted small mb-0">Lengkapi data peminjaman untuk mencatat barang yang dipinjam</p>
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
            <form method="POST" action="{{ route('borrowings.store') }}" id="borrowingForm">
                @csrf

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Peminjam</label>
                        <input type="text" name="borrower_name" value="{{ old('borrower_name') }}" class="form-control"
                            required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Pinjam</label>
                        <input type="date" name="borrow_date" value="{{ old('borrow_date', now()->format('Y-m-d')) }}"
                            class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Batas Kembali (opsional)</label>
                        <input type="date" name="due_date" value="{{ old('due_date') }}" class="form-control">
                    </div>
                </div>

                <h6 class="fw-bold mt-4 mb-2">Daftar Barang Dipinjam</h6>
                <div id="itemRows">
                    <div class="row g-2 mb-2 item-row">
                        <div class="col-md-7">
                            <select name="items[0][product_id]" class="form-select" required>
                                <option value="">-- Pilih Barang --</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }} (stok: {{ $product->stock }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="items[0][quantity]" min="1" value="1"
                                class="form-control" placeholder="Jumlah" required>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-outline-danger w-100 remove-row"><i
                                    class="bi bi-trash"></i></button>
                        </div>
                    </div>
                </div>
                <button type="button" id="addRow" class="btn btn-sm btn-outline-secondary mb-3"><i
                        class="bi bi-plus"></i> Tambah Barang Lain</button>

                <div class="mb-3">
                    <label class="form-label">Catatan (opsional)</label>
                    <textarea name="notes" rows="2" class="form-control">{{ old('notes') }}</textarea>
                </div>

                <div class="mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-danger" style="background-color:#e2001a;border-color:#e2001a;">
                        <i class="bi bi-check-circle"></i> Simpan Peminjaman
                    </button>
                    <a href="{{ route('borrowings.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let rowIndex = 1;
        const productOptions = document.querySelector('#itemRows .item-row select').innerHTML;

        document.getElementById('addRow').addEventListener('click', function() {
            const wrapper = document.createElement('div');
            wrapper.className = 'row g-2 mb-2 item-row';
            wrapper.innerHTML = `
            <div class="col-md-7">
                <select name="items[${rowIndex}][product_id]" class="form-select" required>${productOptions}</select>
            </div>
            <div class="col-md-3">
                <input type="number" name="items[${rowIndex}][quantity]" min="1" value="1" class="form-control" placeholder="Jumlah" required>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-outline-danger w-100 remove-row"><i class="bi bi-trash"></i></button>
            </div>`;
            document.getElementById('itemRows').appendChild(wrapper);
            rowIndex++;
        });

        document.getElementById('itemRows').addEventListener('click', function(e) {
            if (e.target.closest('.remove-row')) {
                const rows = document.querySelectorAll('.item-row');
                if (rows.length > 1) {
                    e.target.closest('.item-row').remove();
                }
            }
        });
    </script>
@endpush
