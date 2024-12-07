<?php

include 'koneksi.php'; 
include 'detailProduk.php'; 

$detailProduk = new DetailProduk($conn);

$kode_barang = $_GET['kode_barang'];
$ukuran = $_GET['ukuran'];

$id_detprod = $detailProduk->getId($kode_barang, $ukuran);

if ($id_detprod) {
    echo $id_detprod;
} else {
    echo "Product not found";
}
?>
