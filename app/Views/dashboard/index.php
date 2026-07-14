<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    
    <!-- Sales Today -->
    <div class="bg-white rounded-xl shadow-sm border p-6 flex items-center space-x-4">
        <div class="w-14 h-14 rounded-full bg-green-100 flex items-center justify-center text-green-600 text-2xl font-bold">
            Rp
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium">Penjualan Hari Ini</p>
            <p class="text-2xl font-bold text-gray-800"><?= number_format($salesToday, 0, ',', '.') ?></p>
        </div>
    </div>

    <!-- Transactions Today -->
    <div class="bg-white rounded-xl shadow-sm border p-6 flex items-center space-x-4">
        <div class="w-14 h-14 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-2xl font-bold">
            #
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium">Transaksi Hari Ini</p>
            <p class="text-2xl font-bold text-gray-800"><?= $transactionsToday ?></p>
        </div>
    </div>

    <!-- Low Stock -->
    <div class="bg-white rounded-xl shadow-sm border p-6 flex items-center space-x-4">
        <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center text-red-600 text-2xl font-bold">
            !
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium">Barang Menipis (<= 5)</p>
            <p class="text-2xl font-bold text-gray-800"><?= count($lowStockProducts) ?></p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    
    <!-- Low Stock Table -->
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50">
            <h3 class="font-semibold text-gray-800">Daftar Stok Menipis</h3>
        </div>
        <div class="p-0">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b">
                        <th class="py-3 px-6 font-semibold text-sm text-gray-600">Nama Barang</th>
                        <th class="py-3 px-6 font-semibold text-sm text-gray-600">Sisa Stok</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if(empty($lowStockProducts)): ?>
                    <tr>
                        <td colspan="2" class="py-4 px-6 text-center text-gray-500">Stok barang aman</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach($lowStockProducts as $product): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-6"><?= esc($product['name']) ?></td>
                            <td class="py-3 px-6">
                                <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-bold"><?= $product['stock'] ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50 flex justify-between items-center">
            <h3 class="font-semibold text-gray-800">5 Transaksi Terakhir</h3>
        </div>
        <div class="p-0">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b">
                        <th class="py-3 px-6 font-semibold text-sm text-gray-600">Invoice</th>
                        <th class="py-3 px-6 font-semibold text-sm text-gray-600">Total</th>
                        <th class="py-3 px-6 font-semibold text-sm text-gray-600">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if(empty($recentTransactions)): ?>
                    <tr>
                        <td colspan="3" class="py-4 px-6 text-center text-gray-500">Belum ada transaksi</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach($recentTransactions as $tx): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-6 font-medium text-blue-600"><?= esc($tx['invoice_number']) ?></td>
                            <td class="py-3 px-6 text-gray-800 font-semibold">Rp <?= number_format($tx['total_amount'], 0, ',', '.') ?></td>
                            <td class="py-3 px-6 text-gray-500 text-sm"><?= date('H:i, d/m/Y', strtotime($tx['created_at'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?= $this->endSection() ?>
