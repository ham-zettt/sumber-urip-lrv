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
    <div x-data="salesChartComponent()" class="bg-surface-1 border border-hairline rounded-lg p-5">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-2">
            <div>
                <h2 class="text-sm font-semibold text-ink">Tren Penjualan</h2>
                <div class="flex items-center gap-1.5 mt-0.5 text-xs text-ink-subtle">
                    <span>Total periode ini:</span>
                    <span class="font-semibold text-ink" x-text="formattedTotal">Rp 0</span>
                </div>
            </div>
            <div class="inline-flex rounded-lg bg-surface-2 p-1 border border-hairline shrink-0">
                <button
                    type="button"
                    @click="setRange(7)"
                    :class="range === 7 ? 'bg-primary text-on-primary shadow-xs font-semibold' : 'text-ink-subtle hover:text-ink font-medium'"
                    class="rounded-md px-3 py-1.5 text-xs transition-colors min-h-[32px] flex items-center cursor-pointer"
                >
                    7 Hari
                </button>
                <button
                    type="button"
                    @click="setRange(30)"
                    :class="range === 30 ? 'bg-primary text-on-primary shadow-xs font-semibold' : 'text-ink-subtle hover:text-ink font-medium'"
                    class="rounded-md px-3 py-1.5 text-xs transition-colors min-h-[32px] flex items-center cursor-pointer"
                >
                    30 Hari
                </button>
            </div>
        </div>

        <div class="relative min-h-[280px] w-full">
            <!-- Loading overlay -->
            <div x-show="loading" class="absolute inset-0 flex items-center justify-center bg-surface-1/70 backdrop-blur-xs z-10 rounded-md transition-opacity">
                <div class="flex items-center gap-2 text-xs text-ink-muted bg-surface-2 px-3 py-1.5 rounded-full border border-hairline">
                    <svg class="animate-spin h-3.5 w-3.5 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>Memuat data grafik...</span>
                </div>
            </div>

            <!-- Error fallback -->
            <div x-show="!loading && error" x-cloak class="absolute inset-0 flex flex-col items-center justify-center text-center p-4">
                <p class="text-xs text-danger" x-text="error"></p>
                <button type="button" @click="fetchData()" class="mt-2 text-xs text-primary hover:underline font-medium">Coba lagi</button>
            </div>

            <!-- ApexCharts container (horizontal scroll on mobile) -->
            <div class="overflow-x-auto -mx-1 px-1">
                <div x-ref="chartContainer" :style="isMobile ? 'min-width:' + mobileChartWidth + 'px' : 'width:100%'"></div>
            </div>
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
        (function () {
            function registerSalesChartComponent() {
                window.Alpine.data('salesChartComponent', () => ({
                    range: 7,
                    loading: true,
                    error: null,
                    chart: null,
                    totalRevenue: 0,
                    isMobile: window.innerWidth < 640,
                    mobileChartWidth: 700,

                    get formattedTotal() {
                        return 'Rp ' + Number(this.totalRevenue || 0).toLocaleString('id-ID');
                    },

                    async init() {
                        window.addEventListener('theme-changed', () => {
                            if (this.chart) {
                                const isLight = document.documentElement.classList.contains('light');
                                this.chart.updateOptions({
                                    theme: { mode: isLight ? 'light' : 'dark' },
                                    grid: { borderColor: isLight ? '#e2e8f0' : '#23252a' },
                                    xaxis: {
                                        axisBorder: { color: isLight ? '#e2e8f0' : '#23252a' },
                                        labels: {
                                            style: {
                                                colors: isLight ? '#64748b' : '#8a8f98'
                                            }
                                        }
                                    },
                                    yaxis: {
                                        labels: {
                                            style: {
                                                colors: isLight ? '#64748b' : '#8a8f98'
                                            }
                                        }
                                    },
                                    markers: {
                                        strokeColors: isLight ? '#ffffff' : '#0f1011'
                                    },
                                    tooltip: {
                                        theme: isLight ? 'light' : 'dark'
                                    }
                                });
                            }
                        });
                        await this.waitForApex();
                        await this.fetchData();
                    },

                    async waitForApex() {
                        let attempts = 0;
                        while (typeof window.ApexCharts === 'undefined' && attempts < 60) {
                            await new Promise(resolve => setTimeout(resolve, 50));
                            attempts++;
                        }
                    },

                    async setRange(newRange) {
                        if (this.range === newRange || this.loading) return;
                        this.range = newRange;
                        await this.fetchData();
                    },

                    async fetchData() {
                        this.loading = true;
                        this.error = null;

                        try {
                            const url = `{{ route('dashboard.chart') }}?range=${this.range}`;
                            const res = await fetch(url, {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });

                            if (!res.ok) {
                                throw new Error('Gagal memuat data (Status ' + res.status + ')');
                            }

                            const { labels, data } = await res.json();
                            const numData = (data || []).map(v => Number(v) || 0);
                            this.totalRevenue = numData.reduce((acc, curr) => acc + curr, 0);

                            this.renderChart(labels || [], numData);
                        } catch (err) {
                            console.error('Error fetching sales chart:', err);
                            this.error = 'Tidak dapat memuat data grafik.';
                        } finally {
                            this.loading = false;
                        }
                    },

                    renderChart(labels, data) {
                        if (typeof window.ApexCharts === 'undefined') {
                            this.error = 'Library grafik tidak tersedia.';
                            return;
                        }

                        const el = this.$refs.chartContainer;
                        if (!el) return;

                        this.isMobile = window.innerWidth < 640;

                        // On mobile, give each data point at least 40px so labels don't overlap
                        const minWidthPerPoint = 40;
                        const computedWidth = this.isMobile
                            ? Math.max(700, labels.length * minWidthPerPoint)
                            : '100%';
                        this.mobileChartWidth = typeof computedWidth === 'number' ? computedWidth : 700;

                        const isLight = document.documentElement.classList.contains('light');

                        const options = {
                            chart: {
                                type: 'area',
                                height: 280,
                                width: this.isMobile ? this.mobileChartWidth : '100%',
                                fontFamily: 'Inter, system-ui, sans-serif',
                                background: 'transparent',
                                toolbar: { show: false },
                                animations: {
                                    enabled: true,
                                    easing: 'easeinout',
                                    speed: 400
                                }
                            },
                            series: [{
                                name: 'Pendapatan',
                                data: data
                            }],
                            colors: ['#5e6ad2'],
                            fill: {
                                type: 'gradient',
                                gradient: {
                                    shadeIntensity: 1,
                                    opacityFrom: isLight ? 0.35 : 0.45,
                                    opacityTo: 0.05,
                                    stops: [0, 95, 100]
                                }
                            },
                            stroke: {
                                curve: 'smooth',
                                width: 2.5
                            },
                            markers: {
                                size: 3,
                                colors: ['#5e6ad2'],
                                strokeColors: isLight ? '#ffffff' : '#0f1011',
                                strokeWidth: 2,
                                hover: {
                                    size: 5
                                }
                            },
                            dataLabels: {
                                enabled: false
                            },
                            grid: {
                                borderColor: isLight ? '#e2e8f0' : '#23252a',
                                strokeDashArray: 4,
                                xaxis: {
                                    lines: { show: false }
                                },
                                yaxis: {
                                    lines: { show: true }
                                },
                                padding: {
                                    top: 10,
                                    right: 15,
                                    bottom: 0,
                                    left: 10
                                }
                            },
                            theme: {
                                mode: isLight ? 'light' : 'dark'
                            },
                            xaxis: {
                                categories: labels,
                                axisBorder: {
                                    show: true,
                                    color: isLight ? '#e2e8f0' : '#23252a'
                                },
                                axisTicks: {
                                    show: false
                                },
                                labels: {
                                    style: {
                                        colors: isLight ? '#64748b' : '#8a8f98',
                                        fontSize: '11px',
                                        fontFamily: 'Inter, system-ui, sans-serif'
                                    }
                                }
                            },
                            yaxis: {
                                labels: {
                                    style: {
                                        colors: isLight ? '#64748b' : '#8a8f98',
                                        fontSize: '11px',
                                        fontFamily: 'Inter, system-ui, sans-serif'
                                    },
                                    formatter: function (val) {
                                        if (val >= 1000000) {
                                            return 'Rp ' + (val / 1000000).toLocaleString('id-ID', { maximumFractionDigits: 1 }) + ' jt';
                                        }
                                        if (val >= 1000) {
                                            return 'Rp ' + (val / 1000).toLocaleString('id-ID', { maximumFractionDigits: 0 }) + ' rb';
                                        }
                                        return 'Rp ' + Number(val || 0).toLocaleString('id-ID');
                                    }
                                }
                            },
                            tooltip: {
                                theme: isLight ? 'light' : 'dark',
                                x: {
                                    show: true
                                },
                                y: {
                                    formatter: function (val) {
                                        return 'Rp ' + Number(val || 0).toLocaleString('id-ID');
                                    }
                                },
                                style: {
                                    fontSize: '12px',
                                    fontFamily: 'Inter, system-ui, sans-serif'
                                }
                            }
                        };

                        if (this.chart) {
                            this.chart.updateOptions({
                                chart: {
                                    width: this.isMobile ? this.mobileChartWidth : '100%',
                                },
                                xaxis: { categories: labels },
                                series: [{ name: 'Pendapatan', data: data }]
                            });
                        } else {
                            this.chart = new window.ApexCharts(el, options);
                            this.chart.render();
                        }
                    }
                }));
            }

            if (window.Alpine) {
                registerSalesChartComponent();
            } else {
                document.addEventListener('alpine:init', registerSalesChartComponent);
            }
        })();
    </script>
@endpush