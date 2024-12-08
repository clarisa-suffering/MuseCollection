<?php
class GajiCalculator {
    private $karyawanRepo;
    private $absensiRepo;

    public function __construct($karyawanRepo, $absensiRepo) {
        $this->karyawanRepo = $karyawanRepo;
        $this->absensiRepo = $absensiRepo;
    }

    public function detailGaji($id_karyawan) {
        $row = $this->karyawanRepo->getKaryawanById($id_karyawan);
        if (!$row) return null;

        $start_date = new DateTime($this->absensiRepo->getFirstAttendanceDate($id_karyawan));
        $current_date = new DateTime();
        $years_of_work = $start_date->diff($current_date)->y;

        $base_salary = 3000000;
        $increment = ($years_of_work > 1) ? ($years_of_work - 1) * 2000000 : 0;
        $calculated_salary = $base_salary + $increment;

        $start_period = $row['periode_terakhir'] ? new DateTime($row['periode_terakhir']) : $start_date;
        $attendance_count = $this->absensiRepo->getAttendanceCount($id_karyawan, $start_period, $current_date);
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
            $this->karyawanRepo->updateGaji($id_karyawan, $detail['total'], $current_date);
        }
        return $detail;
    }
}