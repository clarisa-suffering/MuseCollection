<?php
// confirmTransaction.php
include 'koneksi.php'; // Database connection
include 'transaksi.php';  // Your Transaksi class
include 'detailTransaksi.php'; // Your DetailTransaksi class

$transaksi = new Transaksi($conn);
$detailTransaksi = new DetailTransaksi($conn);

// Calculate total price from products
$products = json_decode($_POST['products']);
$totalPrice = 0;

foreach ($products as $product) {
    $totalPrice += $product->subtotal;
}

// Insert the main transaction
$transaksi->kategori_penjualan = 'Retail';
$transaksi->harga_total = $totalPrice;  // Use calculated total
$transaksi->status_transaksi = 'Confirmed';
$transaksi->tanggal_transaksi = date('Y-m-d H:i:s');
$transaksi->insertTransaksi();

// Insert the detail transaksi
foreach ($products as $product) {
    $detailTransaksi->id_detprod = $product->kode_produk; // Ensure this maps to the correct product ID
    $detailTransaksi->jumlah = $product->jumlah;
    $detailTransaksi->subtotal = $product->subtotal;
    $detailTransaksi->id_transaksi = $transaksi->id_transaksi; 
    $detailTransaksi->insertDetailTransaksi();
}

echo 'Success';
?>