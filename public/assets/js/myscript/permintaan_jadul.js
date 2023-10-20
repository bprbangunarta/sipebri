$(document).ready(function () {
    $("#modal-edit").on("show.bs.modal", function (event) {
        var button = $(event.relatedTarget); // Tombol yang membuka modal
        var id = button.data("id"); // Ambil data-id dari tombol

        // Kirim permintaan AJAX ke route yang mengambil data berdasarkan ID
        $.ajax({
            url: "/themes/analisa/jaminan/lainnya/" + id + "/edit",
            type: "GET",
            dataType: "json",
            cache: false,
            success: function (response) {
                $("#id").val(response.jaminan_id);
                $("#jenis_agunan").val(response.jenis_agunan);
                $("#dokumen").val(response.jenis_dokumen);
                $("#no_dok").val(response.no_dokumen);
                $("#nama").val(response.atas_nama);
                $("#lokasi").val(response.lokasi);
                $("#catatan").val(response.catatan);

                var np = response.nilai_pasar;
                var ps = formatRupiah(np);
                $("#nilai_pasar").val("RP. " + " " + ps);

                var nt = response.nilai_taksasi;
                var ts = formatRupiah(nt);
                $("#nilai_taksasi").val("Rp. " + " " + ts);
            },
            error: function (xhr, status, error) {
                // Tindakan jika terjadi kesalahan dalam permintaan AJAX
                console.error("Error:", xhr.responseText);
            },
        });
    });
});
