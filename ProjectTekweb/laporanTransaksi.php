<?php
    include 'koneksi.php';  // pastikan koneksi database
    
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

        if ($laporan->num_rows > 0) {
            $current_transaksi = null;
            $rowspan_data = [];
            $data = [];
    
            // Hitung jumlah baris per transaksi dan simpan data
            while ($row = $laporan->fetch_assoc()) {
                $data[] = $row;
                if (!isset($rowspan_data[$row['id_transaksi']])) {
                    $rowspan_data[$row['id_transaksi']] = 0;
                }
                $rowspan_data[$row['id_transaksi']]++;
            }
    
            // Render tabel
            foreach ($data as $index => $row) {
                $id_transaksi = $row['id_transaksi'];
                $row_count = $rowspan_data[$id_transaksi];
    
                echo "<tr>";
    
                // Jika transaksi baru, tambahkan kolom timestamp, nama, dan harga_total dengan rowspan
                if ($current_transaksi !== $id_transaksi) {
                    $current_transaksi = $id_transaksi;
                    $counter = 1;
                    echo "<td rowspan='$row_count' style='vertical-align: middle; text-align: center;'>" . htmlspecialchars($row['tanggal_transaksi']) . "</td>";
                    echo "<td rowspan='$row_count' style='vertical-align: middle; text-align: center;'>" . htmlspecialchars($row['nama']) . "</td>";
                    // Menampilkan harga_total hanya pada baris pertama
                    // echo "<td rowspan='$row_count' style='vertical-align: middle; text-align: center;'>" . htmlspecialchars($row['harga_total']) . "</td>";
                }
    
                // Tampilkan data lainnya
                echo "<td>" . htmlspecialchars($row['kode_barang']) . "</td>";
                echo "<td>" . htmlspecialchars($row['ukuran']) . "</td>";
                echo "<td>" . htmlspecialchars($row['jumlah']) . "</td>";
                echo "<td>" . htmlspecialchars($row['harga_satuan']) . "</td>";
                echo "<td>" . htmlspecialchars($row['subtotal']) . "</td>";
                // echo "<td class='harga-total'>{$row['harga_total']}</td>";
                
                if ($current_transaksi === $id_transaksi && $counter <= 1) {
                    // Menampilkan harga_total hanya pada baris pertama
                    echo "<td rowspan='$row_count' style='vertical-align: middle; text-align: center;'>" . htmlspecialchars($row['harga_total']) . "</td>";
                }
                $counter++;
            }
        } else {
            echo "<tr><td colspan='8'>Tidak ada data untuk periode ini.</td></tr>";
        }
    

        // // Tampilkan hasil query dalam tabel
        // if ($laporan->num_rows > 0) {
        //     while ($row = $laporan->fetch_assoc()) {
        //         echo "<tr>";
        //         echo "<td>" . $row['tanggal_transaksi'] . "</td>";
        //         echo "<td>" . $row['nama'] . "</td>";
        //         echo "<td>" . $row['kode_barang'] . "</td>";
        //         echo "<td>" . $row['ukuran'] . "</td>";
        //         echo "<td>" . $row['jumlah'] . "</td>";
        //         echo "<td>" . $row['harga'] . "</td>";
        //         echo "<td>" . $row['subtotal'] . "</td>";
        //         echo "<td>" . $row['harga_total'] . "</td>";
        //         echo "</tr>";
        //     }
        // } else {
        //     echo "<tr><td colspan='8'>Tidak ada data untuk periode ini.</td></tr>";
        // }

        // Tutup koneksi
        $conn->close();
    }
    ?>