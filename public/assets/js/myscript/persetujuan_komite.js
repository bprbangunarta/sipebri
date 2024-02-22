var usulan_plafon = document.getElementById("usulan_plafon");
if (usulan_plafon) {
    usulan_plafon.addEventListener("keyup", function (e) {
        usulan_plafon.value = formatRupiah(this.value, "Rp. ");
    });
}

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
    return prefix == undefined ? rupiah : rupiah ? "Rp. " + rupiah : "";
}

$(document).ready(function () {
    $("#modal-persetujuan").on("show.bs.modal", function (event) {
        $("#komite").empty();
        $("#metode").empty();
        var button = $(event.relatedTarget);
        var pengajuan = button.data("pengajuan");
        var token = $('meta[name="csrf-token"]').attr("content");

        var token = $('meta[name="csrf-token"]').attr("content");
        var data = {
            field: pengajuan,
            _token: token,
        };
        $.ajax({
            url: "/themes/komite/kredit/data",
            type: "post",
            dataType: "json",
            cache: false,
            data: data,
            success: function (response) {
                var da = JSON.stringify(response);
                var data = JSON.parse(da);
                var hasil = data[0];

                $("#kode").val(hasil.kode_pengajuan);
                var rp = parseInt(hasil.max_plafond);
                var c = hasil.max_plafond;

                $("#max").val("Rp. " + " " + formatRupiah(rp.toString()));
                $("#pengajuan").val(pengajuan);
                var komite = $("#komite");
                var role = hasil.role_user;
                var kategori = hasil.kategori;

                var pal = hasil.plafon;
                var pl = formatRupiah(pal);
                $("#plafon").val("Rp. " + " " + pl);
                $("#provisi").val(hasil.b_provisi) ?? 0;
                $("#bunga").val(hasil.suku_bunga);
                $("#admin").val(hasil.b_admin) ?? 0;

                //Persetujuan
                if (kategori === "RELOAN") {
                    if (role === "Staff Analis") {
                        var options = [
                            { value: "Naik Kasi", text: "Naik Kasi" },
                        ];
                    } else if (role == "Kasi Analis") {
                        var options = [
                            { value: "Naik Komite I", text: "Naik Komite I" },
                        ];
                    } else if (role == "Kabag Analis") {
                        var options = [
                            { value: "Naik Komite II", text: "Naik Komite II" },
                        ];
                    } else if (role == "Direksi") {
                        var options = [
                            { value: "", text: "--Pilih--" },
                            { value: "Disetujui", text: "Disetujui" },
                            { value: "Dibatalkan", text: "Dibatalkan" },
                            { value: "Ditolak", text: "Ditolak" },
                        ];
                    }
                } else {
                    if (
                        role === "Staff Analis" &&
                        pal >= 1000 &&
                        pal <= 10000000
                    ) {
                        var options = [
                            { value: "", text: "--Pilih--" },
                            { value: "Disetujui", text: "Disetujui" },
                            { value: "Dibatalkan", text: "Dibatalkan" },
                            { value: "Ditolak", text: "Ditolak" },
                        ];
                    } else if (role == "Staff Analis" && pal > 10000000) {
                        var options = [
                            { value: "Naik Kasi", text: "Naik Kasi" },
                        ];
                    }

                    if (
                        role === "Customer Service" &&
                        pal >= 1000 &&
                        pal <= 10000000
                    ) {
                        var options = [
                            { value: "", text: "--Pilih--" },
                            { value: "Disetujui", text: "Disetujui" },
                            { value: "Dibatalkan", text: "Dibatalkan" },
                            { value: "Ditolak", text: "Ditolak" },
                        ];
                    } else if (role == "Staff Analis" && pal > 10000000) {
                        var options = [
                            { value: "Naik Kasi", text: "Naik Kasi" },
                        ];
                    }

                    if (
                        role === "Kepala Kantor Kas" &&
                        pal >= 1000 &&
                        pal <= 10000000
                    ) {
                        var options = [
                            { value: "", text: "--Pilih--" },
                            { value: "Disetujui", text: "Disetujui" },
                            { value: "Dibatalkan", text: "Dibatalkan" },
                            { value: "Ditolak", text: "Ditolak" },
                        ];
                    } else if (role == "Staff Analis" && pal > 10000000) {
                        var options = [
                            { value: "Naik Kasi", text: "Naik Kasi" },
                        ];
                    }

                    if (
                        role === "Kasi Analis" &&
                        pal > 10000001 &&
                        pal <= 35000000
                    ) {
                        var options = [
                            { value: "", text: "--Pilih--" },
                            { value: "Disetujui", text: "Disetujui" },
                            { value: "Dibatalkan", text: "Dibatalkan" },
                            { value: "Ditolak", text: "Ditolak" },
                        ];
                    } else if (role == "Kasi Analis" && pal > 35000000) {
                        var options = [
                            { value: "", text: "--Pilih--" },
                            { value: "Naik Komite I", text: "Naik Komite I" },
                            { value: "Dibatalkan", text: "Dibatalkan" },
                            { value: "Ditolak", text: "Ditolak" },
                        ];
                    }

                    if (
                        role === "Kabag Analis" &&
                        pal >= 35000001 &&
                        pal <= 75000000
                    ) {
                        var options = [
                            { value: "", text: "--Pilih--" },
                            { value: "Disetujui", text: "Disetujui" },
                            { value: "Dibatalkan", text: "Dibatalkan" },
                            { value: "Ditolak", text: "Ditolak" },
                        ];
                    } else if (role == "Kabag Analis" && pal > 75000001) {
                        var options = [
                            { value: "Naik Komite II", text: "Naik Komite II" },
                            { value: "Dibatalkan", text: "Dibatalkan" },
                            { value: "Ditolak", text: "Ditolak" },
                        ];
                    }

                    if (role === "Direksi" && pal > 75000000) {
                        var options = [
                            { value: "", text: "--Pilih--" },
                            { value: "Disetujui", text: "Disetujui" },
                            { value: "Dibatalkan", text: "Dibatalkan" },
                            { value: "Ditolak", text: "Ditolak" },
                        ];
                    }
                }

                //Select Option
                $.each(response[1], function (index, option) {
                    var selected = "";
                    if (option.nama_metode == response[0].metode_rps) {
                        selected = "selected";
                    }
                    $("#metode").append(
                        '<option value="' +
                            option.nama_metode +
                            '" ' +
                            selected +
                            ">" +
                            option.nama_metode +
                            "</option>"
                    );
                });

                $.each(options, function (index, option) {
                    komite.append(
                        $("<option></option>")
                            .attr("value", option.value)
                            .text(option.text)
                    );
                });

                //Menghitung RC Persetujuan Komite Berdasarkan Select Option yang dipilih
                $("#metode").on("change", function () {
                    var usulan =
                        parseFloat(
                            $("#usulan_plafon").val().replace(/[^\d]/g, "")
                        ) || 0;
                    var keuangan = hasil.keuangan_perbulan;
                    var sb = $("#bunga").val();
                    var jangka_waktu = hasil.jangka_waktu;
                    var selectedValue = $(this).val();

                    if (selectedValue == "FLAT") {
                        var bunga =
                            (parseFloat(usulan) * parseFloat(sb)) / 100 / 12;
                        var poko =
                            parseFloat(usulan) / parseFloat(jangka_waktu);
                        var angsuran = Math.ceil(bunga) + poko;
                        var rc = (angsuran / parseFloat(keuangan)) * 100;

                        $("#rc").val(rc.toFixed(2) + " " + "%");
                    } else if (
                        selectedValue == "EFEKTIF MUSIMAN" ||
                        selectedValue == "EFEKTIF"
                    ) {
                        var bg = (((parseFloat(usulan) * sb) / 100) * 30) / 365;
                        var rc = (bg / keuangan) * 100;

                        $("#rc").val(rc.toFixed(2) + " " + "%");
                    } else if (selectedValue == "EFEKTIF ANUITAS") {
                        var ssb = sb / 100;
                        var bunga = ssb / 12;
                        var anuitas =
                            (usulan * bunga) /
                            (1 - 1 / Math.pow(1 + bunga, jangka_waktu));
                        var rc = (anuitas / keuangan) * 100;
                        $("#rc").val(rc.toFixed(2) + "%");
                    } else {
                        //
                        var bunga =
                            (parseFloat(usulan) * parseFloat(sb)) / 100 / 12;
                        var poko =
                            parseFloat(usulan) / parseFloat(jangka_waktu);
                        var angsuran = Math.ceil(bunga) + poko;
                        var rc = (angsuran / parseFloat(keuangan)) * 100;
                        $("#rc").val(rc.toFixed(2) + " " + "%");
                    }
                });

                //Menghitung RC Persetujuan Komite
                $("#usulan_plafon, #bunga").keyup(function () {
                    var usulan =
                        parseFloat(
                            $("#usulan_plafon").val().replace(/[^\d]/g, "")
                        ) || 0;
                    var keuangan = hasil.keuangan_perbulan;
                    var sb = $("#bunga").val();
                    var jangka_waktu = hasil.jangka_waktu;
                    var mtd = $("#metode").val();

                    if (hasil.metode_rps == "FLAT" || mtd == "FLAT") {
                        //
                        var bunga =
                            (parseFloat(usulan) * parseFloat(sb)) / 100 / 12;
                        var poko =
                            parseFloat(usulan) / parseFloat(jangka_waktu);
                        var angsuran = Math.ceil(bunga) + poko;
                        var rc = (angsuran / parseFloat(keuangan)) * 100;

                        $("#rc").val(rc.toFixed(2) + " " + "%");
                    } else if (
                        hasil.metode_rps == "EFEKTIF MUSIMAN" ||
                        mtd == "EFEKTIF MUSIMAN"
                    ) {
                        var bg = (((parseFloat(usulan) * sb) / 100) * 30) / 365;
                        var rc = (bg / keuangan) * 100;

                        $("#rc").val(rc.toFixed(2) + " " + "%");
                    } else if (
                        hasil.metode_rps == "EFEKTIF" ||
                        mtd == "EFEKTIF"
                    ) {
                        var bg = (((parseFloat(usulan) * sb) / 100) * 30) / 365;
                        var rc = (bg / keuangan) * 100;

                        $("#rc").val(rc.toFixed(2) + " " + "%");
                    } else if (
                        hasil.metode_rps == "EFEKTIF ANUITAS" ||
                        mtd == "EFEKTIF ANUITAS"
                    ) {
                        var ssb = sb / 100;
                        var bunga = ssb / 12;
                        var anuitas =
                            (usulan * bunga) /
                            (1 - 1 / Math.pow(1 + bunga, jangka_waktu));
                        var rc = (anuitas / keuangan) * 100;
                        $("#rc").val(rc.toFixed(2) + "%");
                    } else {
                        //
                        var bunga =
                            (parseFloat(usulan) * parseFloat(sb)) / 100 / 12;
                        var poko =
                            parseFloat(usulan) / parseFloat(jangka_waktu);
                        var angsuran = Math.ceil(bunga) + poko;
                        var rc = (angsuran / parseFloat(keuangan)) * 100;
                        $("#rc").val(rc.toFixed(2) + " " + "%");
                    }
                });
            },
            error: function (xhr, status, error) {
                // Tindakan jika terjadi kesalahan dalam permintaan AJAX
                console.error("Error:", xhr.responseText);
            },
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
            return prefix == undefined ? rupiah : rupiah ? "Rp. " + rupiah : "";
        }
    });

    $("#usulan_plafon").on("input", function () {
        var value1 = parseFloat($(this).val().replace(/[^\d]/g, ""));
        var value2 = parseFloat($("#max").val().replace(/[^\d]/g, ""));

        // Memeriksa apakah nilai input1 lebih besar dari input2
        if (value1 > value2) {
            // Jika lebih besar, atur nilai input1 menjadi nilai input2
            $(this).val(value2);
        }
    });

    $("#provisi").on("input", function () {
        // Mengambil nilai dari input field
        var inputValue = $(this).val();

        // Mengganti koma (,) menjadi titik (.)
        var convertedValue = inputValue.replace(/,/g, ".");

        // Memasukkan nilai yang sudah diubah ke dalam input field
        $(this).val(convertedValue);
    });

    $("#admin").on("input", function () {
        // Mengambil nilai dari input field
        var inputValue = $(this).val();

        // Mengganti koma (,) menjadi titik (.)
        var convertedValue = inputValue.replace(/,/g, ".");

        // Memasukkan nilai yang sudah diubah ke dalam input field
        $(this).val(convertedValue);
    });
});
