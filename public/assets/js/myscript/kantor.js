$(document).ready(function () {
    $("#modal-edit").on("show.bs.modal", function (event) {
        $("#status_wil").empty();
        var button = $(event.relatedTarget); // Tombol yang membuka modal
        var id = button.data("id"); // Ambil data-id dari tombol

        // Kirim permintaan AJAX ke route yang mengambil data berdasarkan ID
        $.ajax({
            url: "/admin/kantor/" + id + "/edit",
            type: "GET",
            dataType: "json",
            cache: false,
            success: function (response) {
                // Isi modal dengan data yang diterima
                var da = JSON.stringify(response);
                var data = JSON.parse(da);
                var hasil = data[0];
                var kode = hasil.kode_kantor;
                var nama = hasil.nama_kantor;

                var kapital = kode.toUpperCase();
                $("#kode_kan").val(kapital);

                var kap = nama.charAt(0).toUpperCase() + nama.slice(1);
                $("#nama_kan").val(kap);
            },
            error: function (xhr, status, error) {
                // Tindakan jika terjadi kesalahan dalam permintaan AJAX
                console.error("Error:", xhr.responseText);
            },
        });
    });
});
