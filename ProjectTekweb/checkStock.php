<?php
include 'koneksi.php'; // Database connection
include 'detailProduk.php'; // Your DetailProduk class

$detailProduk = new DetailProduk($conn);
$kode_barang = $_GET['kode_barang'];
$ukuran = $_GET['ukuran'];
$jumlah = $_GET['jumlah'];

$stock = $detailProduk->checkStockToko($kode_barang, $ukuran);
if($stock >= $jumlah) {
    echo 'Stock available';
} else {
    echo 'Not enough stock';
}
?>

