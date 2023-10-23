$(document).ready(function () {
    $("#jadwal-ulang").on("show.bs.modal", function (event) {
        var button = $(event.relatedTarget); // Tombol yang membuka modal
        var pengajuan = button.data("pengajuan"); // Ambil data-id dari tombol

        // Kirim permintaan AJAX ke route yang mengambil data berdasarkan ID
        $.ajax({
            url: "/themes/permohonan/data_jadul/" + pengajuan,
            type: "GET",
            dataType: "json",
            cache: false,
            success: function (response) {
                $("#id").val(response.id);
                $("#kd_pengajuan").val(response.kode_pengajuan);
                $("#nm_nasabah").val(response.nama_nasabah);
            },
            error: function (xhr, status, error) {
                // Tindakan jika terjadi kesalahan dalam permintaan AJAX
                console.error("Error:", xhr.responseText);
            },
        });
    });
});
