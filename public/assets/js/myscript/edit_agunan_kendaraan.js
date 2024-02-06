$(document).ready(function () {
    $("#modal-edit-kendaraan")
        .off("show.bs.modal")
        .on("show.bs.modal", function (event) {
            $("#jenis_agunan").empty();
            $("#jenis_dokumen").empty();

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
                            value: response[0].jenis_agunan_kode,
                            text: response[0].jenis_agunan,
                        }).prop("selected", true)
                    );

                    $("#jenis_dokumen").append(
                        $("<option>", {
                            value: response[0].jenis_dokumen_kode,
                            text: response[0].jenis_dokumen,
                        }).prop("selected", true)
                    );

                    //All
                    $.each(response[1], function (index, data) {
                        $("#jenis_agunan").append(
                            $("<option>", {
                                value: data.kode,
                                text: data.jenis_agunan,
                            })
                        );
                    });

                    $.each(response[2], function (index, data) {
                        $("#jenis_dokumen").append(
                            $("<option>", {
                                value: data.kode,
                                text: data.jenis_dokumen,
                            })
                        );
                    });

                    $("#dati").append(
                        $("<option>", {
                            value: response[0].kode_dati,
                            text: response[0].nama_dati,
                        }).prop("selected", true)
                    );

                    $("#no_dok").val(response[0].no_dokumen);
                    $("#id").val(response[0].id);
                    $("#atas_nama").val(response[0].atas_nama);
                    $("#lokasi").val(response[0].lokasi);
                    $("#no_mesin").val(response[0].no_mesin);
                    $("#no_rangka").val(response[0].no_rangka);
                    $("#no_polisi").val(response[0].no_polisi);
                    $("#merek").val(response[0].merek);
                    $("#tahun").val(response[0].tahun);
                    $("#warna").val(response[0].warna);
                    $("#tipe_kendaraan").val(response[0].tipe_kendaraan);
                    $("#lokasi_kendaraan").val(response[0].lokasi);
                    $("#catatan").val(response[0].catatan);
                },
                error: function (xhr, status, error) {
                    // Tindakan jika terjadi kesalahan dalam permintaan AJAX
                    console.error("Error:", xhr.responseText);
                },
            });
        });

    $("#otor-kendaraan")
        .off("show.bs.modal")
        .on("show.bs.modal", function (event) {
            $("#agunan").empty();
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
                            value: response[0].jenis_agunan_kode,
                            text: response[0].jenis_agunan,
                        }).prop("selected", true)
                    );

                    $("#dokumen").append(
                        $("<option>", {
                            value: response[0].jenis_dokumen_kode,
                            text: response[0].jenis_dokumen,
                        }).prop("selected", true)
                    );
                    $("#ids").val(response[0].id);
                    $("#dok").val(response[0].no_dokumen);
                    $("#id").val(response[0].id);
                    $("#nama").val(response[0].atas_nama);
                    $("#lok").val(response[0].lokasi);
                    $("#catatans").val(response[0].catatan);
                },
                error: function (xhr, status, error) {
                    // Tindakan jika terjadi kesalahan dalam permintaan AJAX
                    console.error("Error:", xhr.responseText);
                },
            });
        });
});
