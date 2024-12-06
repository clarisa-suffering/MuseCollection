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

    // Mencatat pelanggan
    public function mencatatPelanggan() {
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
    
}
?>
