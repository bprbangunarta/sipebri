$("#tolak-batal").on("show.bs.modal", function (event) {
    var button = $(event.relatedTarget); // Tombol yang membuka modal
    var kode = button.data("tolak"); // Ambil data-id dari tombol
    
    // Kirim permintaan AJAX ke route yang mengambil data berdasarkan ID
    $.ajax({
        url: "/themes/tolak/permohonan/" + kode,
        type: "GET",
        dataType: "json",
        cache: false,
        success: function (response) {
            var hasil = response[0]
            
            $("#kode").val(hasil.kode_pengajuan);
            $("#nama").val(hasil.nama_nasabah);
        },
        error: function (xhr, status, error) {
            // Tindakan jika terjadi kesalahan dalam permintaan AJAX
            console.error("Error:", xhr.responseText);
        },
    });
});
