$(document).ready(function () {
    $("#modal-penjadwalan").on("show.bs.modal", function (event) {
        $("#kode_petugas").empty();
        var button = $(event.relatedTarget); // Tombol yang membuka modal
        var id = button.data("id"); // Ambil data-id dari tombol

        //Kirim permintaan AJAX ke route yang mengambil data berdasarkan ID
        $.ajax({
            url: "/analisa/penjadwalan/" + id,
            type: "GET",
            dataType: "json",
            cache: false,
            success: function (response) {
                // Isi modal dengan data yang diterima
                var da = JSON.stringify(response[0]);
                var data = JSON.parse(da);
                var hasil = data[0];

                var sr = JSON.stringify(response[1]);
                var datas = JSON.parse(sr);

                $("#kode_nasabah").val(hasil.kode_nasabah);
                $("#kode_pengajuan").val(hasil.kode_pengajuan);
                $("#nama_nasabah").val(hasil.nama_nasabah);
                $("#alamat").val(hasil.alamat_ktp);

                $("#kode_petugas").append(
                    $("<option>", {
                        value: hasil.surveyor_kode,
                        text: hasil.name,
                    }).prop("selected", true)
                );

                //Kantor
                $.each(datas, function (index, item) {
                    if (item.code_user != hasil.surveyor_kode) {
                        $("#kode_petugas").append(
                            $("<option>", {
                                value: item.code_user,
                                text: item.nama_user,
                            })
                        );
                    }
                });

                if (hasil.tgl_survei !== null) {
                    $("#datepicker-tanggal-survei").val(hasil.tgl_survei);
                    $("#datepicker-tanggal-survei").prop("disabled", true);
                } else {
                    $("#datepicker-tanggal-survei").val("");
                }

                if (hasil.tgl_jadul_1 !== null) {
                    $("#datepicker-tanggal-survei1").val(hasil.tgl_jadul_1);
                    $("#datepicker-tanggal-survei1").prop("disabled", true);
                } else {
                    $("#datepicker-tanggal-survei1").val("");
                }

                if (hasil.tgl_jadul_2 !== null) {
                    $("#datepicker-tanggal-survei2").val(hasil.tgl_jadul_2);
                    $("#datepicker-tanggal-survei2").prop("disabled", true);
                } else {
                    $("#datepicker-tanggal-survei2").val("");
                }

                $("#catatan").append(
                    "<p>" +
                        "Survei Peretama :" +
                        " " +
                        response[0][0].catatan_survei ||
                        "" +
                            "\n" +
                            "Survei Kedua :" +
                            " " +
                            response[0][0].catatan_resurvei_1 ||
                        "" +
                            "\n" +
                            "Survei Ketiga :" +
                            " " +
                            response[0][0].catatan_resurvei_2 ||
                        "" + "\n" + "</p>"
                );
            },
            error: function (xhr, status, error) {
                // Tindakan jika terjadi kesalahan dalam permintaan AJAX
                console.error("Error:", xhr.responseText);
            },
        });
    });
});
