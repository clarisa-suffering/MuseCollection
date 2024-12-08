<?php
class KaryawanRepository {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getKaryawanById($id_karyawan) {
        $result = $this->conn->query("SELECT id_karyawan, nama, gaji, periode_terakhir, kode_karyawan FROM karyawan WHERE id_karyawan = $id_karyawan");
        return $result->fetch_assoc();
    }

    public function updateGaji($id_karyawan, $new_gaji, $new_periode) {
        $this->conn->query("UPDATE karyawan SET gaji = $new_gaji, periode_terakhir = '{$new_periode->format('Y-m-d')}' WHERE id_karyawan = $id_karyawan");
    }
}