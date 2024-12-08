<?php
class AbsensiRepository {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getFirstAttendanceDate($id_karyawan) {
        $result = $this->conn->query("SELECT MIN(jam) AS tanggal_absensi_pertama FROM absensi WHERE id_karyawan = $id_karyawan");
        $data = $result->fetch_assoc();
        return $data['tanggal_absensi_pertama'];
    }

    public function getAttendanceCount($id_karyawan, $start_period, $end_period) {
        $query = "SELECT COUNT(*) AS hadir FROM absensi 
                  WHERE id_karyawan = $id_karyawan 
                  AND DATE(jam) BETWEEN '{$start_period->format('Y-m-d')}' AND '{$end_period->format('Y-m-d')}'";
        $result = $this->conn->query($query);
        $data = $result->fetch_assoc();
        return $data['hadir'];
    }
}