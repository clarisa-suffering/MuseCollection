<?php
include 'koneksi.php'; 
include 'transaksi.php';  
include 'detailTransaksi.php'; 
include 'pelanggan.php'; 

$pelanggan = new Pelanggan($conn);
$pelanggan->nama = $_POST['nama'];
$pelanggan->nomor_telepon = $_POST['nomor_telepon'];
$pelanggan->alamat = $_POST['alamat'];

// Insert pelanggan ke database
$id_pelanggan = $pelanggan->mencatatPelanggan();

if ($id_pelanggan) {
    $transaksi = new Transaksi($conn);
    $transaksi->id_pelanggan = $id_pelanggan;
    $transaksi->kategori_penjualan = $_POST['kategori_penjualan'];
    $transaksi->harga_total = $_POST['harga_total'];


    $details = json_decode($_POST['details'], true);

    foreach ($details as $detail) {
        $id_detprod = $detail['id_detprod'];
        $jumlah = $detail['jumlah'];
        $subtotal = $detail['subtotal'];

        $detailTransaksi = new DetailTransaksi($conn);
        $detailTransaksi->id_detprod = $id_detprod;
        $detailTransaksi->jumlah = $jumlah;
        $detailTransaksi->subtotal = $subtotal;

        $transaksi->details[] = $detailTransaksi;
    }
    
    // Mencatat transaksi
    if ($transaksi->mencatatTransaksi()) {
        echo "Sukses"; 
    } else {
        echo "Error mencatat transaksi";
    }
}
?>
