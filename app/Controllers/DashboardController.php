<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\TransactionModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $productModel = new ProductModel();
        $transactionModel = new TransactionModel();
        
        $today = date('Y-m-d');
        
        // Total Sales Today
        $salesToday = $transactionModel
            ->selectSum('total_amount')
            ->where('created_at >=', $today . ' 00:00:00')
            ->where('created_at <=', $today . ' 23:59:59')
            ->first();
            
        // Transaction Count Today
        $transactionsToday = $transactionModel
            ->where('created_at >=', $today . ' 00:00:00')
            ->where('created_at <=', $today . ' 23:59:59')
            ->countAllResults();
            
        // Low Stock Products (<= 5)
        $lowStockProducts = $productModel
            ->where('stock <=', 5)
            ->findAll();
            
        // Recent Transactions
        $recentTransactions = $transactionModel
            ->orderBy('created_at', 'DESC')
            ->findAll(5);
            
        $data = [
            'title'             => 'Dashboard',
            'salesToday'        => $salesToday['total_amount'] ?? 0,
            'transactionsToday' => $transactionsToday,
            'lowStockProducts'  => $lowStockProducts,
            'recentTransactions'=> $recentTransactions
        ];
        
        return view('dashboard/index', $data);
    }
}
