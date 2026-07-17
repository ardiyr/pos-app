<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir - <?= esc(config('App')->storeName) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 h-screen flex flex-col" x-data="cashier()">
    
    <!-- Navbar -->
    <nav class="bg-white shadow-sm px-6 py-4 flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-blue-600"><?= esc(config('App')->storeName) ?></h1>
        <div class="flex items-center space-x-4">
            <a href="<?= base_url('index.php/dashboard') ?>" class="text-sm font-medium text-blue-600 hover:text-blue-800 transition">
                &larr; Kembali ke Dashboard
            </a>
            <button @click="openPendingModal()" class="text-sm font-medium text-yellow-600 hover:text-yellow-800 border-l pl-4 transition">
                Daftar Pesanan
            </button>
            <div class="text-gray-500 font-medium border-l pl-4">Kasir: <?= session()->get('username') ?? 'Admin' ?></div>
            <a href="<?= base_url('index.php/logout') ?>" class="text-sm font-semibold text-red-500 hover:text-red-700 ml-4 border-l pl-4">Logout</a>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-1 flex overflow-hidden">
        
        <!-- Left Side: Product Search -->
        <section class="flex-1 p-6 flex flex-col overflow-hidden">
            <div class="mb-4">
                <input 
                    type="text" 
                    x-model="searchQuery" 
                    @input.debounce.500ms="searchProducts()"
                    placeholder="Cari nama barang atau SKU (Minimal 2 huruf)..." 
                    class="w-full p-4 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-lg"
                >
            </div>
            
            <div class="flex-1 overflow-y-auto bg-white rounded-lg border shadow-sm p-4">
                <template x-if="isLoading">
                    <div class="text-center py-4 text-gray-400">Mencari barang...</div>
                </template>
                <template x-if="!isLoading && searchResults.length === 0 && searchQuery.length >= 2">
                    <div class="text-center py-4 text-gray-400">Barang tidak ditemukan</div>
                </template>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <template x-for="product in searchResults" :key="product.id">
                        <div 
                            @click="addToCart(product)"
                            class="border rounded-lg p-4 cursor-pointer hover:border-blue-500 hover:shadow-md transition bg-gray-50"
                        >
                            <div class="font-bold text-gray-800" x-text="product.name"></div>
                            <div class="text-sm text-gray-500 mb-2" x-text="'SKU: ' + product.sku"></div>
                            <div class="flex justify-between items-end">
                                <div class="font-semibold text-blue-600" x-text="formatCurrency(product.sell_price)"></div>
                                <div class="text-xs font-medium px-2 py-1 bg-green-100 text-green-700 rounded-full" x-text="'Stok: ' + product.stock"></div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </section>

        <!-- Right Side: Cart & Checkout -->
        <section class="w-1/3 bg-white border-l shadow-lg flex flex-col">
            <div class="p-4 border-b bg-gray-50">
                <h2 class="text-xl font-bold text-gray-800">Keranjang</h2>
            </div>
            
            <div class="flex-1 overflow-y-auto p-4">
                <template x-if="cart.length === 0">
                    <div class="text-center text-gray-400 mt-10">Keranjang masih kosong</div>
                </template>
                
                <template x-for="(item, index) in cart" :key="item.id">
                    <div class="flex justify-between items-center mb-4 p-3 border rounded-lg hover:bg-gray-50">
                        <div class="flex-1">
                            <div class="font-semibold text-gray-800" x-text="item.name"></div>
                            <div class="text-sm text-gray-500" x-text="formatCurrency(item.sell_price)"></div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <button @click="updateQty(index, -1)" class="w-8 h-8 rounded-full bg-red-100 text-red-600 font-bold hover:bg-red-200">-</button>
                            <span class="font-semibold w-6 text-center" x-text="item.quantity"></span>
                            <button @click="updateQty(index, 1)" class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 font-bold hover:bg-blue-200">+</button>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Total & Payment -->
            <div class="p-6 bg-gray-50 border-t">
                <div class="flex justify-between items-center mb-4">
                    <span class="text-gray-600 font-medium">Total Harga</span>
                    <span class="text-2xl font-bold text-gray-900" x-text="formatCurrency(totalAmount)"></span>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pembeli (Opsional)</label>
                    <input 
                        type="text" 
                        x-model="customerName"
                        class="w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                        placeholder="Contoh: Budi"
                    >
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Bayar</label>
                    <input 
                        type="number" 
                        x-model.number="paymentAmount"
                        class="w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-xl font-semibold"
                        placeholder="0"
                    >
                </div>

                <div class="flex justify-between items-center mb-6">
                    <span class="text-gray-600 font-medium">Kembalian</span>
                    <span class="text-xl font-bold" :class="changeAmount < 0 ? 'text-red-500' : 'text-green-600'" x-text="formatCurrency(changeAmount)"></span>
                </div>

                <div class="flex space-x-2">
                    <button 
                        @click="processCheckout(true)"
                        :disabled="cart.length === 0 || isProcessing"
                        class="w-1/3 py-4 bg-yellow-500 text-white font-bold rounded-lg hover:bg-yellow-600 disabled:opacity-50 disabled:cursor-not-allowed transition shadow-lg text-sm"
                    >
                        Simpan
                    </button>
                    <button 
                        @click="processCheckout(false)"
                        :disabled="cart.length === 0 || changeAmount < 0 || isProcessing"
                        class="w-2/3 py-4 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition shadow-lg text-lg"
                    >
                        <span x-text="isProcessing ? 'Proses...' : 'Bayar & Cetak'"></span>
                    </button>
                </div>
            </div>
        </section>

    </main>

    <!-- Modal Pending Orders -->
    <div x-show="showPendingModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl overflow-hidden flex flex-col max-h-[90vh]">
            <div class="p-6 border-b flex justify-between items-center bg-gray-50">
                <h3 class="text-xl font-bold text-gray-800">Daftar Pesanan Tersimpan</h3>
                <button @click="showPendingModal = false" class="text-gray-400 hover:text-red-500 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="p-6 overflow-y-auto flex-1 bg-gray-100">
                <template x-if="pendingOrders.length === 0">
                    <div class="text-center py-8 text-gray-500">Tidak ada pesanan tersimpan.</div>
                </template>
                <div class="space-y-4">
                    <template x-for="order in pendingOrders" :key="order.id">
                        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                            <div class="flex justify-between items-start mb-3 border-b pb-3">
                                <div>
                                    <h4 class="font-bold text-gray-800" x-text="order.invoice_number"></h4>
                                    <p class="text-sm text-gray-500" x-text="'Pelanggan: ' + (order.customer_name || 'Umum')"></p>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-bold text-blue-600" x-text="formatCurrency(order.total_amount)"></div>
                                    <div class="text-xs text-gray-400" x-text="order.created_at"></div>
                                </div>
                            </div>
                            <div class="mb-4 space-y-1">
                                <template x-for="item in order.details">
                                    <div class="flex justify-between text-sm text-gray-600">
                                        <span x-text="item.quantity + 'x ' + item.name"></span>
                                        <span x-text="formatCurrency(item.subtotal)"></span>
                                    </div>
                                </template>
                            </div>
                            <div class="flex justify-end items-end space-x-3 mt-4 border-t pt-4">
                                <div class="text-right">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Bayar (Rp)</label>
                                    <input type="number" x-model.number="order.temp_payment" class="w-32 p-2 border rounded text-right focus:ring-blue-500 focus:border-blue-500 outline-none">
                                </div>
                                <button 
                                    @click="payPendingOrder(order)"
                                    :disabled="!order.temp_payment || order.temp_payment < order.total_amount || isProcessing"
                                    class="px-4 py-2 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 disabled:opacity-50 transition"
                                >
                                    Bayar & Cetak
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <script>
        function cashier() {
            return {
                searchQuery: '',
                searchResults: [],
                isLoading: false,
                cart: [],
                paymentAmount: '',
                customerName: '',
                isProcessing: false,
                showPendingModal: false,
                pendingOrders: [],

                init() {
                    this.searchProducts();
                },

                async openPendingModal() {
                    this.showPendingModal = true;
                    this.fetchPendingOrders();
                },

                async fetchPendingOrders() {
                    try {
                        const response = await fetch('<?= base_url('index.php/api/orders/pending') ?>');
                        this.pendingOrders = await response.json();
                        this.pendingOrders.forEach(o => o.temp_payment = 0);
                    } catch (error) {
                        console.error('Error fetching pending orders:', error);
                    }
                },

                async payPendingOrder(order) {
                    if (order.temp_payment < order.total_amount) {
                        Swal.fire({icon: 'error', title: 'Kurang', text: 'Pembayaran kurang!'});
                        return;
                    }
                    this.isProcessing = true;
                    try {
                        const response = await fetch('<?= base_url('index.php/api/checkout/pay/') ?>' + order.id, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ payment_amount: order.temp_payment })
                        });
                        const result = await response.json();
                        if (response.ok) {
                            Swal.fire({icon: 'success', title: 'Berhasil', text: 'Pesanan telah dibayar!', showConfirmButton: false, timer: 1500});
                            window.open('<?= base_url('index.php/invoice/print/') ?>' + result.invoice_id, '_blank');
                            this.fetchPendingOrders();
                        } else {
                            Swal.fire({icon: 'error', title: 'Gagal', text: result.message || 'Terjadi kesalahan'});
                        }
                    } catch (error) {
                        console.error(error);
                        Swal.fire({icon: 'error', title: 'Oops...', text: 'Terjadi kesalahan pada server'});
                    }
                    this.isProcessing = false;
                },

                get totalAmount() {
                    return this.cart.reduce((total, item) => total + (item.sell_price * item.quantity), 0);
                },

                get changeAmount() {
                    if (this.paymentAmount === '' || this.paymentAmount === 0) return 0;
                    return this.paymentAmount - this.totalAmount;
                },

                formatCurrency(value) {
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value || 0);
                },

                async searchProducts() {
                    if (this.searchQuery.length < 2) {
                        this.searchResults = [];
                        return;
                    }
                    this.isLoading = true;
                    try {
                        const response = await fetch(`<?= base_url('index.php/api/search') ?>?q=${encodeURIComponent(this.searchQuery)}`);
                        this.searchResults = await response.json();
                    } catch (error) {
                        console.error("Search error:", error);
                    }
                    this.isLoading = false;
                },

                addToCart(product) {
                    const existingItem = this.cart.find(item => item.id === product.id);
                    if (existingItem) {
                        if (existingItem.quantity + 1 > product.stock) {
                            Swal.fire({toast: true, position: 'top-end', icon: 'error', title: 'Stok tidak mencukupi', showConfirmButton: false, timer: 2000});
                            return;
                        }
                        existingItem.quantity++;
                    } else {
                        if (product.stock > 0) {
                            this.cart.push({
                                ...product,
                                quantity: 1
                            });
                        }
                    }
                },

                updateQty(index, change) {
                    const newQty = this.cart[index].quantity + change;
                    if (newQty > 0) {
                        this.cart[index].quantity = newQty;
                    } else {
                        this.cart.splice(index, 1);
                    }
                },

                async processCheckout(isPending = false) {
                    if (this.cart.length === 0) {
                        Swal.fire({icon: 'warning', title: 'Keranjang Kosong', text: 'Mohon masukkan barang ke keranjang terlebih dahulu!'});
                        return;
                    }
                    if (!isPending && this.changeAmount < 0) {
                        Swal.fire({icon: 'error', title: 'Pembayaran Kurang', text: 'Pembayaran kurang Rp ' + Math.abs(this.changeAmount).toLocaleString('id-ID')});
                        return;
                    }
                    
                    this.isProcessing = true;
                    try {
                        const response = await fetch('<?= base_url('index.php/api/checkout') ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                cart: this.cart,
                                total_amount: this.totalAmount,
                                payment_amount: isPending ? 0 : this.paymentAmount,
                                customer_name: this.customerName,
                                is_pending: isPending
                            })
                        });
                        
                        const result = await response.json();
                        
                        if (response.ok) {
                            if (isPending) {
                                Swal.fire({icon: 'success', title: 'Tersimpan', text: 'Pesanan berhasil disimpan!', showConfirmButton: false, timer: 1500});
                            } else {
                                Swal.fire({icon: 'success', title: 'Berhasil', text: 'Transaksi Berhasil!', showConfirmButton: false, timer: 1500});
                                // Print invoice in new tab
                                window.open(`<?= base_url('index.php/invoice/print/') ?>${result.invoice_id}`, '_blank');
                            }
                            
                            // Reset state
                            this.cart = [];
                            this.paymentAmount = '';
                            this.customerName = '';
                            this.searchQuery = '';
                            this.searchResults = [];
                            this.searchProducts(); // Refresh stock
                        } else {
                            Swal.fire({icon: 'error', title: 'Gagal', text: result.message || 'Terjadi kesalahan'});
                        }
                    } catch (error) {
                        console.error('Checkout error:', error);
                        Swal.fire({icon: 'error', title: 'Oops...', text: 'Terjadi kesalahan pada server'});
                    }
                    this.isProcessing = false;
                }
            }
        }
    </script>
</body>
</html>
