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
        <div class="card-body p-3 p-md-4">
            <form method="POST" action="{{ route('borrowings.store') }}" id="borrowingForm">
                @csrf

                <!-- Data Peminjam -->
                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label">Nama Peminjam <span class="text-danger">*</span></label>
                        <input type="text" name="borrower_name" value="{{ old('borrower_name') }}" class="form-control"
                            required>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <label class="form-label">Tanggal Pinjam <span class="text-danger">*</span></label>
                        <input type="date" name="borrow_date" value="{{ old('borrow_date', now()->format('Y-m-d')) }}"
                            class="form-control" required>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <label class="form-label">Batas Kembali</label>
                        <input type="date" name="due_date" value="{{ old('due_date') }}" class="form-control">
                    </div>
                </div>

                <!-- Daftar Barang -->
                <div class="mb-3">
                    <h6 class="fw-bold mb-2">Daftar Barang Dipinjam</h6>
                    <div id="itemRows" class="border rounded p-3 bg-secondary-subtle">
                        <div class="row g-2 mb-2 item-row">
                            <div class="col-12 col-md-7">
                                <select name="items[0][product_id]" class="form-select" required>
                                    <option value="">-- Pilih Barang --</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }} (stok:
                                            {{ $product->stock }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 col-md-3">
                                <input type="number" name="items[0][quantity]" min="1" value="1"
                                    class="form-control" placeholder="Jumlah" required>
                            </div>
                            <div class="col-12 col-sm-6 col-md-2">
                                <button type="button" class="btn btn-outline-danger w-100 remove-row"><i
                                        class="bi bi-trash"></i> <span class="d-inline d-md-none">Hapus</span></button>
                            </div>
                        </div>
                    </div>
                    <button type="button" id="addRow" class="btn btn-sm btn-outline-secondary mt-2"><i
                            class="bi bi-plus"></i> Tambah Barang Lain</button>
                </div>

                <!-- Catatan -->
                <div class="mb-3">
                    <label class="form-label">Catatan (opsional)</label>
                    <textarea name="notes" rows="2" class="form-control" placeholder="Catatan tambahan...">{{ old('notes') }}</textarea>
                </div>

                <!-- Buttons -->
                <div class="mt-4 pt-3 border-top d-flex flex-column flex-sm-row gap-2">
                    <button type="submit" class="btn btn-danger" style="background-color:#e2001a;border-color:#e2001a;"
                        order="1" order-sm="0">
                        <i class="bi bi-check-circle"></i> Simpan Peminjaman
                    </button>
                    <a href="{{ route('borrowings.index') }}" class="btn btn-outline-secondary" order="0"
                        order-sm="1">
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
            <div class="col-12 col-md-7">
                <select name="items[${rowIndex}][product_id]" class="form-select" required>${productOptions}</select>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <input type="number" name="items[${rowIndex}][quantity]" min="1" value="1" class="form-control" placeholder="Jumlah" required>
            </div>
            <div class="col-12 col-sm-6 col-md-2">
                <button type="button" class="btn btn-outline-danger w-100 remove-row"><i class="bi bi-trash"></i> <span class="d-inline d-md-none">Hapus</span></button>
            </div>
        `;
            document.getElementById('itemRows').appendChild(wrapper);
            attachRemoveListener(wrapper.querySelector('.remove-row'));
            rowIndex++;
        });

        function attachRemoveListener(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                this.closest('.item-row').remove();
            });
        }

        document.querySelectorAll('.remove-row').forEach(btn => attachRemoveListener(btn));
    </script>
@endpush
