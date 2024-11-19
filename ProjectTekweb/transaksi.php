<?php
class Transaksi {
    private $conn;

    public $id_transaksi;
    public $kategori_penjualan;
    public $harga_total;
    public $status_transaksi;
    public $tanggal_transaksi;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Function to insert transaksi
    public function insertTransaksi() {
        $sql = "INSERT INTO transaksi (kategori_penjualan, harga_total, status_transaksi, tanggal_transaksi) 
                VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sdss", $this->kategori_penjualan, $this->harga_total, $this->status_transaksi, $this->tanggal_transaksi);
        return $stmt->execute();
    }
}
?>