<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= esc(config('App')->storeName) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-blue-600 p-8 text-center">
            <h1 class="text-3xl font-bold text-blue-100"><?= esc(config('App')->storeName) ?></h1>
            <p class="text-blue-200 mt-2 text-sm">Sistem Kasir Satset & Modern</p>
        </div>
        
        <div class="p-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Selamat Datang</h2>
            
            <?php if(session()->getFlashdata('error')): ?>
                <div class="mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg text-sm text-center">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('index.php/login/authenticate') ?>" method="POST" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <input type="text" name="username" required autofocus autocomplete="username"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition" 
                        placeholder="Masukkan username">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" required autocomplete="current-password"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition" 
                        placeholder="Masukkan password">
                </div>

                <button type="submit" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition duration-200 ease-in-out transform hover:-translate-y-0.5 mt-4">
                    Masuk
                </button>
            </form>
            
            <div class="mt-8 text-center text-sm text-gray-400">
                &copy; <?= date('Y') ?> <?= esc(config('App')->storeName) ?>. All rights reserved.
            </div>
        </div>
    </div>

</body>
</html>
