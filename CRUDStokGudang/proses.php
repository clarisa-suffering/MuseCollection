<?php
include 'conn.php';

if (isset($_POST['add'])) {
    $kode_barang = $_POST['kode_barang'];
    $nama_barang = $_POST['nama_barang'];
    $harga_barang = $_POST['harga_barang'];
    $stok_barang = $_POST['stok_barang'];

    $query = "INSERT INTO stok_barang_gudang (kode_barang, nama_barang, harga_barang, stok_barang) 
          VALUES ('$kode_barang', '$nama_barang', '$harga_barang', '$stok_barang')";
    if (mysqli_query($conn, $query)) {
        echo "Data berhasil ditambahkan!";
    } else {
        echo "Gagal menambahkan data: " . mysqli_error($conn);
    }
}

if (isset($_POST['sub'])) {
    $kode_barang = $_POST['kode_barang'];
    $jumlah = $_POST['jumlah'];

    // Update stok di gudang
    $query = "UPDATE stok_barang_gudang SET stok_barang = stok_barang - $jumlah WHERE kode_barang = '$kode_barang'";
    if (mysqli_query($conn, $query)) {
        echo "Stok berhasil dipindahkan!";
    } else {
        echo "Gagal memindahkan stok: " . mysqli_error($conn);
    }
}

echo "<script>alert('Data berhasil ditambahkan!'); window.location.href='index.php';</script>";

?>
