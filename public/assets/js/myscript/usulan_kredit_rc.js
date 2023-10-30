$(document).ready(function () {
    $("#usulan_plafon, #jw").keyup(function () {
        var usulan =
            parseFloat($("#usulan_plafon").val().replace(/[^\d]/g, "")) || 0;
        var metode = $("#metode").val();
        var keuangan = $("#keuangan").val();
        var sb = $("#suku_bunga").val();
        var jangka_wkatu = $("#jw").val();

        if (metode == "FLAT") {
            //
            var bunga = (parseFloat(usulan) * parseFloat(sb)) / 100 / 12;
            var poko = parseFloat(usulan) / parseFloat(jangka_wkatu);
            var angsuran = bunga + poko;
            var rc = (angsuran / parseFloat(keuangan)) * 100;
            $("#rc").val(rc.toFixed(2) + " " + "%");

            //MAX Plafon FLAT
            var max_plafon =
                (angsuran * jangka_wkatu) / (1 + (jangka_wkatu * sb) / 12);
            $("#max").val(max_plafon.toFixed(2));
            //
        } else if (metode == "EFEKTIF MUSIMAN") {
            //
            var pl = (parseFloat(usulan) * 70) / 100;
            var pp = pl / 6;
            var bg = (((parseFloat(usulan) * jangka_wkatu) / 100) * 30) / 365;
            var rc = (pp / bg) * 100;
            $("#rc").val(rc.toFixed(2) + " " + "%");

            //MAX Plafon FLAT
            var max_plafon =
                (keuangan * jangka_wkatu) / (1 + (jangka_wkatu * sb) / 12);
            $("#max").val(max_plafon.toFixed(2));
            //
        } else if (metode == "EFEKTIF ANUITAS") {
            //
            var bunga = (parseFloat(usulan) * parseFloat(sb)) / 100 / 12;
            var poko = parseFloat(usulan) / parseFloat(jangka_wkatu);
            var angsuran = bunga + poko;
            var rc = (angsuran / parseFloat(keuangan)) * 100;
            $("#rc").val(rc.toFixed(2) + " " + "%");

            //MAX Plafon EFKTIF ANUITAS
            var max_plafon =
                angsuran /
                ((sb / 12) * (Math.pow(1 + sb / 12, jangka_wkatu) - 1));
            var mp = max_plafon.toFixed(2);
            $("#max").val(formatRupiah(mp));
            console.log(mp, angsuran);
        }
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
    return prefix == undefined ? rupiah : rupiah ? "Rp. " + rupiah : "";
}
