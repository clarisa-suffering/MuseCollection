<?php
class Produk {
    private $conn;
    
    public $id_barang;
    public $kode_barang;
    public $harga;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Function to get a price by its kode_barang
    public function getPrice($kode_barang) {
        $stmt = $this->conn->prepare("SELECT harga FROM produk p WHERE p.kode_barang=?");
        $stmt->bind_param("s", $kode_barang);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $result ? $result['harga'] : null; 
    }

    // Function to get a product by its kode_barang
    public function getProdukByKode($kode_barang) {
        $sql = "SELECT * FROM produk WHERE kode_barang = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $kode_barang);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Function to get all products
    public function getAllProduk() {
        $sql = "SELECT * FROM produk";
        $result = $this->conn->query($sql);
        return $result;
    }
}
?>

