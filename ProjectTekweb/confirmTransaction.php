<?php
include 'koneksi.php'; // Database connection
include 'transaksi.php';  // Your Transaksi class
include 'detailTransaksi.php'; // Your DetailTransaksi class
include 'pelanggan.php'; // Include Pelanggan class

// Create an instance of Pelanggan
$pelanggan = new Pelanggan($conn);
$pelanggan->nama = $_POST['nama'];
$pelanggan->nomor_telepon = $_POST['nomor_telepon'];
$pelanggan->alamat = $_POST['alamat'];

// Insert Pelanggan into the database and get the id_pelanggan
$id_pelanggan = $pelanggan->insertPelanggan();

// If Pelanggan insertion was successful, proceed to create the transaction
if ($id_pelanggan) {
    // Create a new Transaksi object
    $transaksi = new Transaksi($conn);
    $transaksi->id_pelanggan=$id_pelanggan;
    $transaksi->kategori_penjualan = $_POST['kategori_penjualan']; // Assuming 'kategori_penjualan' is sent with the POST request
    $transaksi->harga_total = $_POST['harga_total'];

    // Get details from the POST data
    $details = json_decode($_POST['details'], true);

    // Add each detail to the transaction using addDetailTransaksi
    foreach ($details as $detail) {
        $id_detprod = $detail['id_detprod'];
        $jumlah = $detail['jumlah'];
        $subtotal = $detail['subtotal'];
        
        // Use the method to add the detail to the transaction
        $transaksi->addDetailTransaksi($id_detprod, $jumlah, $subtotal);
    }

    // Insert the transaction and all related details in one go
    if ($transaksi->insertTransaksi()) {
        echo 'Success';
    } else {
        echo 'Failed to insert transaction.';
    }
} else {
    echo 'Failed to insert customer.';
}
