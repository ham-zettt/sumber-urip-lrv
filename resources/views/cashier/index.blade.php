@extends('layouts.app')

@section('title', 'Kasir')

@section('content')
    <div x-data="cashierPage()" x-cloak>
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight text-ink">Kasir</h1>
                <p class="text-sm text-ink-subtle mt-1">Tampilkan produk, tambahkan ke keranjang, dan bayar.</p>
            </div>
            <a href="{{ route('transaksi.index') }}" class="inline-flex items-center justify-center gap-2 bg-surface-2 hover:bg-surface-3 border border-hairline text-ink font-medium text-sm rounded-md px-4 py-2.5 min-h-[44px] transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h16M4 12h16M4 18h16"></path></svg>
                Riwayat Transaksi
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Products panel -->
            <div class="lg:col-span-7 bg-surface-1 border border-hairline rounded-lg p-4 max-h-[calc(100vh-14rem)] overflow-y-auto">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5 text-ink-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.3-4.3"></path></svg>
                    <input type="text" x-model="search" placeholder="Cari produk..." class="flex-1 input min-h-[44px]">
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    <template x-for="product in filteredProducts" :key="product.id">
                        <button
                            type="button"
                            x-on:click="addProduct(product)"
                            :disabled="product.track_stock && product.stock <= 0"
                            class="flex flex-col items-center gap-1.5 rounded-lg border border-hairline bg-surface-2 hover:bg-surface-3 hover:border-primary p-3 text-center transition-colors min-h-[88px] disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:border-hairline disabled:hover:bg-surface-2"
                        >
                            <span class="text-xs font-medium text-ink line-clamp-2" x-text="product.name"></span>
                            <span class="text-sm font-semibold text-ink" x-text="formatMoney(product.price)"></span>
                            <span class="text-[10px] text-ink-tertiary capitalize" x-text="product.unit"></span>
                            <template x-if="product.track_stock && product.stock <= 0">
                                <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-danger/10 text-danger border border-danger/20">Stok Habis</span>
                            </template>
                            <template x-if="product.track_stock && product.stock > 0">
                                <span class="text-[10px] text-ink-tertiary">Stok: <span x-text="product.stock"></span></span>
                            </template>
                        </button>
                    </template>

                    <template x-if="filteredProducts.length === 0">
                        <div class="col-span-full py-12 text-center text-ink-subtle text-sm">
                            Tidak ada produk yang cocok.
                        </div>
                    </template>
                </div>

                <p class="mt-3 text-xs text-ink-tertiary text-center">Klik produk untuk menambahkannya ke keranjang.</p>
            </div>

            <!-- Cart panel -->
            <div class="lg:col-span-5">
                <div class="bg-surface-1 border border-hairline rounded-lg p-4 flex flex-col lg:h-[calc(100vh-14rem)]">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-ink">Keranjang</h2>
                        <button type="button" x-on:click="clearCart()" class="text-xs text-danger hover:bg-danger/10 px-2 py-1 rounded transition-colors">Kosongkan</button>
                    </div>

                    <div class="flex-1 overflow-y-auto space-y-2 min-h-[120px] overflow-x-hidden">
                        <template x-for="(item, index) in cartItems" :key="item.product_id">
                            <div class="flex flex-wrap items-center gap-2 p-2 rounded bg-surface-2 border border-hairline/60 overflow-hidden">
                                <div class="min-w-0 flex-1 w-full sm:w-auto">
                                    <p class="text-xs font-medium text-ink truncate" x-text="item.name"></p>
                                    <p class="text-[11px] text-ink-tertiary" x-text="formatMoney(item.price)"></p>
                                </div>
                                <div class="flex items-center gap-1 shrink-0">
                                    <button type="button" x-on:click="updateQty(index, item.qty - 1)" class="w-6 h-6 rounded-md border border-hairline bg-surface-1 text-ink text-sm flex items-center justify-center hover:bg-surface-3">−</button>
                                    <input type="number" x-model.number="item.qty" x-on:change="updateQty(index, item.qty)" min="1" class="w-9 text-center text-xs border border-hairline rounded bg-surface-1 text-ink py-1">
                                    <button type="button" x-on:click="updateQty(index, item.qty + 1)" class="w-6 h-6 rounded-md border border-hairline bg-surface-1 text-ink text-sm flex items-center justify-center hover:bg-surface-3">+</button>
                                </div>
                                <span class="text-xs text-ink-muted text-right w-14 shrink-0" x-text="formatMoney(item.price * item.qty)"></span>
                                <button type="button" x-on:click="removeItem(index)" class="text-ink-tertiary hover:text-danger text-lg leading-none w-5 shrink-0">×</button>
                            </div>
                        </template>

                        <template x-if="cartItems.length === 0">
                            <div class="flex flex-col items-center justify-center py-8 text-center">
                                <p class="text-ink-subtle text-sm">Keranjang masih kosong.</p>
                            </div>
                        </template>
                    </div>

                    <div class="border-t border-hairline pt-4 space-y-4 mt-3">
                        <div class="flex justify-between items-baseline">
                            <span class="text-sm text-ink-muted">Total</span>
                            <span class="text-xl font-semibold text-ink" x-text="formatMoney(total)"></span>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-ink-subtle uppercase tracking-wider mb-2">Metode Pembayaran</p>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="cursor-pointer flex items-center gap-2 p-2.5 rounded border border-hairline bg-surface-1 hover:bg-surface-2 transition-colors min-h-[44px]">
                                    <input type="radio" value="tunai" x-model="paymentMethod" class="w-4 h-4 text-primary">
                                    <span class="text-sm font-medium text-ink">Tunai</span>
                                </label>
                                <label class="cursor-pointer flex items-center gap-2 p-2.5 rounded border border-hairline bg-surface-1 hover:bg-surface-2 transition-colors min-h-[44px]">
                                    <input type="radio" value="transfer" x-model="paymentMethod" class="w-4 h-4 text-primary">
                                    <span class="text-sm font-medium text-ink">Transfer</span>
                                </label>
                            </div>
                        </div>

                        <template x-if="paymentMethod === 'tunai'">
                            <div class="space-y-2">
                                <label class="text-xs font-medium text-ink-subtle uppercase tracking-wider block">Uang Diterima</label>
                                <div class="flex gap-2">
                                    <input type="number" x-model.number="paidAmount" min="0" class="flex-1 input text-sm min-h-[44px]" placeholder="0">
                                    <button type="button" x-on:click="paidAmount = total" class="bg-surface-2 hover:bg-surface-3 border border-hairline text-ink text-xs font-medium px-3 min-h-[44px] rounded-md transition-colors">Uang Pas</button>
                                </div>
                                <div class="flex gap-2">
                                    <button type="button" x-on:click="paidAmount = 50000" class="flex-1 bg-surface-2 hover:bg-surface-3 border border-hairline text-ink text-xs font-medium py-2 rounded-md transition-colors">50.000</button>
                                    <button type="button" x-on:click="paidAmount = 100000" class="flex-1 bg-surface-2 hover:bg-surface-3 border border-hairline text-ink text-xs font-medium py-2 rounded-md transition-colors">100.000</button>
                                </div>
                                <p class="text-xs text-ink-tertiary">Kembalian: <span class="text-ink font-semibold" x-text="formatMoney(change)"></span></p>
                            </div>
                        </template>

                        <template x-if="paymentMethod === 'transfer'">
                            <p class="text-xs text-ink-subtle">Transfer dibayar penuh: <span class="text-ink font-semibold" x-text="formatMoney(total)"></span></p>
                        </template>

                        <div>
                            <label class="text-xs font-medium text-ink-subtle uppercase tracking-wider block mb-1">Catatan (opsional)</label>
                            <textarea x-model="note" rows="2" class="input text-sm resize-none" placeholder="Nama pelanggan / alamat"></textarea>
                        </div>

                        <button type="button"
                                x-on:click="saveTransaction()"
                                :disabled="cartItems.length === 0 || loading || (paymentMethod === 'tunai' && paidAmount < total)"
                                class="w-full bg-primary hover:bg-primary-hover disabled:bg-surface-2 disabled:text-ink-tertiary disabled:cursor-not-allowed text-on-primary font-medium text-sm rounded-md py-3 min-h-[48px] transition-colors"
                                x-text="loading ? 'Memproses...' : 'Bayar / Simpan Transaksi'"></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Receipt modal -->
        <div x-show="receiptVisible" x-cloak class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4" @keydown.escape.window="receiptVisible = false">
            <div class="absolute inset-0" @click="receiptVisible = false"></div>
            <div class="relative bg-white text-black rounded-lg p-5 md:p-6 w-full max-w-xs shadow-2xl max-h-[90vh] overflow-y-auto print-area">
                <div class="flex items-center justify-between border-b border-black/20 pb-3 mb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-primary"></div>
                        <span class="font-bold text-lg tracking-tight">Sumber Urip</span>
                    </div>
                    <span class="text-[10px] font-medium text-black/60">Struk</span>
                </div>

                <div class="text-[11px] text-black/70 mb-3 space-y-0.5">
                    <p>No: <span class="font-mono font-semibold" x-text="receipt.invoice_number"></span></p>
                    <p>Tanggal: <span x-text="formatDate(receipt.created_at)"></span></p>
                </div>

                <table class="w-full text-left mb-3 text-[11px]">
                    <thead class="text-black/50 border-b border-black/10">
                        <tr><th class="pb-1 font-medium">Item</th><th class="pb-1 text-right font-medium">Qty</th><th class="pb-1 text-right font-medium">Subtotal</th></tr>
                    </thead>
                    <tbody>
                        <template x-for="item in receipt.items" :key="item.id">
                            <tr class="border-b border-black/5">
                                <td class="py-1" x-text="item.product_name"></td>
                                <td class="py-1 text-right" x-text="item.quantity"></td>
                                <td class="py-1 text-right" x-text="formatMoney(item.subtotal)"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>

                <div class="border-t border-black/20 pt-2 space-y-1 text-[11px]">
                    <div class="flex justify-between"><span>Total</span><span class="font-bold" x-text="formatMoney(receipt.total)"></span></div>
                    <div class="flex justify-between"><span>Bayar</span><span class="font-bold" x-text="formatMoney(receipt.paid_amount)"></span></div>
                    <div class="flex justify-between"><span class="text-black/60">Kembalian</span><span class="font-bold" x-text="formatMoney(receipt.change_amount)"></span></div>
                </div>

                <template x-if="receipt.note">
                    <div class="text-[11px] mt-3 p-2 rounded border border-black/10 bg-black/5">
                        Catatan: <span x-text="receipt.note"></span>
                    </div>
                </template>

                <p class="text-center text-[10px] text-black/40 mt-4 mb-3">Terima kasih telah berbelanja</p>

                <div class="flex gap-2">
                    <button type="button" x-on:click="printReceipt()" class="flex-1 bg-black hover:bg-black/80 text-white font-medium text-xs rounded py-2.5 min-h-[44px] transition-colors">Cetak Struk</button>
                    <a :href="receipt.url" class="flex-1 bg-white hover:bg-black/5 border border-black/20 text-black font-medium text-xs rounded py-2.5 min-h-[44px] flex items-center justify-center transition-colors">Detail</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('cashierPage', () => ({
                products: @json($products),
                cartItems: [],
                search: '',
                paymentMethod: 'tunai',
                paidAmount: 0,
                note: '',
                loading: false,
                receiptVisible: false,
                receipt: { items: [] },

                get filteredProducts() {
                    const term = this.search.toLowerCase().trim();
                    if (!term) return this.products;
                    return this.products.filter(p => p.name.toLowerCase().includes(term));
                },

                get total() {
                    return this.cartItems.reduce((sum, item) => sum + item.price * item.qty, 0);
                },

                get change() {
                    return Math.max(0, this.paidAmount - this.total);
                },

                formatMoney(value) {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value || 0);
                },

                formatDate(value) {
                    if (!value) return '-';
                    return new Date(value).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' });
                },

                addProduct(product) {
                    const existing = this.cartItems.find(i => i.product_id === product.id);
                    const limit = product.track_stock ? product.stock : Infinity;

                    if (existing) {
                        if (existing.qty >= limit) {
                            alert('Stok produk ' + product.name + ' tidak mencukupi.');
                            return;
                        }
                        existing.qty += 1;
                    } else {
                        if (limit <= 0) {
                            alert('Stok produk ' + product.name + ' habis.');
                            return;
                        }
                        this.cartItems.push({
                            product_id: product.id,
                            name: product.name,
                            price: product.price,
                            qty: 1,
                        });
                    }
                },

                updateQty(index, qty) {
                    const item = this.cartItems[index];
                    const product = this.products.find(p => p.id === item.product_id);
                    const limit = product && product.track_stock ? product.stock : Infinity;

                    if (qty < 1) {
                        this.cartItems.splice(index, 1);
                    } else if (qty > limit) {
                        alert('Stok tidak mencukupi.');
                        item.qty = limit;
                    } else {
                        item.qty = qty;
                    }
                },

                removeItem(index) {
                    this.cartItems.splice(index, 1);
                },

                clearCart() {
                    this.cartItems = [];
                    this.paidAmount = 0;
                    this.note = '';
                },

                async saveTransaction() {
                    if (this.cartItems.length === 0) return;

                    this.loading = true;

                    try {
                        const response = await fetch('{{ route('kasir.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                items: this.cartItems.map(i => ({ product_id: i.product_id, quantity: i.qty })),
                                payment_method: this.paymentMethod,
                                paid_amount: this.paymentMethod === 'tunai' ? this.paidAmount : this.total,
                                note: this.note || null,
                            }),
                        });

                        const result = await response.json();

                        if (!response.ok) {
                            const message = result.message || (result.errors ? Object.values(result.errors).flat()[0] : 'Transaksi gagal.');
                            alert(message);
                            return;
                        }

                        const tx = result.transaction;
                        this.receipt = {
                            invoice_number: tx.invoice_number,
                            created_at: tx.created_at,
                            items: tx.items,
                            total: tx.total,
                            paid_amount: tx.paid_amount,
                            change_amount: tx.change_amount,
                            note: tx.note,
                            url: result.receipt_url,
                        };

                        this.receiptVisible = true;

                        this.products = this.products.map(p => {
                            const sold = this.cartItems.find(i => i.product_id === p.id);
                            if (p.track_stock && sold) {
                                return { ...p, stock: p.stock - sold.qty };
                            }
                            return p;
                        });

                        this.clearCart();
                        this.paymentMethod = 'tunai';
                    } catch (e) {
                        alert('Terjadi kesalahan saat menyimpan transaksi.');
                    } finally {
                        this.loading = false;
                    }
                },

                printReceipt() {
                    window.print();
                    this.receiptVisible = false;
                },
            }));
        });
    </script>
@endsection