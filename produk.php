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

    // Mengubah harga produk
    public function perubahanHarga($harga_baru) {
        // Memastikan harga baru lebih besar dari 0
        if ($harga_baru > 0) {
            $stmt = $this->conn->prepare("UPDATE produk SET harga = ? WHERE kode_barang = ?");
            $stmt->bind_param("is", $harga_baru, $this->kode_barang);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                // Ambil `id_barang` untuk keperluan riwayat
                $stmt = $this->conn->prepare("SELECT id_barang FROM produk WHERE kode_barang = ?");
                $stmt->bind_param("s", $this->kode_barang);
                $stmt->execute();
                $result = $stmt->get_result();
                $data = $result->fetch_assoc();
                $stmt->close();
    
                if ($data) {
                    $id_barang = $data['id_barang'];
    
                    // Simpan ke tabel riwayat_harga
                    $riwayat = new RiwayatHarga($this->conn);
                    try {
                        $riwayat->tambah_rharga($id_barang, $harga_baru);
                    } catch (Exception $e) {
                        error_log("Gagal menyimpan riwayat harga: " . $e->getMessage());
                    }
                }
    
                return true; // Perubahan harga berhasil
            } else {
                return false; // Tidak ada perubahan pada database
            }
        } else {
            return false; // Harga baru tidak valid
        }
       
    }
}
?>

