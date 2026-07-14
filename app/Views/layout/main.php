<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? esc(config('App')->storeName) ?></title>                                    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 h-screen flex overflow-hidden">
    
    <!-- Sidebar -->
    <aside class="w-64 bg-slate-800 text-white flex flex-col hidden md:flex">
        <div class="p-6 pb-2">
            <h1 class="text-2xl font-bold text-blue-400"><?= esc(config('App')->storeName) ?></h1>
        </div>
        <nav class="flex-1 px-4 py-6 space-y-2">
            <a href="<?= base_url('index.php/dashboard') ?>" class="block px-4 py-3 rounded-lg <?= (url_is('dashboard*') || url_is('index.php/dashboard*')) ? 'bg-blue-600' : 'hover:bg-slate-700' ?> transition">
                Dashboard
            </a>
            <a href="<?= base_url('index.php/') ?>" class="block px-4 py-3 rounded-lg <?= (url_is('/') || url_is('index.php')) ? 'bg-blue-600' : 'hover:bg-slate-700' ?> transition">
                Kasir
            </a>
            <a href="<?= base_url('index.php/products') ?>" class="block px-4 py-3 rounded-lg <?= (url_is('products*') || url_is('index.php/products*')) ? 'bg-blue-600' : 'hover:bg-slate-700' ?> transition">
                Manajemen Produk
            </a>
            <a href="<?= base_url('index.php/transactions/history') ?>" class="block px-4 py-3 rounded-lg <?= (url_is('transactions*') || url_is('index.php/transactions*')) ? 'bg-blue-600' : 'hover:bg-slate-700' ?> transition">
                Riwayat Transaksi
            </a>
            <?php if(session()->get('role') === 'admin'): ?>
            <a href="<?= base_url('index.php/users') ?>" class="block px-4 py-3 rounded-lg <?= (url_is('users*') || url_is('index.php/users*')) ? 'bg-blue-600' : 'hover:bg-slate-700' ?> transition">
                Manajemen User
            </a>
            <?php endif; ?>
        </nav>
        <div class="p-4 bg-slate-900 border-t border-slate-700">
            <a href="<?= base_url('index.php/logout') ?>" class="block w-full text-center py-2 px-4 bg-red-600 hover:bg-red-700 text-white rounded-lg transition text-sm font-semibold mb-3">
                Logout
            </a>
            <div class="text-sm text-slate-400 text-center">
                &copy; <?= date('Y') ?> <?= esc(config('App')->storeName) ?>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Topbar -->
        <header class="bg-white shadow-sm border-b px-6 py-4 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800"><?= $title ?? 'Dashboard' ?></h2>
            <div class="flex items-center space-x-4">
                <span class="text-gray-600 font-medium">Halo, Admin</span>
                <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold">
                    A
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <main class="flex-1 overflow-y-auto p-6">
            <?php if (session()->getFlashdata('success')) : ?>
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>
            
            <?php if (session()->getFlashdata('error')) : ?>
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
        </main>
    </div>

    <!-- Global SweetAlert Flashdata -->
    <?php if(session()->getFlashdata('message')): ?>
    <script>
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: '<?= session()->getFlashdata('message') ?>',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    </script>
    <?php endif; ?>

    <?php if(session()->getFlashdata('error')): ?>
    <script>
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            title: '<?= session()->getFlashdata('error') ?>',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true
        });
    </script>
    <?php endif; ?>

    <!-- Global Delete Confirmation -->
    <script>
        function confirmDelete(event, message) {
            event.preventDefault();
            const form = event.target;
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, eksekusi!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>
</body>
</html>
