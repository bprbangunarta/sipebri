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
    var inputs = document.getElementById("nominal5");

    var input5 = document.getElementById("pengeluaran1");
    var input6 = document.getElementById("pengeluaran2");
    var input7 = document.getElementById("pengeluaran3");
    var input8 = document.getElementById("pengeluaran4");
    var input9 = document.getElementById("pengeluaran5");

    var pro = document.getElementById("pph");

    // Menyimpan referensi ke elemen total
    var totalpendapatan = document.getElementById("penus");
    var totalbiaya = document.getElementById("biayaop");
    var hasilbersih = document.getElementById("hasilbersih");
    var biaya_bahan = document.getElementById("bahan_baku");

    // Event listener ketika input berubah
    input1.addEventListener("input", pendapatan);
    input2.addEventListener("input", pendapatan);
    input3.addEventListener("input", pendapatan);
    input4.addEventListener("input", pendapatan);
    inputs.addEventListener("input", pendapatan);

    input5.addEventListener("input", semuabiaya);
    input6.addEventListener("input", semuabiaya);
    input7.addEventListener("input", semuabiaya);
    input8.addEventListener("input", semuabiaya);
    input9.addEventListener("input", semuabiaya);

    pro.addEventListener("input", proyeksi);

    function pendapatan() {
        // Mengambil nilai dari input dan menghapus simbol "Rp." serta karakter pemisah ribuan
        var value1 = input1.value.replace("Rp. ", "").replace(/\./g, "");
        var value2 = input2.value.replace("Rp. ", "").replace(/\./g, "");
        var value3 = input3.value.replace("Rp. ", "").replace(/\./g, "");
        var value4 = input4.value.replace("Rp. ", "").replace(/\./g, "");
        var values = inputs.value.replace("Rp. ", "").replace(/\./g, "");
        var tb = totalbiaya.value.replace("Rp. ", "").replace(/\./g, "");
        var bb = biaya_bahan.value.replace("Rp. ", "").replace(/\./g, "");
        var tpro = pro.value.replace("Rp. ", "").replace(/\./g, "");

        // Mengubah nilai ke angka float
        value1 = parseFloat(value1) || 0;
        value2 = parseFloat(value2) || 0;
        value3 = parseFloat(value3) || 0;
        value4 = parseFloat(value4) || 0;
        values = parseFloat(values) || 0;
        tb = parseFloat(tb) || 0;
        tpro = parseFloat(tpro) || 0;
        bb = parseFloat(bb) || 0;

        // Melakukan perhitungan total
        var total = value1 + value2 + value3 + value4 + values;
        var hs = (total + tpro) - (tb + bb);
        // Memperbarui elemen total
        totalpendapatan.value = "Rp. " + total.toLocaleString("id-ID");
        hasilbersih.value = "Rp. " + hs.toLocaleString("id-ID");
    }

    function semuabiaya() {
        // Mengambil nilai dari input dan menghapus simbol "Rp." serta karakter pemisah ribuan
        var value5 = input5.value.replace("Rp. ", "").replace(/\./g, "");
        var value6 = input6.value.replace("Rp. ", "").replace(/\./g, "");
        var value7 = input7.value.replace("Rp. ", "").replace(/\./g, "");
        var value8 = input8.value.replace("Rp. ", "").replace(/\./g, "");
        var value9 = input9.value.replace("Rp. ", "").replace(/\./g, "");
        var tp = totalpendapatan.value.replace("Rp. ", "").replace(/\./g, "");
        var bb = biaya_bahan.value.replace("Rp. ", "").replace(/\./g, "");
        var tpro = pro.value.replace("Rp. ", "").replace(/\./g, "");

        // Mengubah nilai ke angka float
        value5 = parseFloat(value5) || 0;
        value6 = parseFloat(value6) || 0;
        value7 = parseFloat(value7) || 0;
        value8 = parseFloat(value8) || 0;
        value9 = parseFloat(value9) || 0;
        tp = parseFloat(tp) || 0;
        tpro = parseFloat(tpro) || 0;
        bb = parseFloat(bb) || 0;

        // Melakukan perhitungan total
        var total = value5 + value6 + value7 + value8 + value9;
        var hs = (tp + tpro) - (total + bb);

        // Memperbarui elemen total
        totalbiaya.value = "Rp. " + total.toLocaleString("id-ID");
        hasilbersih.value = "Rp. " + hs.toLocaleString("id-ID");
    }

    function proyeksi() {
        // Mengambil nilai dari input dan menghapus simbol "Rp." serta karakter pemisah ribuan
        var rpph = pro.value.replace("Rp. ", "").replace(/\./g, "");
        var tb = totalbiaya.value.replace("Rp. ", "").replace(/\./g, "");
        var tp = totalpendapatan.value.replace("Rp. ", "").replace(/\./g, "");
        var bb = biaya_bahan.value.replace("Rp. ", "").replace(/\./g, "");

        // Mengubah nilai ke angka float
        rpph = parseFloat(rpph) || 0;
        tb = parseFloat(tb) || 0;
        tp = parseFloat(tp) || 0;
        bb = parseFloat(bb) || 0;

        // Melakukan perhitungan total
        var hs = (rpph + tp) - (tb + bb);

        // Memperbarui elemen total
        hasilbersih.value = "Rp. " + hs.toLocaleString("id-ID");
    }
});
