$(document).ready(function () {
    $("#usulan_plafon, #jw").keyup(function () {
        var usulan =
            parseFloat($("#usulan_plafon").val().replace(/[^\d]/g, "")) || 0;
        var metode = $("#metode").val();
        var keuangan = $("#keuangan").val();
        var sb = $("#suku_bunga").val();
        var jangka_waktu = $("#jw").val();

        if (metode == "FLAT") {
            //
            var bunga = (parseFloat(usulan) * parseFloat(sb)) / 100 / 12;
            var poko = parseFloat(usulan) / parseFloat(jangka_waktu);
            var angsuran = Math.ceil(bunga) + poko;
            var rc = (angsuran / parseFloat(keuangan)) * 100;
            $("#rc").val(rc.toFixed(2) + " " + "%");

            //MAX Plafon FLAT
            var mx_sb = sb / 100;
            var max_plafon =
                (angsuran * jangka_waktu) / (1 + (jangka_waktu * mx_sb) / 12);
            var mx = formatRupiah(max_plafon.toFixed(0));
            $("#max").val("Rp. " + " " + mx);

            //
        } else if (metode == "EFEKTIF MUSIMAN") {
            //
            var pl = (parseFloat(usulan) * 70) / 100;
            var pp = pl / 6;
            var bg = (((parseFloat(usulan) * jangka_waktu) / 100) * 30) / 365;
            var rc = (pp / bg) * 100;
            $("#rc").val(rc.toFixed(2) + " " + "%");

            //MAX Plafon FLAT
            var max_plafon =
                (keuangan * jangka_waktu) / (1 + (jangka_waktu * sb) / 12);
            $("#max").val(max_plafon.toFixed(2));
            //
        } else if (metode == "EFEKTIF ANUITAS") {
            var ssb = sb / 100;
            var bunga = ssb / 12;
            var anuitas =
                (usulan * bunga) / (1 - 1 / Math.pow(1 + bunga, jangka_waktu));
            var rc = (anuitas / keuangan) * 100;
            $("#rc").val(rc.toFixed(2) + "%");

            //MAX Plafon EFKTIF ANUITAS
            var plafonPinjaman =
                anuitas /
                ((ssb / 12) *
                    (Math.pow(1 + ssb / 12, parseInt(jangka_waktu)) /
                        (Math.pow(1 + ssb / 12, parseInt(jangka_waktu)) - 1)));

            $("#max").val("Rp." + " " + plafonPinjaman.toLocaleString("id-ID"));
        } else {
            //
            var bunga = (parseFloat(usulan) * parseFloat(sb)) / 100 / 12;
            var poko = parseFloat(usulan) / parseFloat(jangka_waktu);
            var angsuran = bunga + poko;
            var rc = (angsuran / parseFloat(keuangan)) * 100;
            $("#rc").val(rc.toFixed(2) + " " + "%");

            //MAX Plafon FLAT
            var max_plafon =
                (angsuran * jangka_waktu) / (1 + (jangka_waktu * sb) / 12);
            $("#max").val("Rp." + " " + max_plafon.toFixed(2));
            //
        }
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
