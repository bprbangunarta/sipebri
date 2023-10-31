$(document).ready(function () {
    $("#modal-edit-lain")
        .off("show.bs.modal")
        .on("show.bs.modal", function (event) {
            $("#jenis_agunanx").empty();
            $("#jenis_dokumenx").empty();
            var button = $(event.relatedTarget); // Tombol yang membuka modal
            var id = button.data("id"); // Ambil data-id dari tombol

            // Kirim permintaan AJAX ke route yang mengambil data berdasarkan ID
            $.ajax({
                url: "/pengajuan/agunan/lain/" + id + "/edit",
                type: "GET",
                dataType: "json",
                cache: false,
                success: function (response) {
                    $("#jenis_agunanx").append(
                        $("<option>", {
                            value: response[0].jenis_agunan_kode,
                            text: response[0].jenis_agunan,
                        }).prop("selected", true)
                    );

                    $("#jenis_dokumenx").append(
                        $("<option>", {
                            value: response[0].jenis_dokumen_kode,
                            text: response[0].jenis_dokumen,
                        }).prop("selected", true)
                    );

                    //All
                    $.each(response[1], function (index, data) {
                        $("#jenis_agunanx").append(
                            $("<option>", {
                                value: data.kode,
                                text: data.jenis_agunan,
                            })
                        );
                    });

                    $.each(response[2], function (index, data) {
                        $("#jenis_dokumenx").append(
                            $("<option>", {
                                value: data.kode,
                                text: data.jenis_dokumen,
                            })
                        );
                    });

                    $("#no_dokx").val(response[0].no_dokumen);
                    $("#idx").val(response[0].id);
                    $("#idx").val(response[0].id);
                    $("#atas_namax").val(response[0].atas_nama);
                    $("#lokasix").val(response[0].lokasi);
                    $("#catatanx").val(response[0].catatan);
                },
                error: function (xhr, status, error) {
                    // Tindakan jika terjadi kesalahan dalam permintaan AJAX
                    console.error("Error:", xhr.responseText);
                },
            });
        });

    $("#otor-lain").on("show.bs.modal", function (event) {
        $("#jenis").empty();
        $("#dokumen").empty();
        var button = $(event.relatedTarget); // Tombol yang membuka modal
        var id = button.data("id"); // Ambil data-id dari tombol

        // Kirim permintaan AJAX ke route yang mengambil data berdasarkan ID
        $.ajax({
            url: "/pengajuan/agunan/lain/" + id + "/edit",
            type: "GET",
            dataType: "json",
            cache: false,
            success: function (response) {
                $("#jenis_agunanl").append(
                    $("<option>", {
                        value: response.jenis_agunan_kode,
                        text: response.jenis_agunan,
                    }).prop("selected", true)
                );

                $("#jenis_dokumenl").append(
                    $("<option>", {
                        value: response.jenis_dokumen_kode,
                        text: response.jenis_dokumen,
                    }).prop("selected", true)
                );
                $("#no_dokl").val(response.no_dokumen);
                $("#idl").val(response.id);
                $("#atas_namal").val(response.atas_nama);
                $("#lokasil").val(response.lokasi);
                $("#catatanl").val(response.catatan);
            },
            error: function (xhr, status, error) {
                // Tindakan jika terjadi kesalahan dalam permintaan AJAX
                console.error("Error:", xhr.responseText);
            },
        });
    });
});
