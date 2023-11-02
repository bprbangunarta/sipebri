$(document).ready(function () {
    $("#modal-edit-tanah")
        .off("show.bs.modal")
        .on("show.bs.modal", function (event) {
            $("#jenis_agunans").empty();
            $("#jenis_dokumens").empty();
            var button = $(event.relatedTarget); // Tombol yang membuka modal
            var id = button.data("id"); // Ambil data-id dari tombol
            
            // Kirim permintaan AJAX ke route yang mengambil data berdasarkan ID
            $.ajax({
                url: "/pengajuan/agunan/tanah/" + id + "/edit",
                type: "GET",
                dataType: "json",
                cache: false,
                success: function (response) {
                    console.log(response[0])
                    $("#jenis_agunans").append(
                        $("<option>", {
                            value: response[0].jenis_agunan_kode,
                            text: response[0].jenis_agunan,
                        }).prop("selected", true)
                    );

                    $("#jenis_dokumens").append(
                        $("<option>", {
                            value: response[0].jenis_dokumen_kode,
                            text: response[0].jenis_dokumen,
                        }).prop("selected", true)
                    );

                    //All
                    $.each(response[1], function (index, data) {
                        $("#jenis_agunans").append(
                            $("<option>", {
                                value: data.kode,
                                text: data.jenis_agunan,
                            })
                        );
                    });

                    $.each(response[2], function (index, data) {
                        $("#jenis_dokumens").append(
                            $("<option>", {
                                value: data.kode,
                                text: data.jenis_dokumen,
                            })
                        );
                    });

                    $("#datit").append(
                        $("<option>", {
                            value: response[0].kode_dati,
                            text: response[0].nama_dati,
                        }).prop("selected", true)
                    );
                    $("#idt").val(response[0].id);
                    $("#no_doks").val(response[0].no_dokumen);
                    $("#ids").val(response[0].id);
                    $("#atas_namas").val(response[0].atas_nama);
                    $("#lokasis").val(response[0].lokasi);
                    $("#catat").val(response[0].catatan);

                    var lu = response[0].luas;
                    $("#luass").val(formatRupiah(lu));
                },
                error: function (xhr, status, error) {
                    // Tindakan jika terjadi kesalahan dalam permintaan AJAX
                    console.error("Error:", xhr.responseText);
                },
            });
        });

    $("#otor-tanah")
        .off("show.bs.modal")
        .on("show.bs.modal", function (event) {
            $("#ja").empty();
            $("#jd").empty();
            var button = $(event.relatedTarget); // Tombol yang membuka modal
            var id = button.data("id"); // Ambil data-id dari tombol

            // Kirim permintaan AJAX ke route yang mengambil data berdasarkan ID
            $.ajax({
                url: "/pengajuan/agunan/tanah/" + id + "/edit",
                type: "GET",
                dataType: "json",
                cache: false,
                success: function (response) {
                    $("#ja").append(
                        $("<option>", {
                            value: response[0].jenis_agunan_kode,
                            text: response[0].jenis_agunan,
                        }).prop("selected", true)
                    );

                    $("#jd").append(
                        $("<option>", {
                            value: response[0].jenis_dokumen_kode,
                            text: response[0].jenis_dokumen,
                        }).prop("selected", true)
                    );
                    $("#idd").val(response[0].id);
                    $("#no_d").val(response[0].no_dokumen);
                    $("#atas").val(response[0].atas_nama);
                    $("#lo").val(response[0].lokasi);

                    var lu = response[0].luas;
                    if (lu !== null) {
                        $("#lu").val(formatRupiah(lu));
                    }
                },
                error: function (xhr, status, error) {
                    // Tindakan jika terjadi kesalahan dalam permintaan AJAX
                    console.error("Error:", xhr.responseText);
                },
            });
        });

    function formatRupiah(angka, prefix) {
        var number_string = angka.replace(/[^,\d]/g, "").toString(),
            split = number_string.split(","),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            separator = sisa ? "." : "";
            rupiah += separator + ribuan.join(".");
        }

        rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
        return prefix == undefined ? rupiah : rupiah ? rupiah : "";
    }
});
