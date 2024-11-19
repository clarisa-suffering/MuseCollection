<?php

include 'koneksi.php'; // Database connection
include 'detailProduk.php'; // Your DetailProduk class

// Create an instance of the DetailProduk class
$detailProduk = new DetailProduk($conn);

// Get the kode_barang and ukuran from GET parameters
$kode_barang = $_GET['kode_barang'];
$ukuran = $_GET['ukuran'];

// Call the function to fetch the id_detprod
$id_detprod = $detailProduk->getIdDetprod($kode_barang, $ukuran);

// Return the result as plain text
if ($id_detprod) {
    echo $id_detprod;
} else {
    echo "Product size not found";
}
?>
