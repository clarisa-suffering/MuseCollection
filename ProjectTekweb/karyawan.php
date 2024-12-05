<?php
class Karyawan {
    private $conn;

    // Atribut Karyawan
    public $id_karyawan;
    public $kode_karyawan;
    public $nama;
    public $nomor_telepon;
    public $start_date;
    public $jabatan;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function validasiKodeKaryawan() {
        if (!preg_match('/^(K\d+|P\d+|PG\d+)$/i', $this->kode_karyawan)) {
            echo "Kode Karyawan tidak valid: " . $this->kode_karyawan; // Debugging
            return "Kode karyawan harus diawali dengan 'K/P/PG'";
        }
        return true;
    }            

    public function validasiNomorTelepon() {
        // Periksa apakah nomor telepon diawali dengan '08'
        if (!preg_match('/^08/', $this->nomor_telepon)) {
            return "Nomor telepon harus dimulai dengan '08'.";
        }
    
        // Periksa apakah nomor telepon memiliki panjang 8-12 karakter dan hanya angka
        if (!preg_match('/^08\d{6,10}$/', $this->nomor_telepon)) {
            return "Nomor telepon harus berupa angka dengan panjang 8-12 karakter.";
        }
    
        return true; // Validasi berhasil
    }    

    // Validasi jabatan berdasarkan kode karyawan
    public function setJabatanOtomatis() {
        if (strtoupper(substr($this->kode_karyawan, 0, 2)) === 'PG') {
            $this->jabatan = 'Penjaga Gudang';
        } elseif (strtoupper(substr($this->kode_karyawan, 0, 1)) === 'K') {
            $this->jabatan = 'Karyawan';
        } elseif (strtoupper(substr($this->kode_karyawan, 0, 1)) === 'P') {
            $this->jabatan = 'Pemilik';
        } else {
            $this->jabatan = null; // Jika tidak sesuai format
        }
    }

    // Periksa apakah kode karyawan sudah ada (digunakan untuk tambah)
    public function kodeKaryawanAda() {
        $stmt = $this->conn->prepare("SELECT kode_karyawan FROM karyawan WHERE kode_karyawan = ?");
        $stmt->bind_param("s", $this->kode_karyawan);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    // Periksa apakah kode karyawan sudah ada pada karyawan lain (digunakan untuk edit)
    public function kodeKaryawanAdaSelain($id) {
        $stmt = $this->conn->prepare("SELECT kode_karyawan FROM karyawan WHERE kode_karyawan = ? AND id_karyawan != ?");
        $stmt->bind_param("si", $this->kode_karyawan, $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    // Cari ID terkecil yang belum digunakan
    public function cariIdBaru() {
        $result = $this->conn->query("SELECT id_karyawan FROM karyawan ORDER BY id_karyawan ASC");
        $used_ids = [];
        while ($row = $result->fetch_assoc()) {
            $used_ids[] = $row['id_karyawan'];
        }

        $new_id = 1;
        while (in_array($new_id, $used_ids)) {
            $new_id++;
        }
        $this->id_karyawan = $new_id;
    }

    // Tambah data karyawan ke database
    public function tambahKaryawan() {
        $stmt = $this->conn->prepare("INSERT INTO karyawan (id_karyawan, kode_karyawan, nama, nomor_telepon, start_date, jabatan) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $this->id_karyawan, $this->kode_karyawan, $this->nama, $this->nomor_telepon, $this->start_date, $this->jabatan);
        return $stmt->execute();
    }

    // Edit data karyawan
    public function editKaryawan($id) {
        $stmt = $this->conn->prepare("UPDATE karyawan SET kode_karyawan = ?, nama = ?, nomor_telepon = ?, jabatan = ? WHERE id_karyawan = ?");
        $stmt->bind_param("ssssi", $this->kode_karyawan, $this->nama, $this->nomor_telepon, $this->jabatan, $id);
        return $stmt->execute();
    }
}
?>