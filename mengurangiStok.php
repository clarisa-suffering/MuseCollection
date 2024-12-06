<?php
include 'koneksi.php'; 
include 'detailProduk.php'; 

$detailProduk = new DetailProduk($conn);

$kategori_penjualan = $_POST['kategori_penjualan'];
$details = json_decode($_POST['details'], true);

$allStockReduced = true;
$errorMessages = [];

foreach ($details as $detail) {
    $id_detprod = $detail['id_detprod'];
    $jumlah = $detail['jumlah'];

    $isStockReduced = false;
    if ($kategori_penjualan === 'retail') {
        $isStockReduced = $detailProduk->mengurangiStokToko($id_detprod, $jumlah);
    } else if ($kategori_penjualan === 'PO') {
        $isStockReduced = $detailProduk->mengurangiStokGudang($id_detprod, $jumlah);
    }

    if (!$isStockReduced) {
        $allStockReduced = false;
        $errorMessages[] = "Error mengurangi stok ID $id_detprod";
    }
}

if ($allStockReduced) {
    echo "Sukses";
} else {
    // Return the collected error messages if there were failures
    echo implode(", ", $errorMessages);
}
?>
