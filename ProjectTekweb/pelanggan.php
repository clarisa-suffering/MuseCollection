<?php
class Pelanggan {
    private $conn;
    
    public $id_pelanggan;
    public $nama;
    public $nomor_telepon;
    public $alamat;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Function to insert a new Pelanggan into the database
    public function insertPelanggan() {
        try {
            $stmt = $this->conn->prepare("INSERT INTO pelanggan (nama, nomor_telepon, alamat) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $this->nama, $this->nomor_telepon, $this->alamat);
            if ($stmt->execute()) {
                $this->id_pelanggan = $this->conn->insert_id;
                return $this->id_pelanggan;
            } else {
                return false;
            }

        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    // Function to create a new transaction
    public function createTransaksi($kategori_penjualan) {
        $transaksi = new Transaksi($this->conn);
        $transaksi->kategori_penjualan = $kategori_penjualan;
        return $transaksi;
    }
}
?>
