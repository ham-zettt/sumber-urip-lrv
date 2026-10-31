<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = now()->toDateString();

        $todayStats = Transaction::query()
            ->whereDate('created_at', $today)
            ->where('status', 'selesai')
            ->selectRaw('sum(total) as total_revenue, count(*) as transaction_count, sum((select sum(quantity) from transaction_items where transaction_items.transaction_id = transactions.id)) as items_sold')
            ->first();

        $lowStockProducts = Product::query()
            ->where('track_stock', true)
            ->where('is_active', true)
            ->whereRaw('stock <= min_stock')
            ->orderBy('stock', 'asc')
            ->limit(10)
            ->get();

        $topProducts = TransactionItem::query()
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->where('transactions.status', 'selesai')
            ->where('transactions.created_at', '>=', now()->subDays(30))
            ->selectRaw('transaction_items.product_id, products.name, products.unit, sum(transaction_items.quantity) as total_qty, sum(transaction_items.subtotal) as total_revenue')
            ->groupBy('transaction_items.product_id', 'products.name', 'products.unit')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        return view('dashboard', compact('todayStats', 'lowStockProducts', 'topProducts'));
    }

    public function chart(Request $request): JsonResponse
    {
        $range = $request->integer('range', 7);
        $days = in_array($range, [7, 30]) ? $range : 7;

        $startDate = now()->subDays($days - 1)->startOfDay();
        $endDate = now()->endOfDay();

        $dailyRevenue = Transaction::query()
            ->where('status', 'selesai')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, sum(total) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $labels = [];
        $data = [];
        for ($i = 0; $i < $days; $i++) {
            $date = now()->subDays($days - 1 - $i)->format('Y-m-d');
            $labels[] = now()->subDays($days - 1 - $i)->translatedFormat('d M');
            $data[] = (int) ($dailyRevenue[$date]?->total ?? 0);
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }
}
