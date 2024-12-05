<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Karyawan</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>
    <h1>Absensi Karyawan</h1>

    <!-- Dropdown Filter Tanggal -->
    <label for="tanggal">Filter Tanggal:</label>
    <input type="date" id="tanggal">

    <!-- Input Search Nama -->
    <label for="id_karyawan">Cari ID Karyawan:</label>
    <input type="text" id="id_karyawan" placeholder="ID Karyawan">

    <!-- Tombol Filter -->
    <button onclick="filterAbsensi()">Tampilkan</button>

    <!-- Tabel Absensi -->
    <table>
        <thead>
            <tr>
                <th>ID Absensi</th>
                <th>ID Karyawan</th>
                <th>Jam</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody id="data-absensi">
            <tr>
                <td colspan="4">Tidak ada data.</td>
            </tr>
        </tbody>
    </table>

    <script>
        // Function to fetch and display all attendance data on page load
        window.onload = function() {
            // Call the function to fetch all data
            filterAbsensi();
        };

        // Fungsi untuk menampilkan data absensi
        function filterAbsensi() {
            const tanggal = document.getElementById('tanggal').value;
            const id_karyawan = document.getElementById('id_karyawan').value;

            // Debugging: log URL yang dikirimkan
            console.log(`Fetching data from: absensi.php?tanggal=${tanggal}&id_karyawan=${id_karyawan}`);

            // Panggil API absensi.php
            let url = `MelihatAbsensi.php?tanggal=${tanggal}&id_karyawan=${id_karyawan}`;
            if (!tanggal) {
                url = url.replace("&tanggal=", "");
            }
            if (!id_karyawan) {
                url = url.replace("&id_karyawan=", "");
            }

            fetch(url, {
                method: 'GET',
                headers: {
                    'Cache-Control': 'no-cache',  // Memastikan cache tidak digunakan
                }
            })
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('data-absensi');
                tbody.innerHTML = '';

                if (data.length > 0) {
                    data.forEach(row => {
                        const status = row.status === 1 ? 'Hadir' : 'Tidak Hadir';
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${row.id_absensi}</td>
                            <td>${row.id_karyawan}</td>
                            <td>${row.jam}</td>
                            <td>${status}</td>
                        `;
                        tbody.appendChild(tr);
                    });
                } else {
                    tbody.innerHTML = '<tr><td colspan="4">Tidak ada data.</td></tr>';
                }
            })
            .catch(error => console.error('Error:', error));
        }
    </script>
</body>
</html>
