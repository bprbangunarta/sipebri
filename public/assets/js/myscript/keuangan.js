var konsumsi = document.getElementById("konsumsi");
var kesehatan = document.getElementById("kesehatan");
var pendidikan = document.getElementById("pendidikan");
var gatel = document.getElementById("gatel");
var jajan = document.getElementById("jajan");
var sumbangan = document.getElementById("sumbangan");
var roko = document.getElementById("roko");
var kewajiban1 = document.getElementById("kewajiban1");
var kewajiban2 = document.getElementById("kewajiban2");
var kewajiban3 = document.getElementById("kewajiban3");

if (konsumsi) {
    konsumsi.addEventListener("keyup", function (e) {
        konsumsi.value = formatRupiah(this.value, "Rp. ");
    });
}
if (kesehatan) {
    kesehatan.addEventListener("keyup", function (e) {
        kesehatan.value = formatRupiah(this.value, "Rp. ");
    });
}
if (pendidikan) {
    pendidikan.addEventListener("keyup", function (e) {
        pendidikan.value = formatRupiah(this.value, "Rp. ");
    });
}
if (gatel) {
    gatel.addEventListener("keyup", function (e) {
        gatel.value = formatRupiah(this.value, "Rp. ");
    });
}
if (jajan) {
    jajan.addEventListener("keyup", function (e) {
        jajan.value = formatRupiah(this.value, "Rp. ");
    });
}
if (sumbangan) {
    sumbangan.addEventListener("keyup", function (e) {
        sumbangan.value = formatRupiah(this.value, "Rp. ");
    });
}
if (roko) {
    roko.addEventListener("keyup", function (e) {
        roko.value = formatRupiah(this.value, "Rp. ");
    });
}
if (kewajiban1) {
    kewajiban1.addEventListener("keyup", function (e) {
        kewajiban1.value = formatRupiah(this.value, "Rp. ");
    });
}
if (kewajiban2) {
    kewajiban2.addEventListener("keyup", function (e) {
        kewajiban2.value = formatRupiah(this.value, "Rp. ");
    });
}
if (kewajiban3) {
    kewajiban3.addEventListener("keyup", function (e) {
        kewajiban3.value = formatRupiah(this.value, "Rp. ");
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

$(
    "#konsumsi, #kesehatan, #pendidikan, #gatel, #jajan, #sumbangan, #roko"
).keyup(function () {
    var konsumsi = $("#konsumsi").val();
    var kesehatan = $("#kesehatan").val();
    var pendidikan = $("#pendidikan").val();
    var gatel = $("#gatel").val();
    var jajan = $("#jajan").val();
    var sumbangan = $("#sumbangan").val();
    var roko = $("#roko").val();
    var pendapatan = $("#pendapatan").val();
    var kewajiban = $("#kewajiban_lain").val();

    var rkonsumsi = parseFloat(konsumsi.replace(/[^\d]/g, "")) || 0;
    var rkesehatan = parseFloat(kesehatan.replace(/[^\d]/g, "")) || 0;
    var rpendidikan = parseFloat(pendidikan.replace(/[^\d]/g, "")) || 0;
    var rgatel = parseFloat(gatel.replace(/[^\d]/g, "")) || 0;
    var rjajan = parseFloat(jajan.replace(/[^\d]/g, "")) || 0;
    var rsumbangan = parseFloat(sumbangan.replace(/[^\d]/g, "")) || 0;
    var rroko = parseFloat(roko.replace(/[^\d]/g, "")) || 0;
    var rpendapatan = parseFloat(pendapatan.replace(/[^\d]/g, "")) || 0;
    var rkewajiban = parseFloat(kewajiban.replace(/[^\d]/g, "")) || 0;

    var jml =
        rkonsumsi +
        rkesehatan +
        rpendidikan +
        rgatel +
        rjajan +
        rsumbangan +
        rroko;

    var bjml = "Rp. " + jml.toLocaleString("id-ID");
    $("#biaya").val(bjml);

    var a = jml + rkewajiban;
    var has = rpendapatan - a;

    var bs = "Rp. " + has.toLocaleString("id-ID");
    $("#hasilbersih").val(bs);
});

var ts = 0;
$("#kewajiban1, #kewajiban2, #kewajiban3").keyup(function () {
    ts = 0; // Reset total ke 0 setiap kali salah satu input berubah
    var pendapatan = $("#pendapatan").val();
    var biaya = $("#biaya").val();

    var rpendapatan = parseFloat(pendapatan.replace(/[^\d]/g, "")) || 0;
    var rbiaya = parseFloat(biaya.replace(/[^\d]/g, "")) || 0;

    for (var i = 1; i <= 3; i++) {
        var stock = $("#kewajiban" + i).val();
        var hr = parseFloat(stock.replace(/[^\d]/g, "")) || 0;
        ts += hr;
    }

    var a = ts + rbiaya;
    var has = rpendapatan - a;

    var bs = "Rp. " + ts.toLocaleString("id-ID");
    $("#kewajiban_lain").val(bs);

    var hasil = "Rp. " + has.toLocaleString("id-ID");
    $("#hasilbersih").val(hasil);
});
