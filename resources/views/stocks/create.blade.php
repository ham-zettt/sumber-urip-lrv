@extends('layouts.app')

@section('title', 'Tambah Stok')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold tracking-tight text-ink">Tambah Stok</h1>
        <p class="text-sm text-ink-subtle mt-1">Catat penerimaan stok baru dari supplier.</p>
    </div>

    <div class="max-w-xl bg-surface-1 border border-hairline rounded-lg p-5 md:p-6">
        <form method="POST" action="{{ route('stok.store') }}">
            @csrf

            <div class="space-y-5">
                <div>
                    <label for="product_id" class="label">Produk <span class="text-danger">*</span></label>
                    <select id="product_id" name="product_id" class="input" required>
                        <option value="">Pilih produk</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" @selected(old('product_id') == $product->id)>{{ $product->name }} (Stok: {{ $product->stock }} {{ $product->unit }})</option>
                        @endforeach
                    </select>
                    @error('product_id')
                        <p class="mt-1 text-xs text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="quantity" class="label">Jumlah <span class="text-danger">*</span></label>
                    <input id="quantity" name="quantity" type="number" min="1" step="1" value="{{ old('quantity') }}" required class="input" placeholder="Contoh: 50">
                </div>

                <div>
                    <label for="movement_date" class="label">Tanggal <span class="text-danger">*</span></label>
                    <input id="movement_date" name="movement_date" type="date" value="{{ old('movement_date', now()->format('Y-m-d')) }}" required class="input">
                </div>

                <div>
                    <label for="note" class="label">Catatan</label>
                    <textarea id="note" name="note" rows="3" class="input resize-none" placeholder="Contoh: Supplier PT. Air Bersih">{{ old('note') }}</textarea>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-8">
                <button type="submit" class="bg-primary hover:bg-primary-hover text-on-primary font-medium text-sm rounded-md px-5 py-2.5 min-h-[44px] transition-colors">Simpan</button>
                <a href="{{ route('stok.index') }}" class="text-sm text-ink-muted hover:text-ink px-3 py-2.5 min-h-[44px] inline-flex items-center">Batal</a>
            </div>
        </form>
    </div>
@endsection