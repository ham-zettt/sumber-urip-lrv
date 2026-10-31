@extends('layouts.app')

@section('title', 'Produk')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-ink">Produk</h1>
            <p class="text-sm text-ink-subtle mt-1">Kelola daftar produk dan statusnya.</p>
        </div>
        <a href="{{ route('produk.create') }}" class="inline-flex items-center justify-center gap-2 bg-primary hover:bg-primary-hover text-on-primary font-medium text-sm rounded-md px-4 py-2.5 min-h-[44px] transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"></path></svg>
            Tambah Produk
        </a>
    </div>

    <div class="bg-surface-1 border border-hairline rounded-lg">
        <div class="p-4 border-b border-hairline">
            <form method="GET" action="{{ route('produk.index') }}" class="flex gap-2">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama produk..." class="input max-w-sm">
                <button type="submit" class="bg-surface-2 hover:bg-surface-3 border border-hairline text-ink text-sm font-medium rounded-md px-4 py-2.5 min-h-[44px] transition-colors">Cari</button>
                @if ($search !== '')
                    <a href="{{ route('produk.index') }}" class="inline-flex items-center text-sm text-ink-subtle hover:text-ink px-2">Reset</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs uppercase tracking-wide text-ink-subtle border-b border-hairline">
                    <tr>
                        <th class="px-4 py-3 font-medium">Nama</th>
                        <th class="px-4 py-3 font-medium">Satuan</th>
                        <th class="px-4 py-3 font-medium text-right">Harga</th>
                        <th class="px-4 py-3 font-medium text-right">Stok</th>
                        <th class="px-4 py-3 font-medium text-center">Lacak</th>
                        <th class="px-4 py-3 font-medium text-center">Status</th>
                        <th class="px-4 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-hairline">
                    @forelse ($products as $product)
                        <tr class="hover:bg-surface-2/50 transition-colors">
                            <td class="px-4 py-3 font-medium text-ink">{{ $product->name }}</td>
                            <td class="px-4 py-3 text-ink-subtle">{{ $product->unit }}</td>
                            <td class="px-4 py-3 text-right text-ink-muted">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right {{ $product->isLowStock() ? 'text-danger font-medium' : 'text-ink-muted' }}">
                                {{ $product->track_stock ? $product->stock : '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex text-xs px-2 py-0.5 rounded-full {{ $product->track_stock ? 'bg-surface-2 text-ink-muted' : 'bg-surface-2 text-ink-tertiary' }}">{{ $product->track_stock ? 'Ya' : 'Tidak' }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex text-xs px-2 py-0.5 rounded-full {{ $product->is_active ? 'bg-success/10 text-success' : 'bg-surface-2 text-ink-tertiary' }}">{{ $product->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex items-center gap-3">
                                    <a href="{{ route('produk.edit', $product) }}" class="text-primary hover:text-primary-hover text-sm font-medium">Edit</a>
                                    <form method="POST" action="{{ route('produk.destroy', $product) }}" onsubmit="return confirm('Hapus produk ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-danger hover:opacity-80 text-sm font-medium">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-ink-subtle">
                                Belum ada produk. Tambahkan produk pertama Anda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($products->hasPages())
            <div class="p-4 border-t border-hairline">
                {{ $products->links() }}
            </div>
        @endif
    </div>
@endsection