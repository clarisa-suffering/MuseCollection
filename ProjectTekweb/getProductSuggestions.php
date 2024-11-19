<?php
include 'koneksi.php'; // Database connection
include 'produk.php';  // Your Produk class

$produk = new Produk($conn);
$kode_barang = $_GET['kode_barang'];
$suggestions = $produk->getProdukByKode($kode_barang);

while ($row = $suggestions->fetch_assoc()) {
    echo '<div>' . $row['kode_barang'] . '</div>';
}
?>
