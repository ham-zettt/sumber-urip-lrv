@extends('layouts.app')

@section('title', 'Detail Transaksi')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-ink">Detail Transaksi</h1>
            <p class="text-sm text-ink-subtle mt-1">Invoice: <span class="font-mono">{{ $transaction->invoice_number }}</span></p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('transaksi.index') }}" class="inline-flex items-center gap-2 bg-surface-2 hover:bg-surface-3 border border-hairline text-ink text-sm font-medium rounded-md px-4 py-2.5 min-h-[44px] transition-colors">
                Kembali
            </a>
            <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 bg-primary hover:bg-primary-hover text-on-primary text-sm font-medium rounded-md px-4 py-2.5 min-h-[44px] transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V2h12v7"></path><path d="M6 18H4a2 2 0 0 1-2-2v-5"></path><path d="M18 18h2a2 2 0 0 0 2-2v-5"></path><rect x="6" y="14" width="12" height="8" rx="1"></rect></svg>
                Cetak
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            @include('cashier.receipt', ['transaction' => $transaction])
        </div>

        <div class="space-y-4">
            <div class="bg-surface-1 border border-hairline rounded-lg p-4">
                <h3 class="text-sm font-semibold text-ink mb-3">Ringkasan</h3>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-ink-muted">Total Item</dt>
                        <dd class="font-medium text-ink">{{ $transaction->itemCount() }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-ink-muted">Total</dt>
                        <dd class="font-semibold text-ink">Rp {{ number_format($transaction->total, 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-ink-muted">Bayar</dt>
                        <dd class="font-medium text-ink">Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-ink-muted">Kembalian</dt>
                        <dd class="font-medium text-ink">Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-ink-muted">Pembayaran</dt>
                        <dd class="font-medium text-ink capitalize">{{ $transaction->payment_method }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-ink-muted">Status</dt>
                        <dd>
                            <span class="inline-flex text-xs px-2 py-0.5 rounded-full {{ $transaction->isCancelled() ? 'bg-danger/10 text-danger' : 'bg-success/10 text-success' }}">
                                {{ $transaction->status }}
                            </span>
                        </dd>
                    </div>
                    @if ($transaction->note)
                        <div class="flex justify-between">
                            <dt class="text-ink-muted">Catatan</dt>
                            <dd class="font-medium text-ink">{{ $transaction->note }}</dd>
                        </div>
                    @endif
                    @if ($transaction->isCancelled())
                        <div class="flex justify-between">
                            <dt class="text-ink-muted">Dibatalkan</dt>
                            <dd class="font-medium text-ink">{{ $transaction->cancelled_at?->translatedFormat('d M Y, H:i') }}</dd>
                        </div>
                    @endif
                </dl>
            </div>
        </div>
    </div>
@endsection