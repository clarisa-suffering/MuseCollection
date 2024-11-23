<?php
class Produk {
    private $conn;
    
    public $id_barang;
    public $kode_barang;
    public $harga;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // get harga
    public function getHargaBarang($kode_barang) {
        $stmt = $this->conn->prepare("SELECT harga FROM produk p WHERE p.kode_barang=? and p.status_aktif=1");
        $stmt->bind_param("s", $kode_barang);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $result ? $result['harga'] : null; 
    }
}
?>

