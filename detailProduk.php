<?php
class DetailProduk {
    private $conn;

    public $id_detprod;
    public $stok_toko;
    public $stok_gudang;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Cek stok toko
    public function cekStokToko($kode_barang, $ukuran) {
        $stmt = $this->conn->prepare("SELECT stok_toko FROM detail_produk dp 
                                      JOIN produk p ON dp.id_barang = p.id_barang 
                                      JOIN ukuran u ON dp.id_ukuran = u.id_ukuran 
                                      WHERE p.kode_barang = ? AND u.ukuran = ? and dp.status_aktif=1");
        $stmt->bind_param("ss", $kode_barang, $ukuran);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if ($result) {
            return $result['stok_toko'];
        } else {
            return null;
        }
    }

    // Cek stok gudang
    public function cekStokGudang($kode_barang, $ukuran) {
        $stmt = $this->conn->prepare("SELECT stok_gudang FROM detail_produk dp 
                                      JOIN produk p ON dp.id_barang = p.id_barang 
                                      JOIN ukuran u ON dp.id_ukuran = u.id_ukuran 
                                      WHERE p.kode_barang = ? AND u.ukuran = ?");
        $stmt->bind_param("ss", $kode_barang, $ukuran);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if ($result) {
            return $result['stok_gudang'];
        } else {
            return null;
        }
    }

    // Mengurangi stok toko untuk transaksi
    public function mengurangiStokToko($id_detprod, $jumlah) {
        $stmt = $this->conn->prepare("SELECT stok_toko FROM detail_produk WHERE id_detprod = ?");
        $stmt->bind_param("i", $id_detprod);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($result) {
            $current_stock = $result['stok_toko'];
            $new_stock = $current_stock - $jumlah;

            // update stok
            if ($new_stock >= 0) {
                $stmt = $this->conn->prepare("UPDATE detail_produk SET stok_toko = ? WHERE id_detprod = ?");
                $stmt->bind_param("ii", $new_stock, $id_detprod);
                $stmt->execute();
                $stmt->close();

                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    // Mengurangi stok gudang utk transaksi
    public function mengurangiStokGudang($id_detprod, $jumlah) {
        $stmt = $this->conn->prepare("SELECT stok_gudang FROM detail_produk WHERE id_detprod = ?");
        $stmt->bind_param("i", $id_detprod);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($result) {
            $current_stock = $result['stok_gudang'];
            $new_stock = $current_stock - $jumlah;

            if ($new_stock >= 0) {
                $stmt = $this->conn->prepare("UPDATE detail_produk SET stok_gudang = ? WHERE id_detprod = ?");
                $stmt->bind_param("ii", $new_stock, $id_detprod);
                $stmt->execute();
                $stmt->close();

                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    // get id
    public function getId($kode_barang, $ukuran) {
        $stmt = $this->conn->prepare("SELECT dp.id_detprod FROM detail_produk dp 
                                      JOIN produk p ON dp.id_barang = p.id_barang 
                                      JOIN ukuran u ON dp.id_ukuran = u.id_ukuran 
                                      WHERE p.kode_barang = ? AND u.ukuran = ?");
        $stmt->bind_param("ss", $kode_barang, $ukuran);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($result) {
            return $result['id_detprod'];
        } else {
            return null;
        }
    }

    // Menambah stok toko
    public function menambahStokToko($id_detprod, $jumlah) {
        $stmt = $this->conn->prepare("SELECT stok_toko FROM detail_produk WHERE id_detprod = ?");
        $stmt->bind_param("i", $id_detprod);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($result) {
            $current_stock = $result['stok_toko'];
            $new_stock = $current_stock + $jumlah;

            $stmt = $this->conn->prepare("UPDATE detail_produk SET stok_toko = ? WHERE id_detprod = ?");
            $stmt->bind_param("ii", $new_stock, $id_detprod);
            $stmt->execute();
            $stmt->close();

            return true;
        }
        return false;
    }

    // Menambah stok gudang
    public function menambahStokGudang($id_detprod, $jumlah) {
        $stmt = $this->conn->prepare("SELECT stok_gudang FROM detail_produk WHERE id_detprod = ?");
        $stmt->bind_param("i", $id_detprod);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($result) {
            $current_stock = $result['stok_gudang'];
            $new_stock = $current_stock + $jumlah;

            $stmt = $this->conn->prepare("UPDATE detail_produk SET stok_gudang = ? WHERE id_detprod = ?");
            $stmt->bind_param("ii", $new_stock, $id_detprod);
            $stmt->execute();
            $stmt->close();

            return true;
        }
        return false;
    }

}

?>