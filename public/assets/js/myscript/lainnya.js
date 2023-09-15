//Nominal RP
var nominal1 = document.getElementById("nominal1");
var nominal2 = document.getElementById("nominal2");
var nominal3 = document.getElementById("nominal3");
var nominal4 = document.getElementById("nominal4");
var nominal5 = document.getElementById("nominal5");

if (nominal1) {
    nominal1.addEventListener("keyup", function (e) {
        nominal1.value = formatRupiah(this.value, "Rp. ");
    });
}
if (nominal2) {
    nominal2.addEventListener("keyup", function (e) {
        nominal2.value = formatRupiah(this.value, "Rp. ");
    });
}
if (nominal3) {
    nominal3.addEventListener("keyup", function (e) {
        nominal3.value = formatRupiah(this.value, "Rp. ");
    });
}
if (nominal4) {
    nominal4.addEventListener("keyup", function (e) {
        nominal4.value = formatRupiah(this.value, "Rp. ");
    });
}
if (nominal5) {
    nominal5.addEventListener("keyup", function (e) {
        nominal5.value = formatRupiah(this.value, "Rp. ");
    });
}

//Nominal Pengeluaran RP
var pengeluaran1 = document.getElementById("pengeluaran1");
var pengeluaran2 = document.getElementById("pengeluaran2");
var pengeluaran3 = document.getElementById("pengeluaran3");
var pengeluaran4 = document.getElementById("pengeluaran4");
var pengeluaran5 = document.getElementById("pengeluaran5");
var pph = document.getElementById("pph");

if (pph) {
    pph.addEventListener("keyup", function (e) {
        pph.value = formatRupiah(this.value, "Rp. ");
    });
}
if (pengeluaran1) {
    pengeluaran1.addEventListener("keyup", function (e) {
        pengeluaran1.value = formatRupiah(this.value, "Rp. ");
    });
}
if (pengeluaran2) {
    pengeluaran2.addEventListener("keyup", function (e) {
        pengeluaran2.value = formatRupiah(this.value, "Rp. ");
    });
}
if (pengeluaran3) {
    pengeluaran3.addEventListener("keyup", function (e) {
        pengeluaran3.value = formatRupiah(this.value, "Rp. ");
    });
}
if (pengeluaran4) {
    pengeluaran4.addEventListener("keyup", function (e) {
        pengeluaran4.value = formatRupiah(this.value, "Rp. ");
    });
}
if (pengeluaran5) {
    pengeluaran5.addEventListener("keyup", function (e) {
        pengeluaran5.value = formatRupiah(this.value, "Rp. ");
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
    //Jumlah Nominal
    var total = 0;
    $("#nominal1, #nominal2, #nominal3, #nominal4, #nominal5").on(
        "input",
        function () {
            total = 0; // Reset total ke 0 setiap kali salah satu input berubah

            for (var i = 1; i <= 5; i++) {
                var nom = $("#nominal" + i).val();

                var rnom =
                    parseFloat(nom.replace("Rp. ", "").replace(/\./g, "")) || 0;

                total += rnom;
            }

            var totalFormatted = "Rp. " + total.toLocaleString("id-ID");
            var totalpen = "Rp. " + total.toLocaleString("id-ID");
            $("#penus").val(totalFormatted);

            var bo = $("#biayaop").val();
            var pph = $("#pph").val();
            var rbo =
                parseFloat(bo.replace("Rp. ", "").replace(/\./g, "")) || 0;
            var rpph =
                parseFloat(pph.replace("Rp. ", "").replace(/\./g, "")) || 0;
            var a = total + rpph;
            var has = a - rbo;
            var tpen = "Rp. " + has.toLocaleString("id-ID");
            $("#hasilbersih").val(tpen);
        }
    );

    //Biaya Operasional
    var tot = 0;
    $(
        "#pengeluaran1, #pengeluaran2, #pengeluaran3, #pengeluaran4, #pengeluaran5"
    ).on("input", function () {
        tot = 0; // Reset total ke 0 setiap kali salah satu input berubah

        for (var i = 1; i <= 5; i++) {
            var nom = $("#pengeluaran" + i).val();
            var rnom = parseFloat(nom.replace(/[^\d]/g, "")) || 0;
            tot += rnom;
        }

        var a = $("#penus").val();
        var b = $("#pph").val();
        var ra = parseFloat(a.replace(/[^\d]/g, "")) || 0;
        var rb = parseFloat(b.replace(/[^\d]/g, "")) || 0;

        var tbo = "Rp. " + tot.toLocaleString("id-ID");
        $("#biayaop").val(tbo);

        var hasil = ra - tot + rb;
        var tb = "Rp. " + hasil.toLocaleString("id-ID");
        $("#hasilbersih").val(tb);
    });

    //Proyeksi Penambahan Hasil Usaha
    $("#pph").on("input", function () {
        var penus = $("#penus").val();
        var biaya = $("#biayaop").val();
        var pph = $("#pph").val();
        var rpph = parseFloat(pph.replace(/[^\d]/g, "")) || 0;
        var rpenus = parseFloat(penus.replace(/[^\d]/g, "")) || 0;
        var rbiaya = parseFloat(biaya.replace(/[^\d]/g, "")) || 0;

        var hpph = rpenus - rbiaya + rpph;
        var tb = "Rp. " + hpph.toLocaleString("id-ID");
        $("#hasilbersih").val(tb);
    });
});
