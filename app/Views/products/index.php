<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div x-data="productManager()">
    
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-xl font-bold text-gray-800">Daftar Produk</h3>
        <button @click="openModal('add')" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700 transition">
            + Tambah Produk
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b">
                        <th class="py-3 px-6 font-semibold text-sm text-gray-600">ID</th>
                        <th class="py-3 px-6 font-semibold text-sm text-gray-600">SKU</th>
                        <th class="py-3 px-6 font-semibold text-sm text-gray-600">Nama Barang</th>
                        <th class="py-3 px-6 font-semibold text-sm text-gray-600">Harga Beli</th>
                        <th class="py-3 px-6 font-semibold text-sm text-gray-600">Harga Jual</th>
                        <th class="py-3 px-6 font-semibold text-sm text-gray-600">Stok</th>
                        <th class="py-3 px-6 font-semibold text-sm text-gray-600 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if(empty($products)): ?>
                    <tr>
                        <td colspan="7" class="py-4 px-6 text-center text-gray-500">Belum ada produk</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach($products as $product): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-6 text-gray-500">#<?= $product['id'] ?></td>
                            <td class="py-3 px-6 font-medium text-gray-800"><?= esc($product['sku']) ?></td>
                            <td class="py-3 px-6"><?= esc($product['name']) ?></td>
                            <td class="py-3 px-6">Rp <?= number_format($product['buy_price'], 0, ',', '.') ?></td>
                            <td class="py-3 px-6 text-blue-600 font-semibold">Rp <?= number_format($product['sell_price'], 0, ',', '.') ?></td>
                            <td class="py-3 px-6">
                                <?php if($product['stock'] <= 5): ?>
                                    <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-bold"><?= $product['stock'] ?></span>
                                <?php else: ?>
                                    <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold"><?= $product['stock'] ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-6 text-right space-x-2 flex justify-end">
                                <button @click="openModal('edit', <?= htmlspecialchars(json_encode($product), ENT_QUOTES, 'UTF-8') ?>)" class="text-blue-500 hover:text-blue-700">Edit</button>
                                
                                <form action="<?= base_url('index.php/products/delete/' . $product['id']) ?>" method="post" class="inline" onsubmit="confirmDelete(event, 'Apakah Anda yakin ingin menghapus produk ini?');">
                                    <button type="submit" class="text-red-500 hover:text-red-700">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form -->
    <div x-show="isModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div x-show="isModalOpen" @click="closeModal()" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div x-show="isModalOpen" x-transition.scale class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form :action="formAction" method="POST">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="mb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title" x-text="modalTitle"></h3>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">SKU</label>
                                <input type="text" name="sku" x-model="formData.sku" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nama Barang</label>
                                <input type="text" name="name" x-model="formData.name" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Harga Beli</label>
                                    <input type="number" name="buy_price" x-model="formData.buy_price" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Harga Jual</label>
                                    <input type="number" name="sell_price" x-model="formData.sell_price" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Stok</label>
                                <input type="number" name="stock" x-model="formData.stock" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Simpan
                        </button>
                        <button type="button" @click="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<script>
    function productManager() {
        return {
            isModalOpen: false,
            modalMode: 'add',
            
            formData: {
                id: null,
                sku: '',
                name: '',
                buy_price: '',
                sell_price: '',
                stock: ''
            },
            
            get modalTitle() {
                return this.modalMode === 'add' ? 'Tambah Produk Baru' : 'Edit Produk';
            },
            
            get formAction() {
                return this.modalMode === 'add' ? '<?= base_url('index.php/products/store') ?>' : '<?= base_url('index.php/products/update/') ?>' + this.formData.id;
            },
            
            openModal(mode, data = null) {
                this.modalMode = mode;
                if (mode === 'edit' && data) {
                    this.formData = {
                        id: data.id,
                        sku: data.sku,
                        name: data.name,
                        buy_price: data.buy_price,
                        sell_price: data.sell_price,
                        stock: data.stock
                    };
                } else {
                    this.formData = { id: null, sku: '', name: '', buy_price: '', sell_price: '', stock: '' };
                }
                this.isModalOpen = true;
            },
            
            closeModal() {
                this.isModalOpen = false;
            }
        }
    }
</script>

<?= $this->endSection() ?>
