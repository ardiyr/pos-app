<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use App\Models\TransactionModel;
use App\Models\TransactionDetailModel;

class CashierController extends BaseController
{
    protected $productModel;
    protected $transactionModel;
    protected $transactionDetailModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->transactionModel = new TransactionModel();
        $this->transactionDetailModel = new TransactionDetailModel();
    }

    public function index()
    {
        return view('cashier/index');
    }

    // API to search products by name or SKU
    public function searchProducts()
    {
        $query = $this->request->getGet('q');
        
        if (empty($query)) {
            return $this->response->setJSON([]);
        }

        $products = $this->productModel
            ->groupStart()
                ->like('LOWER(name)', strtolower($query))
                ->orLike('LOWER(sku)', strtolower($query))
            ->groupEnd()
            ->where('stock >', 0)
            ->findAll(10); // Limit to 10 results

        return $this->response->setJSON($products);
    }

    // API to process the checkout or save order
    public function processCheckout()
    {
        $json = $this->request->getJSON();
        $isPending = isset($json->is_pending) && $json->is_pending === true;
        
        if (!$json || empty($json->cart)) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Invalid data']);
        }

        if (!$isPending && (!isset($json->payment_amount) || $json->payment_amount < $json->total_amount)) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Invalid data or insufficient payment']);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Create Transaction
        $invoiceNumber = 'INV-' . date('YmdHis') . '-' . rand(100, 999);
        $transactionData = [
            'invoice_number' => $invoiceNumber,
            'customer_name'  => isset($json->customer_name) && trim($json->customer_name) !== '' ? trim($json->customer_name) : null,
            'total_amount'   => $json->total_amount,
            'payment_amount' => $isPending ? 0 : $json->payment_amount,
            'change_amount'  => $isPending ? 0 : ($json->payment_amount - $json->total_amount),
            'status'         => $isPending ? 'pending' : 'completed'
        ];
        
        $this->transactionModel->insert($transactionData);
        $transactionId = $this->transactionModel->insertID();

        // 2. Create Transaction Details and Deduct Stock
        foreach ($json->cart as $item) {
            $product = $this->productModel->find($item->id);
            if (!$product || $product['stock'] < $item->quantity) {
                $db->transRollback();
                return $this->response->setStatusCode(400)->setJSON(['message' => "Stock for {$item->name} is insufficient"]);
            }

            // Insert Detail
            $this->transactionDetailModel->insert([
                'transaction_id' => $transactionId,
                'product_id'     => $item->id,
                'quantity'       => $item->quantity,
                'unit_price'     => $item->sell_price,
                'subtotal'       => $item->sell_price * $item->quantity
            ]);

            // Deduct Stock
            $this->productModel->update($item->id, [
                'stock' => $product['stock'] - $item->quantity
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setStatusCode(500)->setJSON(['message' => 'Transaction failed']);
        }

        return $this->response->setJSON([
            'success' => true,
            'transaction_id' => $transactionId,
            'invoice_id' => $transactionId,
            'is_pending' => $isPending
        ]);
    }

    // API to get pending orders
    public function getPendingOrders()
    {
        $orders = $this->transactionModel->where('status', 'pending')->orderBy('created_at', 'DESC')->findAll();
        // Load details for each order to show in UI
        foreach ($orders as &$order) {
            $order['details'] = $this->transactionDetailModel
                ->select('transaction_details.*, products.name')
                ->join('products', 'products.id = transaction_details.product_id')
                ->where('transaction_id', $order['id'])
                ->findAll();
        }
        return $this->response->setJSON($orders);
    }

    // API to pay pending order
    public function payOrder($id)
    {
        $json = $this->request->getJSON();
        
        $order = $this->transactionModel->find($id);
        if (!$order || $order['status'] !== 'pending') {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Pesanan tidak ditemukan atau sudah selesai']);
        }

        if (!isset($json->payment_amount) || $json->payment_amount < $order['total_amount']) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Pembayaran kurang']);
        }

        $this->transactionModel->update($id, [
            'payment_amount' => $json->payment_amount,
            'change_amount'  => $json->payment_amount - $order['total_amount'],
            'status'         => 'completed'
        ]);

        return $this->response->setJSON([
            'success' => true,
            'transaction_id' => $id,
            'invoice_id' => $id
        ]);
    }

    public function invoicePrint($id)
    {
        $transaction = $this->transactionModel->find($id);
        if (!$transaction) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $details = $this->transactionDetailModel
            ->select('transaction_details.*, products.name')
            ->join('products', 'products.id = transaction_details.product_id')
            ->where('transaction_id', $id)
            ->findAll();

        $data = [
            'transaction' => $transaction,
            'details'     => $details
        ];

        return view('cashier/invoice_print', $data);
    }
}
