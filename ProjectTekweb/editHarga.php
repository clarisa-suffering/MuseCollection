<?php
// Termasuk file yang bersangkutan / berhubungan
include 'koneksi.php';
include 'Produk.php';
include 'RiwayatHarga.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mengambil data dari permintaan POST (jika tidak ada maka diisi null)
    $kode_barang = $_POST['kode_barang'] ?? null;
    $harga_baru = $_POST['harga_baru'] ?? null;

    // Validasi input
    if (!$kode_barang || !$harga_baru) {
        echo json_encode(['success' => false, 'message' => 'Kode barang atau harga baru tidak valid.']);
        exit;
    }

    // Buat objek Produk
    $produk = new Produk($conn);
    $produk->kode_barang = $kode_barang;

    // Cek apakah produk dengan kode tersebut ada
    if ($produk->getHargaBarang($kode_barang)) {
        // Mengubah harga produk
        if ($produk->perubahanHarga($harga_baru)) {
            echo json_encode(['success' => true, 'message' => 'Harga produk berhasil diubah dan disimpan ke riwayat.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal mengubah harga produk.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Produk tidak ditemukan.']);
    }
}
?>
