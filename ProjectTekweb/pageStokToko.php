<?php
include 'koneksi.php';
include 'detailProduk.php';

$detailProduk = new DetailProduk($conn);

// Menangani pencarian kode barang
$search = isset($_POST['search']) ? $_POST['search'] : ''; // Ambil nilai pencarian

// Modifikasi query SQL untuk pencarian kode barang
$query = "SELECT dp.id_detprod, p.kode_barang, p.harga, u.ukuran, dp.stok_toko 
          FROM detail_produk dp
          JOIN produk p ON dp.id_barang = p.id_barang
          JOIN ukuran u ON dp.id_ukuran = u.id_ukuran
          WHERE dp.status_aktif = 1 AND p.status_aktif = 1 AND u.status_aktif = 1";

// Jika ada pencarian, filter berdasarkan kode barang
if (!empty($search)) {
    $query .= " AND p.kode_barang LIKE '%$search%'";
}

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Stok Toko</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Manajemen Stok Toko</h2>

    <!-- Form Pencarian -->
    <form method="POST" class="mb-4">
        <div class="input-group">
            <input type="text" class="form-control" name="search" placeholder="Cari berdasarkan kode barang" value="<?= htmlspecialchars($search) ?>">
            <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
        </div>
    </form>

    <!-- Notifikasi -->
    <?php if (isset($_GET['alert'])) : ?>
        <script>
            Swal.fire({
                icon: 'info',
                title: 'Info',
                text: '<?= $_GET['alert'] ?>'
            });
        </script>
    <?php endif; ?>

    <!-- Tabel Stok Barang -->
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>ID</th>
            <th>Kode Barang</th>
            <th>Ukuran</th>
            <th>Harga</th>
            <th>Stok Toko</th>
            <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($result->num_rows > 0) : ?>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <tr>
                    <td><?= $row['id_detprod'] ?></td>
                    <td><?= $row['kode_barang'] ?></td>
                    <td><?= $row['ukuran'] ?></td>
                    <td><?= $row['harga'] ?></td>
                    <td><?= $row['stok_toko'] ?></td>
                    <td>
                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addStockModal"
                                data-id-detprod="<?= $row['id_detprod'] ?>">Tambah Stok</button>
                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#subtractStockModal"
                                data-id-detprod="<?= $row['id_detprod'] ?>">Kurangi Stok</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else : ?>
            <tr>
                <td colspan="6" class="text-center">Tidak ada data stok barang.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Tambah Stok -->
<div class="modal fade" id="addStockModal" tabindex="-1" aria-labelledby="addStockModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Stok Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="proses_stokToko.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" id="id_detprod_add" name="id_detprod">
                    <div class="mb-3">
                        <label for="jumlah_add" class="form-label">Jumlah Stok</label>
                        <input type="number" class="form-control" id="jumlah_add" name="jumlah" min="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="action" value="add" class="btn btn-primary">Tambah Stok</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Kurangi Stok -->
<div class="modal fade" id="subtractStockModal" tabindex="-1" aria-labelledby="subtractStockModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kurangi Stok Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="proses_stokToko.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" id="id_detprod_sub" name="id_detprod">
                    <div class="mb-3">
                        <label for="jumlah_sub" class="form-label">Jumlah Stok</label>
                        <input type="number" class="form-control" id="jumlah_sub" name="jumlah" min="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="action" value="subtract" class="btn btn-danger">Kurangi Stok</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.querySelectorAll('[data-bs-target="#addStockModal"]').forEach(button => {
        button.addEventListener('click', () => {
            document.getElementById('id_detprod_add').value = button.getAttribute('data-id-detprod');
        });
    });
    document.querySelectorAll('[data-bs-target="#subtractStockModal"]').forEach(button => {
        button.addEventListener('click', () => {
            document.getElementById('id_detprod_sub').value = button.getAttribute('data-id-detprod');
        });
    });
</script>
</body>
</html>