<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div x-data="userManagement()">
    
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-xl font-bold text-gray-800">Manajemen User</h3>
        <button @click="openModal('add')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
            + Tambah User
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b">
                        <th class="py-3 px-6 font-semibold text-sm text-gray-600">Username</th>
                        <th class="py-3 px-6 font-semibold text-sm text-gray-600">Nama Lengkap</th>
                        <th class="py-3 px-6 font-semibold text-sm text-gray-600">Role</th>
                        <th class="py-3 px-6 font-semibold text-sm text-gray-600 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if(empty($users)): ?>
                    <tr>
                        <td colspan="4" class="py-4 px-6 text-center text-gray-500">Belum ada user</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach($users as $user): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-6 text-gray-800 font-medium"><?= esc($user['username']) ?></td>
                            <td class="py-3 px-6 text-gray-600"><?= esc($user['name']) ?></td>
                            <td class="py-3 px-6">
                                <span class="px-2 py-1 rounded text-xs font-semibold <?= $user['role'] === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-green-100 text-green-700' ?>">
                                    <?= esc(strtoupper($user['role'])) ?>
                                </span>
                            </td>
                            <td class="py-3 px-6 text-right space-x-2 flex justify-end">
                                <button @click="openModal('edit', <?= htmlspecialchars(json_encode($user), ENT_QUOTES, 'UTF-8') ?>)" class="text-blue-500 hover:text-blue-700 bg-blue-50 px-3 py-1 rounded text-sm font-medium transition">Edit</button>
                                
                                <?php if($user['id'] != session()->get('user_id')): ?>
                                <form action="<?= base_url('index.php/users/delete/' . $user['id']) ?>" method="post" class="inline" onsubmit="confirmDelete(event, 'Apakah Anda yakin ingin menghapus akun ini?');">
                                    <button type="submit" class="text-red-500 hover:text-red-700 bg-red-50 px-3 py-1 rounded text-sm font-medium transition">Hapus</button>
                                </form>
                                <?php endif; ?>
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
            <div x-show="isModalOpen" @click="closeModal()" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div x-show="isModalOpen" x-transition.scale class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form :action="formAction" method="POST">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="mb-4 border-b pb-3">
                            <h3 class="text-lg leading-6 font-bold text-gray-900" x-text="modalMode === 'add' ? 'Tambah User Baru' : 'Edit User'"></h3>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                                <input type="text" name="username" x-model="formData.username" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                                <input type="text" name="name" x-model="formData.name" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                                <select name="role" x-model="formData.role" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 bg-white">
                                    <option value="kasir">Kasir</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Password <span x-show="modalMode === 'edit'" class="text-xs text-gray-500 font-normal">(Kosongkan jika tidak ingin mengubah)</span>
                                </label>
                                <input type="password" name="password" :required="modalMode === 'add'" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse space-y-2 sm:space-y-0 sm:space-x-2 sm:space-x-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:w-auto sm:text-sm">
                            Simpan
                        </button>
                        <button type="button" @click="closeModal()" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function userManagement() {
        return {
            isModalOpen: false,
            modalMode: 'add',
            formData: {
                id: null,
                username: '',
                name: '',
                role: 'kasir'
            },
            
            get formAction() {
                return this.modalMode === 'add' ? '<?= base_url('index.php/users/store') ?>' : '<?= base_url('index.php/users/update/') ?>' + this.formData.id;
            },
            
            openModal(mode, data = null) {
                this.modalMode = mode;
                if (mode === 'edit' && data) {
                    this.formData = {
                        id: data.id,
                        username: data.username,
                        name: data.name,
                        role: data.role
                    };
                } else {
                    this.formData = {
                        id: null,
                        username: '',
                        name: '',
                        role: 'kasir'
                    };
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
