<?php
    include 'koneksi.php';
    
    // Proses data jika form disubmit
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['start_date']) && isset($_POST['end_date']) && !empty($_POST['start_date']) && !empty($_POST['end_date'])) {
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];

        // Mengubah start_date dan end_date menjadi format timestamp yang sesuai
        $start_datetime = $start_date . " 00:00:00";  // Tanggal awal + jam 00:00:00
        $end_datetime = $end_date . " 23:59:59";      // Tanggal akhir + jam 23:59:59

        // Query untuk menampilkan data transaksi sesuai rentang tanggal
        $query = "SELECT t.id_transaksi, t.tanggal_transaksi, p.nama, prod.kode_barang, u.ukuran, dt.jumlah, 
                        ROUND(dt.subtotal / dt.jumlah) as 'harga_satuan', dt.subtotal, t.harga_total 
                    FROM transaksi t 
                    JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan 
                    JOIN detail_transaksi dt ON t.id_transaksi = dt.id_transaksi 
                    JOIN detail_produk dp ON dt.id_detprod = dp.id_detprod 
                    JOIN produk prod ON dp.id_barang = prod.id_barang 
                    JOIN ukuran u ON dp.id_ukuran = u.id_ukuran 
                    WHERE t.tanggal_transaksi BETWEEN '$start_datetime' AND '$end_datetime'
                    ORDER BY t.id_transaksi";
        
        // Jalankan query
        $laporan = $conn->query($query);

        // Cek jika ada data pada laporan transaksi sesuai dgn tanggal yg dipilih
        if ($laporan->num_rows > 0) {
            $current_transaksi = null;      // untuk melacak id_transaksi yg sedang diproses
            $rowspan_data = [];             // untuk menyimpan jumlah baris per transaksi (sesuai id_transaksi)
            $data = [];                     // untuk menyimpan semua hasil query
    
            // Hitung jumlah baris per transaksi dan simpan data
            while ($row = $laporan->fetch_assoc()) {
                $data[] = $row;
                if (!isset($rowspan_data[$row['id_transaksi']])) {
                    $rowspan_data[$row['id_transaksi']] = 0;
                }
                $rowspan_data[$row['id_transaksi']]++;
            }
    
            // Menampilkan data dalam tabel
            foreach ($data as $index => $row) {
                $id_transaksi = $row['id_transaksi'];           // Mengambil id_transaksi dari data saat ini
                $row_count = $rowspan_data[$id_transaksi];      // Menyimpan jumlah baris per transaksi saat ini
    
                echo "<tr>";
    
                // Jika transaksi baru, tambahkan kolom timestamp, nama, dan harga_total dengan rowspan
                if ($current_transaksi !== $id_transaksi) {
                    $current_transaksi = $id_transaksi;
                    $counter = 1;       // counter untuk menghitung sudah di baris ke berapa
                    // Transaksi yang sama hanya akan dimunculkan satu kali dan kolom timestamp dan nama dimerge
                    echo "<td rowspan='$row_count' style='vertical-align: middle; text-align: center;'>" . htmlspecialchars($row['tanggal_transaksi']) . "</td>";
                    echo "<td rowspan='$row_count' style='vertical-align: middle; text-align: center;'>" . htmlspecialchars($row['nama']) . "</td>";
                }
    
                // Tampilkan data lainnya = detail transaksi
                echo "<td>" . htmlspecialchars($row['kode_barang']) . "</td>";
                echo "<td>" . htmlspecialchars($row['ukuran']) . "</td>";
                echo "<td>" . htmlspecialchars($row['jumlah']) . "</td>";
                echo "<td>" . number_format($row['harga_satuan'], 0, ',', '.') . "</td>";
                echo "<td>" . number_format($row['subtotal'], 0, ',', '.') . "</td>";

                // Menampilkan harga_total hanya pada baris pertama
                if ($current_transaksi === $id_transaksi && $counter <= 1) {
                    echo "<td rowspan='$row_count' style='vertical-align: middle; text-align: center;'>" . number_format($row['harga_total'], 0, ',', '.') . "</td>";
                }
                $counter++;
            }
        } 
        // Jika tidak ada transaksi pada periode tsb, akan memunculkan tulisan dan alert
        else {
            echo "<tr><td colspan='8' style='text-align: center;'>Data tidak tersedia untuk periode ini</td></tr>";
            echo "<script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Tidak Ada Data',
                            text: 'Tidak ada transaksi untuk periode ini'
                        });
                    </script>";
        }

        // Tutup koneksi
        $conn->close();
    }
    ?>