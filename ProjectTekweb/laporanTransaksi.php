<?php
// Include koneksi database
include 'koneksi.php';

// Default query untuk menampilkan semua data
$query = "SELECT timestamp_transaksi, nama_pelanggan, kode_barang, nama_barang, ukuran, jumlah, harga_satuan, harga_total FROM laporan_transaksi";

// Jika filter tanggal diterapkan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['start_date']) && isset($_POST['end_date'])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Query untuk menampilkan data sesuai dengan rentang tanggal
    $query .= " WHERE DATE(timestamp_transaksi) BETWEEN '$start_date' AND '$end_date'";
}

// Jalankan query
$laporan = $conn->query($query);

// Tampilkan data
if ($laporan->num_rows > 0) {
    while ($row = $laporan->fetch_assoc()) {
        echo "<tr>
            <td>{$row['timestamp_transaksi']}</td>
            <td>{$row['nama_pelanggan']}</td>
            <td>{$row['kode_barang']}</td>
            <td>{$row['nama_barang']}</td>
            <td>{$row['ukuran']}</td>
            <td>{$row['jumlah']}</td>
            <td>Rp " . number_format($row['harga_satuan'], 0, ',', '.') . "</td>
            <td>Rp " . number_format($row['harga_total'], 0, ',', '.') . "</td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='8'>Tidak ada data untuk periode ini.</td></tr>";
}
?>
