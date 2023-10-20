$(document).ready(function () {
    $("#usulan_plafon, #jw").keyup(function () {
        var usulan =
            parseFloat($("#usulan_plafon").val().replace(/[^\d]/g, "")) || 0;
        var metode = $("#metode").val();
        var keuangan = $("#keuangan").val();
        var sb = $("#suku_bunga").val();
        var jangka_wkatu = $("#jw").val();

        if (metode == "Flat") {
            var bunga = (parseFloat(usulan) * parseFloat(sb)) / 100 / 12;
            var poko = parseFloat(usulan) / parseFloat(jangka_wkatu);
            var angsuran = bunga + poko;
            var rc = (angsuran / parseFloat(keuangan)) * 100;
            $("#rc").val(rc.toFixed(2));
            //
        } else if (metode == "Efektif Musiman") {
            //
            var pl = (parseFloat(usulan) * 70) / 100;
            var pp = pl / 6;
            var bg = (((parseFloat(usulan) * jangka_wkatu) / 100) * 30) / 365;
            var rc = (pp / bg) * 100;
            $("#rc").val(rc.toFixed(2));
        }
    });
});
