@extends('layouts.app')

@section('title', 'Stok')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-ink">Riwayat Stok</h1>
            <p class="text-sm text-ink-subtle mt-1">Semua pergerakan stok masuk dan keluar.</p>
        </div>
        <a href="{{ route('stok.create') }}" class="inline-flex items-center justify-center gap-2 bg-primary hover:bg-primary-hover text-on-primary font-medium text-sm rounded-md px-4 py-2.5 min-h-[44px] transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"></path></svg>
            Tambah Stok
        </a>
    </div>

    <div class="bg-surface-1 border border-hairline rounded-lg">
        <div class="p-4 border-b border-hairline">
            <form method="GET" action="{{ route('stok.index') }}" class="flex flex-wrap gap-2">
                <select name="product_id" class="input max-w-xs">
                    <option value="">Semua produk</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}" @selected($productId === $product->id)>{{ $product->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="bg-surface-2 hover:bg-surface-3 border border-hairline text-ink text-sm font-medium rounded-md px-4 py-2.5 min-h-[44px] transition-colors">Filter</button>
                @if ($productId)
                    <a href="{{ route('stok.index') }}" class="inline-flex items-center text-sm text-ink-subtle hover:text-ink px-2">Reset</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs uppercase tracking-wide text-ink-subtle border-b border-hairline">
                    <tr>
                        <th class="px-4 py-3 font-medium">Tanggal</th>
                        <th class="px-4 py-3 font-medium">Produk</th>
                        <th class="px-4 py-3 font-medium">Tipe</th>
                        <th class="px-4 py-3 font-medium text-right">Jumlah</th>
                        <th class="px-4 py-3 font-medium">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-hairline">
                    @forelse ($movements as $movement)
                        <tr class="hover:bg-surface-2/50 transition-colors">
                            <td class="px-4 py-3 text-ink-muted whitespace-nowrap">{{ $movement->movement_date->translatedFormat('d M Y') }}</td>
                            <td class="px-4 py-3 text-ink">{{ $movement->product?->name ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex text-xs px-2 py-0.5 rounded-full {{ $movement->type === 'masuk' ? 'bg-success/10 text-success' : 'bg-danger/10 text-danger' }}">{{ $movement->type }}</span>
                            </td>
                            <td class="px-4 py-3 text-right text-ink-muted">{{ $movement->quantity }}</td>
                            <td class="px-4 py-3 text-ink-subtle">{{ $movement->note ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-ink-subtle">
                                Belum ada pergerakan stok.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($movements->hasPages())
            <div class="p-4 border-t border-hairline">
                {{ $movements->links() }}
            </div>
        @endif
    </div>
@endsection