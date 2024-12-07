<?php
class Transaksi {
    private $conn;

    public $id_transaksi;
    public $kategori_penjualan;
    public $harga_total;
    public $status_transaksi;
    public $tanggal_transaksi;

    // Detail transaksi
    public $details = [];

    public function __construct($conn) {
        $this->conn = $conn;
    }

    
    // Mencatat transaksi
    public function mencatatTransaksi() {
        $sql = "INSERT INTO transaksi (id_pelanggan, kategori_penjualan, harga_total) 
                VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("isd", $this->id_pelanggan, $this->kategori_penjualan, $this->harga_total);
        if ($stmt->execute()) {
            $this->id_transaksi = $this->conn->insert_id;  // Get inserted ID

            // Catat detail transaksi
            foreach ($this->details as $detail) {
                $detail->id_transaksi = $this->id_transaksi; // Set id transaksi mjd id trans detail
                if (!$detail->mencatatDetailTransaksi()) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }
}


?>