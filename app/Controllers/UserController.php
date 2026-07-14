<?php

namespace App\Controllers;

use App\Models\UserModel;

class UserController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        // Only allow admin
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/')->with('error', 'Akses ditolak. Hanya Admin yang dapat mengelola pengguna.');
        }

        $data = [
            'title' => 'Manajemen User',
            'users' => $this->userModel->findAll()
        ];
        
        return view('users/index', $data);
    }

    public function store()
    {
        if (session()->get('role') !== 'admin') return redirect()->to('/');

        $this->userModel->save([
            'username' => $this->request->getPost('username'),
            'password' => $this->request->getPost('password'),
            'name'     => $this->request->getPost('name'),
            'role'     => $this->request->getPost('role'),
        ]);

        return redirect()->to('/users')->with('message', 'User berhasil ditambahkan');
    }

    public function update($id)
    {
        if (session()->get('role') !== 'admin') return redirect()->to('/');

        $data = [
            'username' => $this->request->getPost('username'),
            'name'     => $this->request->getPost('name'),
            'role'     => $this->request->getPost('role'),
        ];

        // Only update password if provided
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = $password;
        }

        $this->userModel->update($id, $data);

        return redirect()->to('/users')->with('message', 'User berhasil diperbarui');
    }

    public function delete($id)
    {
        if (session()->get('role') !== 'admin') return redirect()->to('/');

        // Prevent deleting oneself
        if (session()->get('user_id') == $id) {
            return redirect()->to('/users')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri saat sedang login.');
        }

        $this->userModel->delete($id);

        return redirect()->to('/users')->with('message', 'User berhasil dihapus');
    }
}
