<?php
// Include koneksi database
include 'koneksi.php';

$alert = ''; // Variabel untuk menyimpan script alert

// Proses tambah, edit, dan hapus
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'add') {
        $nama = $_POST['nama'];
        $nomor_telepon = $_POST['nomor_telepon'];
        $start_date = date('Y-m-d H:i:s');
        $kode_karyawan = $_POST['kode_karyawan'];
    
        // Validasi kode karyawan dengan regex
        $jabatan = '';
        if (preg_match('/^(K\d+|PG\d+|P\d+)$/i', $kode_karyawan)) {
            if (strtoupper(substr($kode_karyawan, 0, 1)) === 'K') {
                $jabatan = 'Kasir';
            } elseif (strtoupper(substr($kode_karyawan, 0, 1)) === 'P') {
                if (strtoupper(substr($kode_karyawan, 1, 1)) === 'G') {
                    $jabatan = 'Penjaga Gudang';
                } else {
                    $jabatan = 'Pemilik';
                }
            } elseif (strtoupper(substr($kode_karyawan, 0, 2)) === 'PG') {
                $jabatan = 'Penjaga Gudang';
            }
        }
    
        // Jika kode karyawan tidak valid
        if (empty($jabatan)) {
            $alert = "<script>Swal.fire('Gagal!', 'Kode karyawan tidak valid. Harus diawali dengan P, PG, atau K dan diikuti angka.', 'error');</script>";
        } else {
            // Periksa apakah kode karyawan sudah ada
            $check_result = $conn->query("SELECT kode_karyawan FROM karyawan WHERE kode_karyawan = '$kode_karyawan'");
            if ($check_result->num_rows > 0) {
                $alert = "<script>Swal.fire('Gagal!', 'Kode karyawan \"$kode_karyawan\" sudah ada. Gunakan kode lain.', 'error');</script>";
            } else {
                // Cari ID terkecil yang tersedia
                $result = $conn->query("SELECT id_karyawan FROM karyawan ORDER BY id_karyawan ASC");
                $used_ids = [];
                while ($row = $result->fetch_assoc()) {
                    $used_ids[] = $row['id_karyawan'];
                }
    
                // Cari ID terkecil yang belum digunakan
                $new_id = 1;
                while (in_array($new_id, $used_ids)) {
                    $new_id++;
                }
    
                // Masukkan data ke database dengan ID yang telah ditemukan
                $stmt = $conn->prepare("INSERT INTO karyawan (id_karyawan, kode_karyawan, nama, nomor_telepon, start_date, jabatan) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("isssss", $new_id, $kode_karyawan, $nama, $nomor_telepon, $start_date, $jabatan);
    
                if ($stmt->execute()) {
                    $alert = "<script>Swal.fire('Berhasil!', 'Data berhasil ditambahkan.', 'success');</script>";
                } else {
                    $alert = "<script>Swal.fire('Gagal!', 'Data gagal ditambahkan.', 'error');</script>";
                }
                $stmt->close();
            }
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'edit') {
        $id = $_POST['id'];
        $kode_karyawan = $_POST['kode_karyawan'];
        $nama = $_POST['nama'];
        $nomor_telepon = $_POST['nomor_telepon'];

        // Validasi kode karyawan dengan regex
        $jabatan = '';
        if (preg_match('/^(K\d+|PG\d+|P\d+)$/i', $kode_karyawan)) {
            if (strtoupper(substr($kode_karyawan, 0, 1)) === 'K') {
                $jabatan = 'Kasir';
            } elseif (strtoupper(substr($kode_karyawan, 0, 1)) === 'P') {
                if (strtoupper(substr($kode_karyawan, 1, 1)) === 'G') {
                    $jabatan = 'Penjaga Gudang';
                } else {
                    $jabatan = 'Pemilik';
                }
            } elseif (strtoupper(substr($kode_karyawan, 0, 2)) === 'PG') {
                $jabatan = 'Penjaga Gudang';
            }
        }

        // Jika kode karyawan tidak valid
        if (empty($jabatan)) {
            $alert = "<script>Swal.fire('Gagal!', 'Kode karyawan tidak valid. Harus diawali dengan P, PG, atau K dan diikuti angka.', 'error');</script>";
        } else {
            // Periksa apakah kode karyawan sudah ada di database untuk ID lain
            $check_result = $conn->query("SELECT kode_karyawan FROM karyawan WHERE kode_karyawan = '$kode_karyawan' AND id_karyawan != $id");
            if ($check_result->num_rows > 0) {
                $alert = "<script>Swal.fire('Gagal!', 'Kode karyawan \"$kode_karyawan\" sudah digunakan oleh karyawan lain. Gunakan kode lain.', 'error');</script>";
            } else {
                // Lakukan update pada database
                $stmt = $conn->prepare("UPDATE karyawan SET kode_karyawan = ?, nama = ?, nomor_telepon = ? WHERE id_karyawan = ?");
                $stmt->bind_param("sssi", $kode_karyawan, $nama, $nomor_telepon, $id);

                if ($stmt->execute()) {
                    $alert = "<script>Swal.fire('Berhasil!', 'Data berhasil diperbarui.', 'success');</script>";
                } else {
                    $alert = "<script>Swal.fire('Gagal!', 'Data gagal diperbarui.', 'error');</script>";
                }
                $stmt->close();
            }
        }
    } // Proses untuk menghapus data dan memperbarui ID
    elseif (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id = $_POST['id'];
    
        // Mulai transaksi untuk memastikan semua query terjadi bersama-sama
        $conn->begin_transaction();
    
        try {
            // Langkah 1: Hapus data karyawan yang terpilih
            $stmt = $conn->prepare("DELETE FROM karyawan WHERE id_karyawan = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
    
            // Langkah 2: Update ID karyawan yang lebih besar dari ID yang dihapus
            $updateStmt = $conn->prepare("UPDATE karyawan SET id_karyawan = id_karyawan - 1 WHERE id_karyawan > ?");
            $updateStmt->bind_param("i", $id);
            $updateStmt->execute();
            $updateStmt->close();
    
            // Commit transaksi
            $conn->commit();
    
            $alert = "<script>Swal.fire('Berhasil!', 'Data berhasil dihapus dan ID diperbarui.', 'success');</script>";
        } catch (Exception $e) {
            // Jika ada error, rollback transaksi
            $conn->rollback();
            $alert = "<script>Swal.fire('Gagal!', 'Terjadi kesalahan saat menghapus data.', 'error');</script>";
        }
    }    
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Karyawan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        tr:hover {
            background-color: #f1f1f1;
            cursor: pointer;
        }

        tr.selected {
            background-color: #a5d6ff !important;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h2>Data Karyawan</h2>
        <?= $alert; ?>
        <table class="table table-striped" id="karyawanTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Nomor Telepon</th>
                    <th>Jabatan</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT * FROM karyawan");
                while ($row = $result->fetch_assoc()) {
                    echo "<tr data-id='{$row['id_karyawan']}' data-kode='{$row['kode_karyawan']}' data-nama='{$row['nama']}' data-nomor='{$row['nomor_telepon']}' data-jabatan='{$row['jabatan']}'>
                        <td>{$row['id_karyawan']}</td>
                        <td>{$row['kode_karyawan']}</td>
                        <td>{$row['nama']}</td>
                        <td>{$row['nomor_telepon']}</td>
                        <td>{$row['jabatan']}</td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>

        <div class="d-flex justify-content-end">
            <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#addModal">Add</button>
            <button class="btn btn-warning me-2" id="editButton">Edit</button>
            <button class="btn btn-danger me-2" id="deleteButton">Delete</button>
            <a href="karyawan_list.php" class="btn btn-secondary me-2">Back</a>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addModalLabel">Tambah Karyawan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="kode_karyawan" class="form-label">Kode Karyawan</label>
                            <input type="text" name="kode_karyawan" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="nomor_telepon" class="form-label">Nomor Telepon</label>
                            <input type="text" name="nomor_telepon" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="editId">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Karyawan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_kode_karyawan" class="form-label">Kode Karyawan</label>
                            <input type="text" name="kode_karyawan" id="edit_kode_karyawan" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_nama" class="form-label">Nama</label>
                            <input type="text" name="nama" id="edit_nama" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_nomor_telepon" class="form-label">Nomor Telepon</label>
                            <input type="text" name="nomor_telepon" id="edit_nomor_telepon" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Handle Edit and Delete button functionality
        document.getElementById('editButton').addEventListener('click', function () {
            const selectedRow = document.querySelector('tr.selected');
            if (!selectedRow) {
                Swal.fire('Peringatan', 'Silakan pilih data yang akan diubah!', 'warning');
                return;
            }

            const id = selectedRow.getAttribute('data-id');
            const kode = selectedRow.getAttribute('data-kode');
            const nama = selectedRow.getAttribute('data-nama');
            const nomor = selectedRow.getAttribute('data-nomor');
            const jabatan = selectedRow.getAttribute('data-jabatan');

            document.getElementById('editId').value = id;
            document.getElementById('edit_kode_karyawan').value = kode;
            document.getElementById('edit_nama').value = nama;
            document.getElementById('edit_nomor_telepon').value = nomor;

            new bootstrap.Modal(document.getElementById('editModal')).show();
        });

        document.getElementById('deleteButton').addEventListener('click', function () {
            const selectedRow = document.querySelector('tr.selected');
            if (!selectedRow) {
                Swal.fire('Peringatan', 'Silakan pilih data yang akan dihapus!', 'warning');
                return;
            }

            const id = selectedRow.getAttribute('data-id');
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin menghapus data ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal'
            }).then(result => {
                if (result.isConfirmed) {
                    // Mengirim request delete
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '';
                    form.innerHTML = `<input type="hidden" name="action" value="delete">
                                      <input type="hidden" name="id" value="${id}">`;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });

        // Menangani baris yang dipilih untuk edit atau delete
        document.querySelectorAll('tr').forEach(row => {
            row.addEventListener('click', function () {
                this.classList.toggle('selected');
            });
        });
    </script>
</body>
</html>