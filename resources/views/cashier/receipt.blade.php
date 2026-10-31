{{-- Reusable printable receipt. Expects $transaction with items loaded. --}}
<div class="print-area bg-white text-black rounded-lg p-5 md:p-6 w-full max-w-sm mx-auto">
    <div class="flex items-center justify-between border-b border-black/20 pb-3 mb-3">
        <div class="flex items-center gap-2">
            <div class="w-3 h-3 rounded-full bg-primary"></div>
            <span class="font-bold text-lg tracking-tight">Sumber Urip</span>
        </div>
        <span class="text-[10px] font-medium text-black/60">Struk</span>
    </div>

    <div class="text-[11px] text-black/70 mb-3 space-y-0.5">
        <p>No: <span class="font-mono font-semibold">{{ $transaction->invoice_number }}</span></p>
        <p>Tanggal: {{ $transaction->created_at->translatedFormat('d M Y, H:i') }}</p>
        <p>Pembayaran: <span class="capitalize">{{ $transaction->payment_method }}</span></p>
        <p>Status: <span class="capitalize">{{ $transaction->status }}</span></p>
    </div>

    <table class="w-full text-left mb-3 text-[11px]">
        <thead class="text-black/50 border-b border-black/10">
            <tr>
                <th class="pb-1 font-medium">Item</th>
                <th class="pb-1 text-right font-medium">Qty</th>
                <th class="pb-1 text-right font-medium">Harga</th>
                <th class="pb-1 text-right font-medium">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transaction->items as $item)
                <tr class="border-b border-black/5">
                    <td class="py-1">{{ $item->product_name }}</td>
                    <td class="py-1 text-right">{{ $item->quantity }}</td>
                    <td class="py-1 text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="py-1 text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="border-t border-black/20 pt-2 space-y-1 text-[11px]">
        <div class="flex justify-between"><span>Total</span><span class="font-bold">Rp {{ number_format($transaction->total, 0, ',', '.') }}</span></div>
        <div class="flex justify-between"><span>Bayar</span><span class="font-bold">Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</span></div>
        <div class="flex justify-between"><span class="text-black/60">Kembalian</span><span class="font-bold">Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</span></div>
    </div>

    @if ($transaction->note)
        <div class="text-[11px] mt-3 p-2 rounded border border-black/10 bg-black/5">
            Catatan: {{ $transaction->note }}
        </div>
    @endif

    @if ($transaction->isCancelled())
        <div class="text-[11px] mt-3 p-2 rounded border border-danger/30 bg-danger/10 text-danger font-medium">
            Transaksi dibatalkan pada {{ $transaction->cancelled_at?->translatedFormat('d M Y, H:i') }}
        </div>
    @endif

    <p class="text-center text-[10px] text-black/40 mt-4">Terima kasih telah berbelanja</p>
</div>