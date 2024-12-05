<?php
// Include koneksi database dan kelas Karyawan
include 'koneksi.php';
include 'Karyawan.php';

// Variabel untuk menyimpan alert jika ada
$alert = isset($_GET['alert']) ? $_GET['alert'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Karyawan</title>
    <!-- Tambahkan Bootstrap untuk styling -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Manajemen Karyawan</h2>

        <!-- Tampilkan alert jika ada -->
        <?php if (!empty($alert)) : ?>
            <script>
                Swal.fire(<?= $alert ?>);
            </script>
        <?php endif; ?>

        <!-- Form Tambah/Edit Karyawan -->
        <form action="proses_karyawan.php" method="POST">
            <h4 id="form-title">Tambah Karyawan</h4>
            <div class="mb-3">
                <label for="nama" class="form-label">Nama</label>
                <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan nama" required>
            </div>
            <div class="mb-3">
                <label for="nomor_telepon" class="form-label">Nomor Telepon</label>
                <input type="text" class="form-control" id="nomor_telepon" name="nomor_telepon" placeholder="Masukkan nomor telepon" required>
            </div>
            <div class="mb-3">
                <label for="kode_karyawan" class="form-label">Kode Karyawan</label>
                <input type="text" class="form-control" id="kode_karyawan" name="kode_karyawan" placeholder="Contoh: K123, PG456, P789" required>
            </div>
            <input type="hidden" name="id" id="id">
            <input type="hidden" name="action" id="action" value="add">
            <button type="submit" class="btn btn-primary w-100" id="submit-button">Tambah Karyawan</button>
        </form>

        <hr class="my-5">

        <!-- Tabel Daftar Karyawan -->
        <h4>Daftar Karyawan</h4>
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kode Karyawan</th>
                    <th>Nama</th>
                    <th>Nomor Telepon</th>
                    <th>Jabatan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Ambil data karyawan dari database
                $result = $conn->query("SELECT * FROM karyawan");
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                            <td>{$row['id_karyawan']}</td>
                            <td>{$row['kode_karyawan']}</td>
                            <td>{$row['nama']}</td>
                            <td>{$row['nomor_telepon']}</td>
                            <td>{$row['jabatan']}</td>
                            <td>
                                <button class='btn btn-warning btn-sm edit-button' 
                                    data-id='{$row['id_karyawan']}' 
                                    data-nama='{$row['nama']}' 
                                    data-nomor='{$row['nomor_telepon']}' 
                                    data-kode='{$row['kode_karyawan']}'>Edit</button>
                                <a href='proses_karyawan.php?action=delete&id={$row['id_karyawan']}' class='btn btn-danger btn-sm'>Hapus</a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center'>Belum ada data karyawan.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">


    <!-- JavaScript untuk Edit Data -->
    <script>
        document.querySelectorAll('.edit-button').forEach(button => {
            button.addEventListener('click', function () {
                document.getElementById('form-title').textContent = "Edit Karyawan";
                document.getElementById('submit-button').textContent = "Update Karyawan";
                document.getElementById('action').value = "edit";

                // Isi form dengan data yang akan di-edit
                document.getElementById('id').value = this.getAttribute('data-id');
                document.getElementById('nama').value = this.getAttribute('data-nama');
                document.getElementById('nomor_telepon').value = this.getAttribute('data-nomor');
                document.getElementById('kode_karyawan').value = this.getAttribute('data-kode');
            });
        });
    </script>

    <script>
        // Fungsi untuk membaca parameter URL
        function getUrlParameter(name) {
            name = name.replace(/[\[\]]/g, '\\$&');
            var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)');
            var results = regex.exec(window.location.href);
            if (!results) return null;
            if (!results[2]) return '';
            return decodeURIComponent(results[2].replace(/\+/g, ' '));
        }

        // Ambil parameter alert
        var alertMessage = getUrlParameter('alert');
        if (alertMessage) {
            // Format pesan sesuai SweetAlert
            const alertParts = alertMessage.split(", ");
            Swal.fire({
                title: alertParts[0].replace(/'/g, ""), // Judul alert
                text: alertParts[1].replace(/'/g, ""),  // Isi pesan
                icon: alertParts[2].replace(/'/g, "")   // Tipe alert
            });
        }
    </script>

    <script>
        // Hapus parameter alert dari URL
        if (alertMessage) {
            const url = new URL(window.location.href);
            url.searchParams.delete('alert');
            window.history.replaceState(null, null, url.toString());
        }
    </script>

</body>
</html>