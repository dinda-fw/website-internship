@php $product = $product ?? null; @endphp

<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">Kode Barang</label>
        <input type="text" name="code" value="{{ old('code', $product->code ?? '') }}" class="form-control @error('code') is-invalid @enderror" placeholder="BRG-0013" required>
        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-8">
        <label class="form-label">Nama Barang</label>
        <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" class="form-control @error('name') is-invalid @enderror" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Kategori</label>
        <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
            <option value="">-- Pilih Kategori --</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
            @endforeach
        </select>
        @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Stok</label>
        <input type="number" min="0" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" class="form-control @error('stock') is-invalid @enderror" required>
        @error('stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Kondisi Barang</label>
        <select name="condition" class="form-select @error('condition') is-invalid @enderror" required>
            <option value="baik" {{ old('condition', $product->condition ?? '') == 'baik' ? 'selected' : '' }}>Baik</option>
            <option value="rusak_ringan" {{ old('condition', $product->condition ?? '') == 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
            <option value="rusak_berat" {{ old('condition', $product->condition ?? '') == 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
        </select>
        @error('condition')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Lokasi Penyimpanan</label>
        <input type="text" name="location" value="{{ old('location', $product->location ?? '') }}" class="form-control @error('location') is-invalid @enderror" placeholder="Gudang IT Lt. 2">
        @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Gambar Barang (opsional)</label>
        <input type="file" name="image" accept="image/*" class="form-control @error('image') is-invalid @enderror">
        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
        @if(!empty($product?->image))
            <img src="{{ asset('storage/'.$product->image) }}" class="table-thumb mt-2" alt="preview">
        @endif
    </div>

    <div class="col-12">
        <label class="form-label">Deskripsi (opsional)</label>
        <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $product->description ?? '') }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
