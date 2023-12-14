$("#generate-code").on("show.bs.modal", function (event) {
    var button = $(event.relatedTarget); // Tombol yang membuka modal
    var kode = button.data("id"); // Ambil data-id dari tombol

    // Kirim permintaan AJAX ke route yang mengambil data berdasarkan ID
    $.ajax({
        url: "/themes/notifikasi/kredit/get/" + kode,
        type: "GET",
        dataType: "json",
        cache: false,
        success: function (response) {
            
            $("#kd_pengajuan").val(response.kode_pengajuan);
            $("#nm_nasabah").val(response.nama_nasabah);
            $("#generate").val(response.kode_notif);
            $("#nomor").val(response.nomor);
            $("#produk").val(response.produk_kode+" "+"-"+" "+response.nama_produk);
            $("#jw").val(response.jangka_waktu+" "+"BULAN"+" "+"-"+" "+response.metode_rps);

            var plafon = parseFloat(response.plafon)
            var pl = "Rp. " + plafon.toLocaleString("id-ID");
            $("#plafon").val(pl);
        },
        error: function (xhr, status, error) {
            // Tindakan jika terjadi kesalahan dalam permintaan AJAX
            console.error("Error:", xhr.responseText);
        },
    });
});
