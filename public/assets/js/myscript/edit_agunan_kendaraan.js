$(document).ready(function () {
    $("#modal-edit-kendaraan").on("show.bs.modal", function (event) {
        $("#jenis").empty();
        $("#dokumen").empty();
        var button = $(event.relatedTarget); // Tombol yang membuka modal
        var id = button.data("id"); // Ambil data-id dari tombol

        // Kirim permintaan AJAX ke route yang mengambil data berdasarkan ID
        $.ajax({
            url: "/pengajuan/agunan/" + id + "/edit",
            type: "GET",
            dataType: "json",
            cache: false,
            success: function (response) {
                $("#jenis_agunan").append(
                    $("<option>", {
                        value: response.jenis_agunan_kode,
                        text: response.jenis_agunan,
                    }).prop("selected", true)
                );

                $("#jenis_dokumen").append(
                    $("<option>", {
                        value: response.jenis_dokumen_kode,
                        text: response.jenis_dokumen,
                    }).prop("selected", true)
                );
                $("#no_dok").val(response.no_dokumen);
                $("#id").val(response.id);
                $("#atas_nama").val(response.atas_nama);
                $("#lokasi").val(response.lokasi);
                $("#catatan").val(response.catatan);
            },
            error: function (xhr, status, error) {
                // Tindakan jika terjadi kesalahan dalam permintaan AJAX
                console.error("Error:", xhr.responseText);
            },
        });
    });

    $("#otor-kendaraan").on("show.bs.modal", function (event) {
        $("#jenis").empty();
        $("#dokumen").empty();
        var button = $(event.relatedTarget); // Tombol yang membuka modal
        var ids = button.data("id"); // Ambil data-id dari tombol

        // Kirim permintaan AJAX ke route yang mengambil data berdasarkan ID
        $.ajax({
            url: "/pengajuan/agunan/" + ids + "/edit",
            type: "GET",
            dataType: "json",
            cache: false,
            success: function (response) {
                $("#agunan").append(
                    $("<option>", {
                        value: response.jenis_agunan_kode,
                        text: response.jenis_agunan,
                    }).prop("selected", true)
                );

                $("#dokumen").append(
                    $("<option>", {
                        value: response.jenis_dokumen_kode,
                        text: response.jenis_dokumen,
                    }).prop("selected", true)
                );
                $("#ids").val(response.id);
                $("#dok").val(response.no_dokumen);
                $("#id").val(response.id);
                $("#nama").val(response.atas_nama);
                $("#lok").val(response.lokasi);
                $("#catatans").val(response.catatan);
            },
            error: function (xhr, status, error) {
                // Tindakan jika terjadi kesalahan dalam permintaan AJAX
                console.error("Error:", xhr.responseText);
            },
        });
    });
});
