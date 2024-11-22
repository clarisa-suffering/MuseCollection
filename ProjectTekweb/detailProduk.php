<?php
class DetailProduk {
    private $conn;

    public $id_detprod;
    public $stok_toko;
    public $stok_gudang;

    // Constructor to set up the database connection
    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Function to get stock levels based on product ID and size ID
    public function checkStockToko($kode_barang, $ukuran) {
        $stmt = $this->conn->prepare("SELECT stok_toko FROM detail_produk dp 
                                      JOIN produk p ON dp.id_barang = p.id_barang 
                                      JOIN ukuran u ON dp.id_ukuran = u.id_ukuran 
                                      WHERE p.kode_barang = ? AND u.ukuran = ?");
        $stmt->bind_param("ss", $kode_barang, $ukuran);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if ($result) {
            return $result['stok_toko'];  // Return
        } else {
            return null;  // Return null if no matching record is found
        }
    }

    // Function to insert a new detail record into the detail_produk table
    public function insertDetailProduk($id_barang, $id_ukuran, $stok_toko, $stok_gudang) {
        $stmt = $this->conn->prepare("INSERT INTO detail_produk (id_barang, id_ukuran, stok_toko, stok_gudang) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiii", $id_barang, $id_ukuran, $stok_toko, $stok_gudang);
        $stmt->execute();
        $stmt->close();
    }

    // Function to update stock after adding to a transaction
    public function updateStock($id_detprod, $stok_toko) {
        $stmt = $this->conn->prepare("UPDATE detail_produk SET stok_toko = ? WHERE id_detprod = ?");
        $stmt->bind_param("ii", $stok_toko, $id_detprod);
        $stmt->execute();
        $stmt->close();
    }

    // Function to reduce stock after a transaction is confirmed
    public function reduceStock($id_detprod, $jumlah) {
        // Get the current stock level
        $stmt = $this->conn->prepare("SELECT stok_toko FROM detail_produk WHERE id_detprod = ?");
        $stmt->bind_param("i", $id_detprod);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($result) {
            $current_stock = $result['stok_toko'];
            $new_stock = $current_stock - $jumlah;

            // Update the stock only if the result is not negative
            if ($new_stock >= 0) {
                $this->updateStock($id_detprod, $new_stock);
                return true;
            } else {
                return false; // Stock cannot be negative
            }
        }
        return false; // Return false if product not found
    }

    // get id from kode and ukuran
    public function getIdDetprod($kode_barang, $ukuran) {
        $stmt = $this->conn->prepare("SELECT dp.id_detprod FROM detail_produk dp 
                                      JOIN produk p ON dp.id_barang = p.id_barang 
                                      JOIN ukuran u ON dp.id_ukuran = u.id_ukuran 
                                      WHERE p.kode_barang = ? AND u.ukuran = ?");
        $stmt->bind_param("ss", $kode_barang, $ukuran);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($result) {
            return $result['id_detprod'];  // Return the id_detprod
        } else {
            return null;  // Return null if no matching record is found
        }
    }

}

?>
