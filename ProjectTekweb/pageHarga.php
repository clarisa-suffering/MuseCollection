
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Coba Project</title>
    <!-- <link rel="stylesheet" href="Latihan Test (card form) Style.css"> -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.0/mdb.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <div class="container text-center mt-5">
      <div class="input-group">
        <div class="form-outline border rounded" data-mdb-input-init>
          <input type="search" id="findKode" class="form-control" placeholder="Search">
          <!-- <label class="form-label" for="findKode">Search</label> -->
        </div>
        <button type="button" id="btnSearch" class="btn btn-primary" data-mdb-ripple-init>
          <i class="fas fa-search"></i>
        </button>
      </div>
    </div>

    <div class="container text-center mt-3">
        <div class="col-12 col-md-6">
          <form id = "price-form">
            <div class="row mb-3 align-items-center">
              <label for="inputKode" class="col-sm-3 col-form-label">Kode</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="inputKode">
                </div>
            </div>
            <div class="row mb-3 align-items-center">
              <label for="inputStok" class="col-sm-3 col-form-label">Stok</label>
              <div class="col-sm-9">
                <input type="text" class="form-control" id="inputStok">
              </div>
            </div>
            <div class="row mb-3 align-items-center">
              <label for="inputHarga" class="col-sm-3 col-form-label">Harga</label>
              <div class="col-sm-9">
                  <input type="text" class="form-control" id="inputHarga">
              </div>
            </div>
            <button type="submit" id="btnEditHarga" class="btn btn-primary">Edit Harga</button>
            <button type="button" id="btnBack" class="btn btn-primary">Kembali</button>
          </form>
        </div>
       </div>


      <!-- Modal kode tidak ditemukan -->
      <div class="modal fade" id="notFoundModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="notFoundModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="notFoundModalLabel">NOT FOUND</h1>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              Maaf, kode barang tidak ditemukan
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Modal konfirmasi sukses -->
      <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="successModalLabel">SUCCESS</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Perubahan harga berhasil disimpan
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    

  <script>
    $(document).ready(function () {

      $("#btnSearch").on("click", function () {
          const inputKode = $("#findKode").val();

          if (inputKode !== '') {
              $.ajax({
                  url: "searchKode.php", // File pencarian
                  method: "GET",
                  data: { inputKode: inputKode },
                  success: function (response) {
                      const data = JSON.parse(response);

                      if (data.error) {
                                // Jika data tidak ditemukan, tampilkan pesan error
                                const notFoundModal = new bootstrap.Modal(document.getElementById('notFoundModal'));
                                notFoundModal.show();
                                $("#inputKode").val("");
                                $("#inputStok").val("");
                                $("#inputHarga").val("");
                            } else {
                                // Jika data ditemukan, isi dengan data dari database
                                $("#inputKode").val(data.kode_barang).prop("disabled", true); // Disable input kode
                                $("#inputStok").val(data.stok_toko).prop("disabled", true); // Disable input stok
                                $("#inputHarga").val(data.harga); // Isi harga

                                if (data.stok_toko == 0) {
                                  $("#inputHarga").prop("disabled", true);
                                  $("#btnEditHarga").prop("disabled", true);
                                   // Tampilkan alert setelah data selesai diisi
                                  setTimeout(function () {
                                      alert("Stok barang tidak tersedia, harga tidak dapat diedit");
                                      $("#findKode").val("");
                                      $("#inputKode").val("");
                                      $("#inputStok").val("");
                                      $("#inputHarga").val("");
                                  }, 100); // Tunggu 100ms untuk memastikan data terlihat
                                } else {
                                  $("#inputHarga").prop("disabled", false);
                                  $("#btnEditHarga").prop("disabled", false);
                                }
                              }
                  },
                  error: function () {
                      console.log("Gagal mengambil data");
                  }
              });
        }
    });

    // Edit Harga
    $("#btnEditHarga").on("click", function() {
      event.preventDefault();
      const kodeBarang = $("#inputKode").val();
      const hargaBaru = $("#inputHarga").val();

      // Pastikan ada
      if (kodeBarang !== "" && hargaBaru !== "") {
        // Validasi BR2: Harga harus bilangan positif dalam ribuan
        if (isNaN(hargaBaru) || parseInt(hargaBaru) <= 0 || parseInt(hargaBaru) % 1000 !== 0) {
            alert("Harga harus dalam bentuk bilangan positif dalam ribu rupiah.");
            return;
        }

       // Log data yang dikirim ke server untuk debugging
       console.log("Kode Barang:", kodeBarang);
        console.log("Harga Baru:", hargaBaru);

        $.ajax({
          url: 'editHarga.php', // Endpoint untuk menyimpan data harga yang diedit
          method: 'POST',
          data: {
            kode_barang: kodeBarang,
            harga_baru: hargaBaru
          },
          success: function (response) {
              try {
                  const data = JSON.parse(response);
                  if (data.success) {
                      // Tampilkan modal sukses
                      const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                      successModal.show();
                  } else {
                      alert('Gagal memperbarui harga');
                  }
              } catch (e) {
                  console.error('Error parsing JSON:', e);
                  console.log('Response from server:', response); // Untuk debugging
              }
          },
          error: function () {
              console.log('Error saving data');
          }
      });
    } else {
        alert('Mohon isi semua data');
    }
  });
  


});
  </script>
</body>
</html>