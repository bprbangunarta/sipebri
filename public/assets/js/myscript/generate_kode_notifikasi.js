$("#generate-code").on("show.bs.modal", function (event) {
    var button = $(event.relatedTarget); // Tombol yang membuka modal
    var kode = button.data("id"); // Ambil data-id dari tombol

    // Kirim permintaan AJAX ke route yang mengambil data berdasarkan ID
    $.ajax({
        url: "/themes/notifikasi/kredit/generate/" + kode,
        type: "GET",
        dataType: "json",
        cache: false,
        success: function (response) {
            $("#kd_pengajuan").val(response.kode_pengajuan);
            $("#nm_nasabah").val(response.nama_nasabah);
            $("#generate").val(response.generate);
        },
        error: function (xhr, status, error) {
            // Tindakan jika terjadi kesalahan dalam permintaan AJAX
            console.error("Error:", xhr.responseText);
        },
    });
});
