<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice <?= esc($transaction['invoice_number']) ?></title>
    <style>
        @page { margin: 0; }
        body {
            font-family: 'Courier New', Courier, monospace;
            margin: 0;
            padding: 10px;
            width: 300px; /* Adjust for thermal printer */
            font-size: 12px;
            color: #000;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .divider { border-top: 1px dashed #000; margin: 5px 0; }
        .mb-2 { margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; }
        .btn-print {
            display: block;
            width: 100%;
            padding: 10px;
            background: #3b82f6;
            color: #fff;
            text-align: center;
            border: none;
            cursor: pointer;
            margin-bottom: 15px;
            font-family: Arial, sans-serif;
            font-weight: bold;
            border-radius: 5px;
        }
        @media print {
            .no-print { display: none; }
            body { width: 100%; }
        }
    </style>
</head>
<body>

    <button class="btn-print no-print" onclick="window.print()">Print Struk</button>

    <div class="text-center mb-2">
        <h2 style="margin: 0;"><?= esc(config('App')->storeName) ?></h2>
        <p style="margin: 2px 0;"><?= esc(config('App')->storeAddress) ?></p>
        <p style="margin: 2px 0;">Telp: <?= esc(config('App')->storePhone) ?></p>
    </div>

    <div class="divider"></div>

    <p style="margin: 2px 0;">No   : <?= esc($transaction['invoice_number']) ?></p>
    <p style="margin: 2px 0;">Tgl  : <?= date('d/m/Y H:i', strtotime($transaction['created_at'])) ?></p>
    <p style="margin: 2px 0;">Kasir: Admin</p>
    <?php if(!empty($transaction['customer_name'])): ?>
    <p style="margin: 2px 0;">Pelanggan: <?= esc($transaction['customer_name']) ?></p>
    <?php endif; ?>

    <div class="divider"></div>

    <table>
        <?php foreach ($details as $item): ?>
        <tr>
            <td colspan="3"><?= esc($item['name']) ?></td>
        </tr>
        <tr>
            <td><?= $item['quantity'] ?> x</td>
            <td><?= number_format($item['unit_price'], 0, ',', '.') ?></td>
            <td class="text-right"><?= number_format($item['subtotal'], 0, ',', '.') ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <div class="divider"></div>

    <table>
        <tr>
            <td class="font-bold">Total</td>
            <td class="text-right font-bold"><?= number_format($transaction['total_amount'], 0, ',', '.') ?></td>
        </tr>
        <tr>
            <td>Tunai</td>
            <td class="text-right"><?= number_format($transaction['payment_amount'], 0, ',', '.') ?></td>
        </tr>
        <tr>
            <td>Kembali</td>
            <td class="text-right"><?= number_format($transaction['change_amount'], 0, ',', '.') ?></td>
        </tr>
    </table>

    <div class="divider"></div>

    <div class="text-center" style="margin-top: 10px;">
        <p style="margin: 2px 0;">Terima Kasih</p>
        <p style="margin: 2px 0;">Barang yang sudah dibeli tidak dapat ditukar/dikembalikan</p>
    </div>

</body>
</html>
