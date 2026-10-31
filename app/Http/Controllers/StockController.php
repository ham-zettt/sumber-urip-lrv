<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockController extends Controller
{
    public function index(Request $request): View
    {
        $productId = $request->integer('product_id') ?: null;

        $movements = StockMovement::query()
            ->with('product')
            ->when($productId, fn ($query) => $query->where('product_id', $productId))
            ->latest('movement_date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $products = Product::query()->orderBy('name')->get();

        return view('stocks.index', compact('movements', 'products', 'productId'));
    }

    public function create(): View
    {
        $products = Product::query()
            ->where('track_stock', true)
            ->orderBy('name')
            ->get();

        return view('stocks.create', compact('products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'movement_date' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $product = Product::findOrFail($data['product_id']);

        if (! $product->track_stock) {
            return back()
                ->withInput()
                ->withErrors(['product_id' => 'Produk ini tidak menggunakan pelacakan stok.']);
        }

        DB::transaction(function () use ($product, $data) {
            $product->increment('stock', $data['quantity']);

            StockMovement::create([
                'product_id' => $product->id,
                'type' => 'masuk',
                'quantity' => $data['quantity'],
                'note' => $data['note'] ?? null,
                'movement_date' => $data['movement_date'],
            ]);
        });

        return redirect()
            ->route('stok.index')
            ->with('success', 'Stok berhasil ditambahkan.');
    }
}
