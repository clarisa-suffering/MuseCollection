
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Coba Project</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>


    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.0/mdb.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" rel="stylesheet">
</head>

<style>
        table th, table td {
        padding: 10px; /* Memberikan ruang di dalam kolom */
        text-align: left; /* Mengatur teks ke kiri */
    }

    table td {
        vertical-align: middle; /* Menjaga agar teks tidak terlalu dekat ke atas/bawah */
    }
    
</style>

<body>
    <div class="container mt-4">
        <h2>Pilih Periode Laporan</h2>
        <form method="POST">
            <div class="row g-3 align-items-center">
                <!-- Input Tanggal Awal -->
                <div class="col-md-5">
                    <label for="start_date" class="form-label">Tanggal Awal:</label>
                    <input type="text" name="start_date" id="start_date" class="form-control datepicker" placeholder="YYYY-MM-DD" 
                                    value="<?php echo isset($_POST['start_date']) ? $_POST['start_date'] : ''; ?>" required>
                </div>
                <!-- Input Tanggal Akhir -->
                <div class="col-md-5">
                    <label for="end_date" class="form-label">Tanggal Akhir:</label>
                    <input type="text" name="end_date" id="end_date" class="form-control datepicker" placeholder="YYYY-MM-DD"
                                    value="<?php echo isset($_POST['end_date']) ? $_POST['end_date'] : ''; ?>" required>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Go</button>
                </div>
            </div>
                <!-- <button type="button" class="btn btn-secondary" onclick="window.history.back();">Back</button> -->
        </form>
    </div>

    <div class="container mt-4">
        <table class="table table-bordered mx-auto" style="width: auto;">
            <thead>
                <tr>
                    <th>Timestamp Transaksi</th>
                    <th>Nama Pelanggan</th>
                    <th>Kode Barang</th>
                    <th>Ukuran</th>
                    <th>Jumlah</th>
                    <th>Harga Satuan</th>
                    <th>Subtotal</th>
                    <th class="harga-total">Harga Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    // Pastikan data hanya ditampilkan setelah form dikirim
                    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['start_date']) && isset($_POST['end_date']) && !empty($_POST['start_date']) && !empty($_POST['end_date'])) {
                        include 'laporanTransaksi.php';  // Menampilkan data berdasarkan rentang tanggal
                    }
                ?>
            </tbody>
        </table>
    </div>



    <script>
        $(document).ready(function() {
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd', // Format tanggal
                autoclose: true,      // Menutup otomatis setelah memilih tanggal
                todayHighlight: true, // Menyorot tanggal hari ini
                orientation: 'bottom',// Tampilan picker
            });
        });
    </script>



</body>
</html>