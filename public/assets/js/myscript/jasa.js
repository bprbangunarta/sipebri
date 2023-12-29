var pendapatan = document.getElementById("pendapatan");
var pajak = document.getElementById("pajak");
var lainnya = document.getElementById("lainnya");
var tpenghasilan = document.getElementById("tpenghasilan");

if (pendapatan) {
    pendapatan.addEventListener("keyup", function (e) {
        pendapatan.value = formatRupiah(this.value, "Rp. ");
    });
}
if (pajak) {
    pajak.addEventListener("keyup", function (e) {
        pajak.value = formatRupiah(this.value, "Rp. ");
    });
}
if (lainnya) {
    lainnya.addEventListener("keyup", function (e) {
        lainnya.value = formatRupiah(this.value, "Rp. ");
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

$("#pajak, #lainnya").keyup(function () {
    var pajak = parseFloat($("#pajak").val().replace(/[^\d]/g, "")) || 0;
    var lainnya = parseFloat($("#lainnya").val().replace(/[^\d]/g, "")) || 0;
    var penghasilan =
        parseFloat($("#tpenghasilan").val().replace(/[^\d]/g, "")) || 0;

    var penlu = pajak + lainnya;
    var has = penghasilan - penlu;

    var hs = "Rp. " + penlu.toLocaleString("id-ID");
    var hasil = "Rp. " + has.toLocaleString("id-ID");

    $("#tpengeluaran").val(hs);
    $("#laba").val(hasil);
});

$("#pendapatan").keyup(function () {
    var pen = $("#pendapatan").val();
    var rpen = parseFloat(pen.replace(/[^\d]/g, "")) || 0;

    var pengeluaran = $("#tpengeluaran").val();
    var rpengeluaran = parseFloat(pengeluaran.replace(/[^\d]/g, "")) || 0;

    var jml = parseFloat(rpen) - parseFloat(rpengeluaran);

    // var hsl = rbulan - parseFloat(rdgn) + rpn;
    var hasil = "Rp. " + jml.toLocaleString("id-ID");
    var rp = "Rp. " + rpen.toLocaleString("id-ID");
    $("#tpenghasilan").val(rp);
    $("#laba").val(hasil);
});
