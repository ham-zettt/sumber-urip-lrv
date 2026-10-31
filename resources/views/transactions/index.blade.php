@extends('layouts.app')

@section('title', 'Transaksi')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold tracking-tight text-ink">Transaksi</h1>
        <p class="text-sm text-ink-subtle mt-1">Riwayat penjualan dan pembatalan transaksi.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-surface-1 border border-hairline rounded-lg p-4">
            <p class="text-xs text-ink-subtle uppercase tracking-wider">Total Pendapatan</p>
            <p class="text-xl font-semibold text-ink mt-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
        </div>
        <div class="bg-surface-1 border border-hairline rounded-lg p-4">
            <p class="text-xs text-ink-subtle uppercase tracking-wider">Jumlah Transaksi</p>
            <p class="text-xl font-semibold text-ink mt-1">{{ $transactions->total() }}</p>
        </div>
        <div class="bg-surface-1 border border-hairline rounded-lg p-4">
            <p class="text-xs text-ink-subtle uppercase tracking-wider">Periode</p>
            <p class="text-sm font-medium text-ink mt-2">{{ \Illuminate\Support\Carbon::parse($startDate)->translatedFormat('d M Y') }} – {{ \Illuminate\Support\Carbon::parse($endDate)->translatedFormat('d M Y') }}</p>
        </div>
    </div>

    <div class="bg-surface-1 border border-hairline rounded-lg">
        <div class="p-4 border-b border-hairline">
            <form method="GET" action="{{ route('transaksi.index') }}" class="flex flex-wrap items-end gap-3">
                <div>
                    <label for="start_date" class="label mb-1">Dari Tanggal</label>
                    <input id="start_date" type="date" name="start_date" value="{{ $startDate }}" class="input text-sm">
                </div>
                <div>
                    <label for="end_date" class="label mb-1">Sampai Tanggal</label>
                    <input id="end_date" type="date" name="end_date" value="{{ $endDate }}" class="input text-sm">
                </div>
                <div class="flex-1 min-w-[160px]">
                    <label for="search" class="label mb-1">Cari Invoice</label>
                    <input id="search" type="text" name="search" value="{{ $search }}" placeholder="INV-..." class="input text-sm">
                </div>
                <button type="submit" class="bg-primary hover:bg-primary-hover text-on-primary text-sm font-medium rounded-md px-4 py-2.5 min-h-[44px] transition-colors">Terapkan</button>
                <a href="{{ route('transaksi.index') }}" class="text-sm text-ink-subtle hover:text-ink px-2 py-2.5">Reset</a>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs uppercase tracking-wide text-ink-subtle border-b border-hairline">
                    <tr>
                        <th class="px-4 py-3 font-medium">Invoice</th>
                        <th class="px-4 py-3 font-medium">Tanggal</th>
                        <th class="px-4 py-3 font-medium text-right">Item</th>
                        <th class="px-4 py-3 font-medium text-right">Total</th>
                        <th class="px-4 py-3 font-medium">Pembayaran</th>
                        <th class="px-4 py-3 font-medium text-center">Status</th>
                        <th class="px-4 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-hairline">
                    @forelse ($transactions as $transaction)
                        <tr class="hover:bg-surface-2/50 transition-colors">
                            <td class="px-4 py-3 font-mono text-xs text-ink">{{ $transaction->invoice_number }}</td>
                            <td class="px-4 py-3 text-ink-muted whitespace-nowrap">{{ $transaction->created_at->translatedFormat('d M Y, H:i') }}</td>
                            <td class="px-4 py-3 text-right text-ink-muted">{{ (int) $transaction->items_sum_quantity }}</td>
                            <td class="px-4 py-3 text-right text-ink font-medium">Rp {{ number_format($transaction->total, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-ink-subtle capitalize">{{ $transaction->payment_method }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex text-xs px-2 py-0.5 rounded-full {{ $transaction->status === 'selesai' ? 'bg-success/10 text-success' : 'bg-danger/10 text-danger' }}">{{ $transaction->status }}</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex items-center gap-3">
                                    <a href="{{ route('transaksi.show', $transaction) }}" class="text-primary hover:text-primary-hover text-sm font-medium">Detail</a>
                                    @if (! $transaction->isCancelled())
                                        <form method="POST" action="{{ route('transaksi.cancel', $transaction) }}" onsubmit="return confirm('Batalkan transaksi {{ $transaction->invoice_number }}? Stok akan dikembalikan.');">
                                            @csrf
                                            <button type="submit" class="text-danger hover:opacity-80 text-sm font-medium">Batal</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-ink-subtle">
                                Tidak ada transaksi pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($transactions->hasPages())
            <div class="p-4 border-t border-hairline">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
@endsection