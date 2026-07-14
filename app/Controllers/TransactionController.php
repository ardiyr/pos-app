<?php

namespace App\Controllers;

use App\Models\TransactionModel;
use App\Models\TransactionDetailModel;

class TransactionController extends BaseController
{
    protected $transactionModel;
    protected $transactionDetailModel;

    public function __construct()
    {
        $this->transactionModel = new TransactionModel();
        $this->transactionDetailModel = new TransactionDetailModel();
    }

    public function history()
    {
        $data = [
            'title' => 'Riwayat Transaksi',
            'transactions' => $this->transactionModel->orderBy('created_at', 'DESC')->findAll()
        ];
        
        return view('transactions/history', $data);
    }

    // API to get details for modal
    public function details($id)
    {
        $details = $this->transactionDetailModel
            ->select('transaction_details.*, products.name')
            ->join('products', 'products.id = transaction_details.product_id')
            ->where('transaction_id', $id)
            ->findAll();

        return $this->response->setJSON($details);
    }

    public function delete($id)
    {
        $transaction = $this->transactionModel->find($id);
        if (!$transaction) {
            return redirect()->to('/transactions/history')->with('error', 'Transaksi tidak ditemukan.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Get all transaction details
        $details = $this->transactionDetailModel->where('transaction_id', $id)->findAll();
        
        // 2. Restore stock
        $productModel = new \App\Models\ProductModel();
        foreach ($details as $item) {
            $product = $productModel->withDeleted()->find($item['product_id']);
            if ($product) {
                $newStock = $product['stock'] + $item['quantity'];
                $productModel->update($product['id'], ['stock' => $newStock]);
            }
        }

        // 3. Delete details
        $this->transactionDetailModel->where('transaction_id', $id)->delete();
        
        // 4. Delete transaction
        $this->transactionModel->delete($id);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->to('/transactions/history')->with('error', 'Gagal membatalkan transaksi.');
        }

        return redirect()->to('/transactions/history')->with('message', 'Transaksi berhasil dibatalkan dan stok telah dikembalikan.');
    }
}
