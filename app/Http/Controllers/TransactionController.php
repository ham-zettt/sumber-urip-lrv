<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(Request $request): View
    {
        $startDate = $request->input('start_date', now()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        $search = $request->string('search')->trim()->toString();

        $query = Transaction::query()
            ->withSum('items', 'quantity')
            ->when($startDate !== '', fn ($q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate !== '', fn ($q) => $q->whereDate('created_at', '<=', $endDate))
            ->when($search !== '', fn ($q) => $q->where('invoice_number', 'like', "%{$search}%"));

        $totalRevenue = (clone $query)
            ->where('status', 'selesai')
            ->sum('total');

        $transactions = $query
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('transactions.index', compact('transactions', 'totalRevenue', 'startDate', 'endDate', 'search'));
    }

    public function show(Transaction $transaction): View
    {
        $transaction->load('items.product');

        return view('transactions.show', compact('transaction'));
    }

    public function cancel(Transaction $transaction): RedirectResponse
    {
        if ($transaction->isCancelled()) {
            return back()->withErrors(['transaction' => 'Transaksi ini sudah dibatalkan.']);
        }

        DB::transaction(function () use ($transaction) {
            $transaction->load('items.product');

            foreach ($transaction->items as $item) {
                $product = $item->product;

                if ($product && $product->track_stock) {
                    $product->increment('stock', $item->quantity);

                    StockMovement::create([
                        'product_id' => $product->id,
                        'type' => 'masuk',
                        'quantity' => $item->quantity,
                        'note' => "Pembatalan transaksi {$transaction->invoice_number}",
                        'transaction_id' => $transaction->id,
                        'movement_date' => now()->toDateString(),
                    ]);
                }
            }

            $transaction->update([
                'status' => 'batal',
                'cancelled_at' => now(),
            ]);
        });

        return back()->with('success', 'Transaksi berhasil dibatalkan dan stok telah dikembalikan.');
    }
}
