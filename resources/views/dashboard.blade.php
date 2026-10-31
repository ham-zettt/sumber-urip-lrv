@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold tracking-tight text-ink">Dashboard</h1>
        <p class="text-sm text-ink-subtle mt-1">Ringkasan penjualan harian.</p>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-surface-1 border border-hairline rounded-lg p-4">
            <div class="flex items-baseline gap-2">
                <div class="text-3xl font-semibold text-ink">Rp {{ number_format($todayStats->total_revenue ?? 0, 0, ',', '.') }}</div>
                <div class="text-xs text-ink-subtle">Total Pendapatan</div>
            </div>
        </div>
        <div class="bg-surface-1 border border-hairline rounded-lg p-4">
            <div class="flex items-baseline gap-2">
                <div class="text-3xl font-semibold text-ink">{{ $todayStats->transaction_count ?? 0 }}</div>
                <div class="text-xs text-ink-subtle">Jumlah Transaksi</div>
            </div>
        </div>
        <div class="bg-surface-1 border border-hairline rounded-lg p-4">
            <div class="flex items-baseline gap-2">
                <div class="text-3xl font-semibold text-ink">{{ $todayStats->items_sold ?? 0 }}</div>
                <div class="text-xs text-ink-subtle">Total Items Terjual</div>
            </div>
        </div>
        <div class="bg-surface-1 border border-hairline rounded-lg p-4">
            <div class="flex items-baseline gap-2">
                <div class="text-3xl font-semibold text-ink text-danger">{{ $lowStockProducts->count() }}</div>
                <div class="text-xs text-ink-subtle">Produk Low Stock</div>
            </div>
        </div>
    </div>

    <!-- Chart -->
    <div class="bg-surface-1 border border-hairline rounded-lg p-4">
        <div class="flex items-center gap-3 mb-4">
            <button id="range-7" class="range-btn inline-flex items-center rounded-md bg-primary text-on-primary px-3 py-1.5 text-sm font-medium">7 Hari</button>
            <button id="range-30" class="range-btn inline-flex items-center rounded-md bg-surface-2 text-ink-subtle hover:text-ink hover:bg-surface-3 px-3 py-1.5 text-sm font-medium">30 Hari</button>
        </div>
        <div class="relative h-64">
            <div id="salesChart"></div>
        </div>
    </div>

    <!-- Top Products -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6 mt-6">
        @forelse ($topProducts as $product)
            <div class="bg-surface-1 border border-hairline rounded-lg p-3">
                <div class="flex items-center gap-2 text-xs mb-1">
                    <span class="font-medium text-ink">{{ $product->name }}</span>
                    <span class="text-ink-subtle capitalize">{{ $product->unit }}</span>
                </div>
                <div class="text-sm font-semibold text-ink">{{ number_format($product->total_qty, 0, ',', '.') }}</div>
                <div class="text-[10px] text-ink-subtle capitalize">{{ $product->unit }}</div>
            </div>
        @empty
            <div class="text-center py-4 text-ink-subtle text-sm">Tidak ada data top produk.</div>
        @endforelse
    </div>

    <!-- Low Stock Warnings -->
    <div class="mt-6">
        <h2 class="text-sm font-semibold text-ink mb-3">Produk Stok Rendah</h2>
        <div class="space-y-2 text-sm">
            @forelse ($lowStockProducts as $product)
                <div class="flex items-center justify-between gap-2 p-2 rounded bg-danger/10 border border-danger/20">
                    <div>
                        <span class="font-medium text-ink">{{ $product->name }}</span>
                        <span class="text-ink-subtle capitalize block text-xs">{{ $product->unit }}</span>
                    </div>
                    <span class="text-xs text-ink-tertiary">{{ $product->stock }} / {{ $product->min_stock }}</span>
                </div>
            @empty
                <p class="text-ink-subtle">Tidak ada produk dengan stok rendah.</p>
            @endforelse
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btn7 = document.getElementById('range-7');
            const btn30 = document.getElementById('range-30');

            async function draw(range) {
                btn7.className = range === 7
                    ? 'range-btn inline-flex items-center rounded-md bg-primary text-on-primary px-3 py-1.5 text-sm font-medium'
                    : 'range-btn inline-flex items-center rounded-md bg-surface-2 text-ink-subtle hover:text-ink hover:bg-surface-3 px-3 py-1.5 text-sm font-medium';
                btn30.className = range === 30
                    ? 'range-btn inline-flex items-center rounded-md bg-primary text-on-primary px-3 py-1.5 text-sm font-medium'
                    : 'range-btn inline-flex items-center rounded-md bg-surface-2 text-ink-subtle hover:text-ink hover:bg-surface-3 px-3 py-1.5 text-sm font-medium';

                const res = await fetch(`/dashboard/chart?range=${range}`);
                const { labels, data } = await res.json();
                const el = document.getElementById('salesChart');
                if (!el) return;
                if (window.salesChart) window.salesChart.destroy();
                const options = {
                    chart: { type: 'line', height: 260, toolbar: { show: false }, animations: { enabled: false } },
                    series: [{ name: 'Pendapatan', data }],
                    xaxis: { categories: labels },
                    stroke: { curve: 'smooth', width: 2 },
                    colors: ['#5e6ad2'],
                    dataLabels: { enabled: false },
                    grid: { borderColor: '#23252a' },
                    theme: { mode: 'dark' },
                    yaxis: { labels: { formatter: v => 'Rp ' + v.toLocaleString('id-ID') } }
                };
                window.salesChart = new ApexCharts(el, options);
                window.salesChart.render();
            }

            btn7.addEventListener('click', () => draw(7));
            btn30.addEventListener('click', () => draw(30));
            draw(7);
        });
    </script>
@endpush