<?php
class Transaksi {
    private $conn;

    public $id_transaksi;
    public $kategori_penjualan;
    public $harga_total;
    public $status_transaksi;
    public $tanggal_transaksi;

    // To hold the details before confirmation
    public $details = [];

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Function to add detail transaksi to the transaction
    public function addDetailTransaksi($id_detprod, $jumlah, $subtotal) {
        // Ensure a new DetailTransaksi object is created
        $detail = new DetailTransaksi($this->conn);
        $detail->id_detprod = $id_detprod;
        $detail->jumlah = $jumlah;
        $detail->subtotal = $subtotal;

        // Add the detail to the transaction's details array
        $this->details[] = $detail;
    }

    // Function to insert the transaction into the database
    public function insertTransaksi() {
        // Insert the main transaction
        $sql = "INSERT INTO transaksi (id_pelanggan, kategori_penjualan, harga_total) 
                VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("isd", $this->id_pelanggan, $this->kategori_penjualan, $this->harga_total);
        if ($stmt->execute()) {
            $this->id_transaksi = $this->conn->insert_id;  // Get the inserted ID

            // Insert all the details
            foreach ($this->details as $detail) {
                $detail->id_transaksi = $this->id_transaksi; // Set the transaction ID for each detail
                if (!$detail->insertDetailTransaksi()) {
                    return false; // If any insert fails, return false
                }
            }
            return true;
        }
        return false;
    }
}


?>