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
        date_default_timezone_set('Asia/Jakarta');
    }

    // Fungsi tambah riwayat perubahan harga
    public function tambah_rharga($id_barang, $perubahan_harga) {
        // Validasi input
        if (empty($id_barang) || !is_numeric($perubahan_harga) || $perubahan_harga <= 0) {
            throw new Exception("Data tidak valid.");
        }
        // Ambil waktu saat ini dengan format timestamp
        $tanggal = date("Y-m-d H:i:s");

        // Menyiapkan Query Insert untuk memasukkan data
        $stmt = $this->conn->prepare("INSERT INTO riwayat_harga (id_barang, tanggal, perubahan_harga) VALUES (?, ?, ?)");
        // Cek jika prepare query berhasil
        if ($stmt === false) {
            throw new Exception("Gagal menyiapkan query.");
        }
        
        // Eksekusi Query sesuai dengan parameter
        $stmt->bind_param("isi", $id_barang, $tanggal, $perubahan_harga);
        // Cek hasil
        if (!$stmt->execute()) {
            throw new Exception("Gagal menyimpan riwayat harga: " . $stmt->error);
        }
        
        $stmt->close();
    }

    // Fungsi get riwayat perubahan harga berdasarkan id_barang
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

        // Eksekusi query
        $stmt->bind_param("i", $id_barang);
        $stmt->execute();

        // Ambil hasil query
        $result = $stmt->get_result();
        // Jika tidak ada riwayat harga, return kosong
        if ($result->num_rows === 0) {
            $stmt->close();
            return [];
        }

        // Jika ada data, dikembalikan dalam bentuk array
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $data; 
    }
}
?>
