<?php
include 'koneksi.php'; 
include 'detailProduk.php'; 

$detailProduk = new DetailProduk($conn);
$kode_barang = $_GET['kode_barang'];
$ukuran = $_GET['ukuran'];
$jumlah = $_GET['jumlah'];
$kategori_penjualan=$_GET['kategori_penjualan'];

if($kategori_penjualan === 'retail'){
$stock = $detailProduk->cekStokToko($kode_barang, $ukuran);
} else{
$stock = $detailProduk->cekStokGudang($kode_barang, $ukuran);
}

if (is_numeric($stock)) {
    echo $stock;
} else {
    echo "Produk tidak ditemukan";
}
?>
