<?php
class RiwayatHarga {
    private $conn;

    public $id_rharga;
    public $perubahan_harga;
    public $tanggal;
    public $id_barang;

    // Konstruktor untuk inisialisasi koneksi database
    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Fungsi untuk menambahkan riwayat perubahan harga
    public function tambah_rharga($id_barang, $perubahan_harga) {
        // Validasi input
        if (empty($id_barang) || !is_numeric($perubahan_harga) || $perubahan_harga <= 0) {
            throw new Exception("Data tidak valid.");
        }

        // Ambil waktu saat ini
        $tanggal = date("Y-m-d H:i:s");
        
        // Menyiapkan query untuk memasukkan data
        $stmt = $this->conn->prepare("INSERT INTO riwayat_harga (id_barang, tanggal, perubahan_harga) VALUES (?, ?, ?)");
        
        // Cek jika prepare query berhasil
        if ($stmt === false) {
            throw new Exception("Gagal menyiapkan query.");
        }
        
        $stmt->bind_param("isi", $id_barang, $tanggal, $perubahan_harga);
        
        // Eksekusi query dan cek hasilnya
        if (!$stmt->execute()) {
            throw new Exception("Gagal menyimpan riwayat harga: " . $stmt->error);
        }
        
        $stmt->close();
    }

    // Fungsi untuk mendapatkan riwayat perubahan harga berdasarkan id_barang
    public function getRiwayat($id_barang) {
        // Validasi id_barang
        if (empty($id_barang)) {
            throw new Exception("ID barang tidak boleh kosong.");
        }

        // Query untuk mengambil riwayat harga
        $stmt = $this->conn->prepare("SELECT * FROM riwayat_harga WHERE id_barang = ? ORDER BY tanggal DESC");
        
        // Cek jika prepare query berhasil
        if ($stmt === false) {
            throw new Exception("Gagal menyiapkan query.");
        }

        $stmt->bind_param("i", $id_barang);
        $stmt->execute();

        // Ambil hasil query
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $stmt->close();
            return [];  // Jika tidak ada riwayat harga
        }

        $data = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $data;  // Kembalikan data riwayat harga dalam bentuk array
    }
}
?>
