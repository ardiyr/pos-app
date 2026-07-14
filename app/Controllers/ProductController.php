<?php

namespace App\Controllers;

use App\Models\ProductModel;

class ProductController extends BaseController
{
    protected $productModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Manajemen Produk',
            'products' => $this->productModel->orderBy('id', 'DESC')->findAll()
        ];
        return view('products/index', $data);
    }

    public function store()
    {
        $this->productModel->save([
            'sku'        => $this->request->getPost('sku'),
            'name'       => $this->request->getPost('name'),
            'buy_price'  => $this->request->getPost('buy_price'),
            'sell_price' => $this->request->getPost('sell_price'),
            'stock'      => $this->request->getPost('stock')
        ]);
        
        return redirect()->to('/products')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function update($id)
    {
        $this->productModel->update($id, [
            'sku'        => $this->request->getPost('sku'),
            'name'       => $this->request->getPost('name'),
            'buy_price'  => $this->request->getPost('buy_price'),
            'sell_price' => $this->request->getPost('sell_price'),
            'stock'      => $this->request->getPost('stock')
        ]);

        return redirect()->to('/products')->with('success', 'Produk berhasil diupdate.');
    }

    public function delete($id)
    {
        try {
            $this->productModel->delete($id);
            return redirect()->to('/products')->with('success', 'Produk berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->to('/products')->with('error', 'Produk gagal dihapus (mungkin sudah ada transaksi terkait).');
        }
    }
}
