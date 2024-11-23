<?php
include 'koneksi.php';
include 'produk.php';

$produk = new produk($conn);
$kode_barang = $_GET['kode_barang'];
$harga = $produk->getHargaBarang($kode_barang);
if ($harga != null) {
    echo $harga;
} else {
    echo '0';
}
?>
