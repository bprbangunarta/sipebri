$(document).ready(function () {
    $("#jangka_waktu, #suku_bunga").keyup(function () {
        var usulan = parseFloat($("#plafon").val().replace(/[^\d]/g, "")) || 0;
        var metode = $("#select-metodes").val();
        var keuangan = $("#keuangan").val();
        var sb = $("#suku_bunga").val();
        var jangka_waktu = $("#jangka_waktu").val();

        if (metode == "FLAT") {
            const rc = flat(usulan, sb, jangka_waktu, keuangan);
            $("#rc").val(rc.toFixed(2) + " " + "%");
        } else if (metode == "EFEKTIF MUSIMAN" || metode == "EFEKTIF") {
            const rc = musiman(usulan, sb, jangka_waktu, keuangan);
            $("#rc").val(rc.toFixed(2) + " " + "%");
        } else if (metode == "EFEKTIF ANUITAS") {
            const rc = anuitas(usulan, sb, jangka_waktu, keuangan);
            $("#rc").val(rc.toFixed(2) + "%");
        } else {
            //
            const rc = flat(usulan, sb, jangka_waktu, keuangan);
            $("#rc").val(rc.toFixed(2) + " " + "%");
        }
    });

    $("#select-metodes").on("change", function () {
        var usulan = parseFloat($("#plafon").val().replace(/[^\d]/g, "")) || 0;
        var keuangan = $("#keuangan").val();
        var sb = $("#suku_bunga").val();
        var jangka_waktu = $("#jangka_waktu").val();
        var selectedValue = $(this).val();

        if (selectedValue == "FLAT") {
            const rc = flat(usulan, sb, jangka_waktu, keuangan);
            $("#rc").val(rc.toFixed(2) + " " + "%");

            $("#jangka_bunga").val("1");
            $("#jangka_bunga").attr("readonly", true);

            $("#jangka_pokok").val("1");
            $("#jangka_pokok").attr("readonly", true);
        } else if (
            selectedValue == "EFEKTIF MUSIMAN" ||
            selectedValue == "EFEKTIF"
        ) {
            const rc = musiman(usulan, sb, jangka_waktu, keuangan);
            $("#rc").val(rc.toFixed(2) + " " + "%");

            $("#jangka_bunga").attr("readonly", false);
            $("#jangka_pokok").attr("readonly", false);
        } else if (selectedValue == "EFEKTIF ANUITAS") {
            const rc = anuitas(usulan, sb, jangka_waktu, keuangan);
            $("#rc").val(rc.toFixed(2) + "%");

            $("#jangka_bunga").attr("readonly", false);
            $("#jangka_pokok").attr("readonly", false);
        } else {
            const rc = flat(usulan, sb, jangka_waktu, keuangan);
            $("#rc").val(rc.toFixed(2) + " " + "%");

            $("#jangka_bunga").attr("readonly", false);
            $("#jangka_pokok").attr("readonly", false);
        }
    });

    function flat(usulan, sb, jangka_waktu, keuangan) {
        var bunga = (parseFloat(usulan) * parseFloat(sb)) / 100 / 12;
        var poko = parseFloat(usulan) / parseFloat(jangka_waktu);
        var angsuran = Math.ceil(bunga) + poko;
        var rc = (angsuran / parseFloat(keuangan)) * 100;

        push(poko, bunga, angsuran);

        return rc;
    }

    function musiman(usulan, sb, jangka_waktu, keuangan) {
        var bg = (((parseFloat(usulan) * sb) / 100) * 30) / 365;
        const p = jangka_waktu / 6;
        const poko = usulan / p;
        var angsuran = Math.ceil(bg) + poko;
        var rc = (bg / keuangan) * 100;

        push(poko, bg, bg);

        return rc;
    }

    function anuitas(usulan, sb, jangka_waktu, keuangan) {
        var ssb = sb / 100;
        var bg = (usulan * sb) / 100 / 12;
        var bunga = ssb / 12;
        var anuitas =
            (usulan * bunga) / (1 - 1 / Math.pow(1 + bunga, jangka_waktu));
        var rc = (anuitas / keuangan) * 100;

        push(anuitas - bg, bg, anuitas);

        return rc;
    }

    function push(poko, bunga, angsuran) {
        $("#angsuran_pokok").val(Math.ceil(poko).toLocaleString("id-ID"));
        $("#angsuran_bunga").val(Math.ceil(bunga).toLocaleString("id-ID"));
        $("#total_angsuran").val(Math.ceil(angsuran).toLocaleString("id-ID"));
    }
});
