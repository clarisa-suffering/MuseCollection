<?php
class DetailTransaksi {
    private $conn;

    public $id_detail;
    public $id_detprod;
    public $jumlah;
    public $subtotal;
    public $id_transaksi;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // mencatat detail transaksi
    public function mencatatDetailTransaksi() {
        $sql = "INSERT INTO detail_transaksi (id_detprod, jumlah, subtotal, id_transaksi) 
                VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iiii", $this->id_detprod, $this->jumlah, $this->subtotal, $this->id_transaksi);
        return $stmt->execute();
    }
}
?>
