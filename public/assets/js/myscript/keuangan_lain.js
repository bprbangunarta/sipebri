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

document.addEventListener("DOMContentLoaded", function () {
    // Menyimpan referensi ke elemen input
    var input1 = document.getElementById("nominal1");
    var input2 = document.getElementById("nominal2");
    var input3 = document.getElementById("nominal3");
    var input4 = document.getElementById("nominal4");

    var input5 = document.getElementById("pengeluaran1");
    var input6 = document.getElementById("pengeluaran2");
    var input7 = document.getElementById("pengeluaran3");
    var input8 = document.getElementById("pengeluaran4");

    // Menyimpan referensi ke elemen total
    var totalpendapatan = document.getElementById("penus");
    var totalbiaya = document.getElementById("biayaop");
    var hasilbersih = document.getElementById("hasilbersih");

    // Event listener ketika input berubah
    input1.addEventListener("input", pendapatan);
    input2.addEventListener("input", pendapatan);
    input3.addEventListener("input", pendapatan);
    input4.addEventListener("input", pendapatan);

    input5.addEventListener("input", semuabiaya);
    input6.addEventListener("input", semuabiaya);
    input7.addEventListener("input", semuabiaya);
    input8.addEventListener("input", semuabiaya);

    function pendapatan() {
        // Mengambil nilai dari input dan menghapus simbol "Rp." serta karakter pemisah ribuan
        var value1 = input1.value.replace("Rp. ", "").replace(/\./g, "");
        var value2 = input2.value.replace("Rp. ", "").replace(/\./g, "");
        var value3 = input3.value.replace("Rp. ", "").replace(/\./g, "");
        var value4 = input4.value.replace("Rp. ", "").replace(/\./g, "");

        // Mengubah nilai ke angka float
        value1 = parseFloat(value1) || 0;
        value2 = parseFloat(value2) || 0;
        value3 = parseFloat(value3) || 0;
        value4 = parseFloat(value4) || 0;

        // Melakukan perhitungan total
        var total = value1 + value2 + value3 + value4;

        // Memperbarui elemen total
        totalpendapatan.value = "Rp. " + total.toLocaleString("id-ID");
    }

    function semuabiaya() {
        // Mengambil nilai dari input dan menghapus simbol "Rp." serta karakter pemisah ribuan
        var value5 = input5.value.replace("Rp. ", "").replace(/\./g, "");
        var value6 = input6.value.replace("Rp. ", "").replace(/\./g, "");
        var value7 = input7.value.replace("Rp. ", "").replace(/\./g, "");
        var value8 = input8.value.replace("Rp. ", "").replace(/\./g, "");

        // Mengubah nilai ke angka float
        value5 = parseFloat(value5) || 0;
        value6 = parseFloat(value6) || 0;
        value7 = parseFloat(value7) || 0;
        value8 = parseFloat(value8) || 0;

        // Melakukan perhitungan total
        var total = value5 + value6 + value7 + value8;

        // Memperbarui elemen total
        totalbiaya.value = "Rp. " + total.toLocaleString("id-ID");
    }
});

// $(document).ready(function () {
//     //Jumlah Nominal
//     var total = 0;
//     $("#nominal1, #nominal2, #nominal3, #nominal4, #nominal5").on(
//         "input",
//         function () {
//             total = 0; // Reset total ke 0 setiap kali salah satu input berubah

//             for (var i = 1; i <= 5; i++) {
//                 var nom = parseFloat(
//                     $("#nominal" + i)
//                         .val()
//                         .replace(/[^\d]/g, "")
//                 );

//                 // var rnom = parseFloat(nom.replace(/\D/g, "")) || 0;
//                 // var rnom = parseFloat(nom.replace(/[Rp.,]/g, "")) || 0;
//                 total += nom;
//                 console.log(nom);
//             }

//             var totalFormatted = "Rp. " + total.toLocaleString("id-ID");
//             $("#penus").val(totalFormatted);

//             var bo = $("#biayaop").val();
//             var pph = $("#pph").val();
//             var rbo =
//                 parseFloat(bo.replace("Rp. ", "").replace(/\./g, "")) || 0;
//             var rpph =
//                 parseFloat(pph.replace("Rp. ", "").replace(/\./g, "")) || 0;
//             var a = total + rpph;
//             var has = a - rbo;
//             var tpen = "Rp. " + has.toLocaleString("id-ID");
//             $("#hasilbersih").val(tpen);
//         }
//     );

//     //Biaya Operasional
//     var tot = 0;
//     $(
//         "#pengeluaran1, #pengeluaran2, #pengeluaran3, #pengeluaran4, #pengeluaran5"
//     ).on("input", function () {
//         tot = 0; // Reset total ke 0 setiap kali salah satu input berubah

//         for (var i = 1; i <= 5; i++) {
//             var nom = $("#pengeluaran" + i).val();
//             var rnom = parseFloat(nom.replace(/[^\d]/g, "")) || 0;
//             tot += rnom;
//         }

//         var a = $("#penus").val();
//         var b = $("#pph").val();
//         var ra = parseFloat(a.replace(/[^\d]/g, "")) || 0;
//         var rb = parseFloat(b.replace(/[^\d]/g, "")) || 0;

//         var tbo = "Rp. " + tot.toLocaleString("id-ID");
//         $("#biayaop").val(tbo);

//         var hasil = ra - tot + rb;
//         var tb = "Rp. " + hasil.toLocaleString("id-ID");
//         $("#hasilbersih").val(tb);
//     });

//     //Proyeksi Penambahan Hasil Usaha
//     $("#pph").on("input", function () {
//         var penus = $("#penus").val();
//         var biaya = $("#biayaop").val();
//         var pph = $("#pph").val();
//         var rpph = parseFloat(pph.replace(/[^\d]/g, "")) || 0;
//         var rpenus = parseFloat(penus.replace(/[^\d]/g, "")) || 0;
//         var rbiaya = parseFloat(biaya.replace(/[^\d]/g, "")) || 0;

//         var hpph = rpenus - rbiaya + rpph;
//         var tb = "Rp. " + hpph.toLocaleString("id-ID");
//         $("#hasilbersih").val(tb);
//     });
// });
