@php($product = $product ?? null)

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <div class="md:col-span-2">
        <label for="name" class="label">Nama Produk</label>
        <input id="name" name="name" type="text" value="{{ old('name', $product?->name ?? '') }}" required class="input" placeholder="Contoh: Isi Ulang Air Galon">
    </div>

    <div>
        <label for="unit" class="label">Satuan</label>
        <input id="unit" name="unit" type="text" value="{{ old('unit', $product?->unit ?? '') }}" required class="input" placeholder="galon, pcs">
    </div>

    <div>
        <label for="price" class="label">Harga Jual (Rp)</label>
        <input id="price" name="price" type="number" min="0" step="1" value="{{ old('price', $product?->price ?? '') }}" required class="input" placeholder="6000">
    </div>

    <div>
        <label for="min_stock" class="label">Stok Minimum</label>
        <input id="min_stock" name="min_stock" type="number" min="0" step="1" value="{{ old('min_stock', $product?->min_stock ?? 0) }}" required class="input">
    </div>

    @if ($product === null)
        <div>
            <label for="stock" class="label">Stok Awal</label>
            <input id="stock" name="stock" type="number" min="0" step="1" value="{{ old('stock', 0) }}" required class="input">
            <p class="text-xs text-ink-tertiary mt-1">Stok awal hanya dapat diisi saat produk dibuat. Selanjutnya gunakan menu Stok.</p>
        </div>
    @endif

    <div>
        <label for="track_stock" class="label">Lacak Stok</label>
        <select id="track_stock" name="track_stock" class="input" required>
            <option value="1" @selected(old('track_stock', $product?->track_stock ?? true) == true)>Ya (produk fisik)</option>
            <option value="0" @selected(old('track_stock', $product?->track_stock ?? true) == false)>Tidak (jasa / tanpa stok)</option>
        </select>
    </div>

    <div>
        <label class="label">Status</label>
        <label class="flex items-center gap-2 min-h-[44px] text-sm text-ink-muted cursor-pointer">
            <input type="checkbox" name="is_active" value="1" class="w-4 h-4 rounded border-hairline-strong bg-surface-1 text-primary focus:ring-primary-focus" @checked(old('is_active', $product?->is_active ?? true))>
            Aktif (tampil di kasir)
        </label>
    </div>
</div>

<div class="flex items-center gap-3 mt-8">
    <button type="submit" class="bg-primary hover:bg-primary-hover text-on-primary font-medium text-sm rounded-md px-5 py-2.5 min-h-[44px] transition-colors">Simpan</button>
    <a href="{{ route('produk.index') }}" class="text-sm text-ink-muted hover:text-ink px-3 py-2.5 min-h-[44px] inline-flex items-center">Batal</a>
</div>