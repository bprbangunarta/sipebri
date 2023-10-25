$(document).ready(function () {
    $("#modal-edit-tanah").on("show.bs.modal", function (event) {
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
                $("#jenis_agunans").append(
                    $("<option>", {
                        value: response.jenis_agunan_kode,
                        text: response.jenis_agunan,
                    }).prop("selected", true)
                );

                $("#jenis_dokumens").append(
                    $("<option>", {
                        value: response.jenis_dokumen_kode,
                        text: response.jenis_dokumen,
                    }).prop("selected", true)
                );
                $("#ids").val(response.id);
                $("#no_doks").val(response.no_dokumen);
                $("#ids").val(response.id);
                $("#atas_namas").val(response.atas_nama);
                $("#lokasis").val(response.lokasi);

                var lu = response.luas;
                $("#luass").val(formatRupiah(lu));
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
