$(document).ready(function () {
    $("#usulan_plafon, #jw").keyup(function () {
        var usulan =
            parseFloat($("#usulan_plafon").val().replace(/[^\d]/g, "")) || 0;
        var metode = $("#metode").val();
        var keuangan = $("#keuangan").val();
        var sb = $("#suku_bunga").val();
        var jangka_waktu = $("#jw").val();
        var mx = $("#max").val();

        if (metode == "FLAT") {
            //
            var bunga = (parseFloat(usulan) * parseFloat(sb)) / 100 / 12;
            var poko = parseFloat(usulan) / parseFloat(jangka_waktu);
            var angsuran = bunga + poko;
            var rc = (angsuran / parseFloat(keuangan)) * 100;
            $("#rc").val(rc.toFixed(2) + " " + "%");

            //MAX Plafon FLAT
            var max_plafon =
                (angsuran * jangka_waktu) / (1 + (jangka_waktu * sb) / 12);
            $("#max").val(max_plafon.toFixed(2));
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
            //
            var bunga = sb / 12;
            var anuitas =
                (usulan * bunga) / (1 - 1 / Math.pow(1 + bunga, jangka_waktu));
            var rc = (anuitas / keuangan) * 100;
            $("#rc").val(rc.toFixed(2) + "%");

            //MAX Plafon EFKTIF ANUITAS
            var plafonPinjaman =
                anuitas /
                ((sb / 12) *
                    (Math.pow(1 + sb / 12, parseInt(jangka_waktu)) /
                        (Math.pow(1 + sb / 12, parseInt(jangka_waktu)) - 1)));

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
            $("#max").val(max_plafon.toFixed(2));
            //
        }
    });
});
