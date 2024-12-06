<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'koneksi.php';
include 'Karyawan.php';

$alert = ''; // Variabel untuk menyimpan alert

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $karyawan = new Karyawan($conn);

    $karyawan->nama = $_POST['nama'];
    $karyawan->nomor_telepon = $_POST['nomor_telepon'];
    $karyawan->kode_karyawan = $_POST['kode_karyawan'];

    if ($_POST['action'] === 'add') {
        $karyawan->start_date = date('Y-m-d H:i:s');
        $karyawan->setJabatanOtomatis();

        $validasiKode = $karyawan->validasiKodeKaryawan();
        if ($validasiKode !== true) {
            $alert = "'Gagal!', '$validasiKode', 'error'";
        } elseif ($karyawan->kodeKaryawanAda()) {
            $alert = "'Gagal!', 'Kode karyawan sudah ada.', 'error'";
        } else {
            $validasiTelepon = $karyawan->validasiNomorTelepon();
            if ($validasiTelepon !== true) {
                $alert = "'Gagal!', '$validasiTelepon', 'error'";
            } else {
                $karyawan->cariIdBaru();
                if ($karyawan->tambahKaryawan()) {
                    $alert = "'Berhasil!', 'Data berhasil ditambahkan.', 'success'";
                } else {
                    $alert = "'Gagal!', 'Terjadi kesalahan saat menyimpan data.', 'error'";
                }
            }
        }
    } elseif ($_POST['action'] === 'edit') {
        $id = $_POST['id'];
        $karyawan->setJabatanOtomatis();

        $validasiKode = $karyawan->validasiKodeKaryawan();
        if ($validasiKode !== true) {
            $alert = "'Gagal!', '$validasiKode', 'error'";
        } elseif ($karyawan->kodeKaryawanAdaSelain($id)) {
            $alert = "'Gagal!', 'Kode karyawan sudah digunakan oleh karyawan lain.', 'error'";
        } else {
            $validasiTelepon = $karyawan->validasiNomorTelepon();
            if ($validasiTelepon !== true) {
                $alert = "'Gagal!', '$validasiTelepon', 'error'";
            } else {
                if ($karyawan->editKaryawan($id)) {
                    $alert = "'Berhasil!', 'Data berhasil diperbarui.', 'success'";
                } else {
                    $alert = "'Gagal!', 'Terjadi kesalahan saat memperbarui data.', 'error'";
                }
            }
        }
    }
} elseif (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];

    // Validasi ID
    if (!is_numeric($id)) {
        $alert = "'Gagal!', 'ID karyawan tidak valid.', 'error'";
    } else {
        // Hapus karyawan berdasarkan ID
        $stmt = $conn->prepare("DELETE FROM karyawan WHERE id_karyawan = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $alert = "'Berhasil!', 'Data karyawan berhasil dihapus.', 'success'";
        } else {
            $alert = "'Gagal!', 'Terjadi kesalahan saat menghapus data.', 'error'";
        }
        $stmt->close();
    }
}

// Redirect kembali ke halaman utama
header("Location: pageKaryawan.php?alert=" . urlencode($alert));
exit;
?>