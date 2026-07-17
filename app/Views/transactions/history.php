<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div x-data="transactionHistory()">
    
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-xl font-bold text-gray-800">Riwayat Transaksi</h3>
    </div>

    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b">
                        <th class="py-3 px-6 font-semibold text-sm text-gray-600">Waktu</th>
                        <th class="py-3 px-6 font-semibold text-sm text-gray-600">No. Invoice</th>
                        <th class="py-3 px-6 font-semibold text-sm text-gray-600">Nama Pembeli</th>
                        <th class="py-3 px-6 font-semibold text-sm text-gray-600">Total Belanja</th>
                        <th class="py-3 px-6 font-semibold text-sm text-gray-600">Kasir</th>
                        <th class="py-3 px-6 font-semibold text-sm text-gray-600 text-center">Status</th>
                        <th class="py-3 px-6 font-semibold text-sm text-gray-600 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if(empty($transactions)): ?>
                    <tr>
                        <td colspan="6" class="py-4 px-6 text-center text-gray-500">Belum ada transaksi</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach($transactions as $tx): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-6 text-sm text-gray-500"><?= date('H:i - d/m/Y', strtotime($tx['created_at'])) ?></td>
                            <td class="py-3 px-6 font-medium text-blue-600"><?= esc($tx['invoice_number']) ?></td>
                            <td class="py-3 px-6 text-gray-800">
                                <?= !empty($tx['customer_name']) ? esc($tx['customer_name']) : '<span class="text-gray-400 italic">Tanpa Nama</span>' ?>
                            </td>
                            <td class="py-3 px-6 font-semibold">Rp <?= number_format($tx['total_amount'], 0, ',', '.') ?></td>
                            <td class="py-3 px-6 text-gray-600">Admin</td>
                            <td class="py-3 px-6 text-center">
                                <?php if(isset($tx['status']) && $tx['status'] === 'pending'): ?>
                                    <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2.5 py-0.5 rounded border border-yellow-300">Pending</span>
                                <?php else: ?>
                                    <span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded border border-green-300">Selesai</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-6 text-right space-x-2 flex justify-end">
                                <button @click="openDetails(<?= $tx['id'] ?>, '<?= esc($tx['invoice_number']) ?>')" class="text-blue-500 hover:text-blue-700 bg-blue-50 px-3 py-1 rounded text-sm font-medium transition">Detail</button>
                                <?php if(!isset($tx['status']) || $tx['status'] === 'completed'): ?>
                                <a href="<?= base_url('index.php/invoice/print/' . $tx['id']) ?>" target="_blank" class="text-green-600 hover:text-green-800 bg-green-50 px-3 py-1 rounded text-sm font-medium transition">Cetak Ulang</a>
                                <?php endif; ?>
                                <form action="<?= base_url('index.php/transactions/delete/' . $tx['id']) ?>" method="post" class="inline" onsubmit="confirmDelete(event, 'Apakah Anda yakin ingin membatalkan transaksi ini? Stok barang akan dikembalikan secara otomatis.');">
                                    <button type="submit" class="text-red-500 hover:text-red-700 bg-red-50 px-3 py-1 rounded text-sm font-medium transition">Batal</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Detail Transaksi -->
    <div x-show="isModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div x-show="isModalOpen" @click="closeModal()" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div x-show="isModalOpen" x-transition.scale class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="mb-4 flex justify-between items-center border-b pb-3">
                        <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                            Detail Transaksi <span class="text-blue-600" x-text="selectedInvoice"></span>
                        </h3>
                        <button @click="closeModal()" class="text-gray-400 hover:text-gray-600 font-bold text-xl">&times;</button>
                    </div>
                    
                    <div class="space-y-4">
                        <template x-if="isLoading">
                            <div class="text-center py-4 text-gray-500">Memuat detail...</div>
                        </template>
                        
                        <template x-if="!isLoading && details.length > 0">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50 border-b">
                                        <th class="py-2 px-4 font-semibold text-sm text-gray-600">Nama Barang</th>
                                        <th class="py-2 px-4 font-semibold text-sm text-gray-600 text-center">Qty</th>
                                        <th class="py-2 px-4 font-semibold text-sm text-gray-600 text-right">Harga Satuan</th>
                                        <th class="py-2 px-4 font-semibold text-sm text-gray-600 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <template x-for="item in details" :key="item.id">
                                        <tr>
                                            <td class="py-2 px-4" x-text="item.name"></td>
                                            <td class="py-2 px-4 text-center" x-text="item.quantity"></td>
                                            <td class="py-2 px-4 text-right" x-text="formatCurrency(item.unit_price)"></td>
                                            <td class="py-2 px-4 text-right font-medium" x-text="formatCurrency(item.subtotal)"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </template>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" @click="closeModal()" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    function transactionHistory() {
        return {
            isModalOpen: false,
            isLoading: false,
            selectedInvoice: '',
            details: [],
            
            formatCurrency(value) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value || 0);
            },
            
            async openDetails(id, invoiceNumber) {
                this.selectedInvoice = invoiceNumber;
                this.isModalOpen = true;
                this.isLoading = true;
                this.details = [];
                
                try {
                    const response = await fetch(`<?= base_url('index.php/transactions/details/') ?>${id}`);
                    this.details = await response.json();
                } catch (error) {
                    console.error("Detail error:", error);
                    Swal.fire({icon: 'error', title: 'Oops...', text: 'Gagal memuat detail transaksi.'});
                }
                
                this.isLoading = false;
            },
            
            closeModal() {
                this.isModalOpen = false;
            }
        }
    }
</script>

<?= $this->endSection() ?>
