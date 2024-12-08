<?php
class KaryawanService {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Fungsi sebelumnya dari KaryawanRepository
    public function getKaryawanById($id_karyawan) {
        $result = $this->conn->query("SELECT * FROM karyawan WHERE id_karyawan = $id_karyawan");
        return $result->fetch_assoc();
    }

    public function updateGaji($id_karyawan, $new_gaji, $new_periode) {
        $this->conn->query("UPDATE karyawan SET gaji = $new_gaji, periode_terakhir = '{$new_periode->format('Y-m-d')}' WHERE id_karyawan = $id_karyawan");
    }

    // Fungsi sebelumnya dari AbsensiRepository
    private function getFirstAttendanceDate($id_karyawan) {
        $result = $this->conn->query("SELECT MIN(jam) AS tanggal_absensi_pertama FROM absensi WHERE id_karyawan = $id_karyawan");
        $data = $result->fetch_assoc();
        return $data['tanggal_absensi_pertama'];
    }

    private function getAttendanceCount($id_karyawan, $startDate, $endDate) {
        $query = "SELECT COUNT(*) AS hadir FROM absensi 
                  WHERE id_karyawan = $id_karyawan 
                  AND DATE(jam) BETWEEN '{$startDate->format('Y-m-d')}' AND '{$endDate->format('Y-m-d')}'";
        $result = $this->conn->query($query);
        $data = $result->fetch_assoc();
        return $data['hadir'];
    }

    // Fungsi perhitungan gaji (dari GajiCalculator)
    public function detailGaji($id_karyawan) {
        $row = $this->getKaryawanById($id_karyawan);
        if (!$row) return null;

        $start_date = new DateTime($this->getFirstAttendanceDate($id_karyawan));
        $current_date = new DateTime();
        $years_of_work = $start_date->diff($current_date)->y;

        $base_salary = 3000000;
        $increment = ($years_of_work > 1) ? ($years_of_work - 1) * 2000000 : 0;
        $calculated_salary = $base_salary + $increment;

        $start_period = $row['periode_terakhir'] ? new DateTime($row['periode_terakhir']) : $start_date;
        $attendance_count = $this->getAttendanceCount($id_karyawan, $start_period, $current_date);

        $work_days = 312;
        $bonus = ($attendance_count >= $work_days) ? 500000 : 0;
        $calculated_salary += $bonus;

        return [
            'nama' => $row['nama'],
            'base_salary' => $base_salary,
            'increment' => $increment,
            'bonus' => $bonus,
            'total' => $calculated_salary,
            'years_of_work' => $years_of_work
        ];
    }

    public function hitungGaji($id_karyawan) {
        $detail = $this->detailGaji($id_karyawan);
        if ($detail) {
            $current_date = new DateTime();
            $this->updateGaji($id_karyawan, $detail['total'], $current_date);
        }
        return $detail;
    }
}