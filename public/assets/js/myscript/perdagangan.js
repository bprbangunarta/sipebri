//Harga Jual RP
var hrg1 = document.getElementById("hrg1");
var hrg2 = document.getElementById("hrg2");
var hrg3 = document.getElementById("hrg3");
var hrg4 = document.getElementById("hrg4");
var hrg5 = document.getElementById("hrg5");
var hrg6 = document.getElementById("hrg6");
var hrg7 = document.getElementById("hrg7");
var hrg8 = document.getElementById("hrg8");
var hrg9 = document.getElementById("hrg9");
var hrg10 = document.getElementById("hrg10");

if (hrg1) {
    hrg1.addEventListener("keyup", function (e) {
        hrg1.value = formatRupiah(this.value, "Rp. ");
    });
}
if (hrg2) {
    hrg2.addEventListener("keyup", function (e) {
        hrg2.value = formatRupiah(this.value, "Rp. ");
    });
}
if (hrg3) {
    hrg3.addEventListener("keyup", function (e) {
        hrg3.value = formatRupiah(this.value, "Rp. ");
    });
}
if (hrg4) {
    hrg4.addEventListener("keyup", function (e) {
        hrg4.value = formatRupiah(this.value, "Rp. ");
    });
}
if (hrg5) {
    hrg5.addEventListener("keyup", function (e) {
        hrg5.value = formatRupiah(this.value, "Rp. ");
    });
}
if (hrg6) {
    hrg6.addEventListener("keyup", function (e) {
        hrg6.value = formatRupiah(this.value, "Rp. ");
    });
}
if (hrg7) {
    hrg7.addEventListener("keyup", function (e) {
        hrg7.value = formatRupiah(this.value, "Rp. ");
    });
}
if (hrg8) {
    hrg8.addEventListener("keyup", function (e) {
        hrg8.value = formatRupiah(this.value, "Rp. ");
    });
}
if (hrg9) {
    hrg9.addEventListener("keyup", function (e) {
        hrg9.value = formatRupiah(this.value, "Rp. ");
    });
}
if (hrg10) {
    hrg10.addEventListener("keyup", function (e) {
        hrg10.value = formatRupiah(this.value, "Rp. ");
    });
}

//Harga Jual RP
var jual1 = document.getElementById("jual1");
var jual2 = document.getElementById("jual2");
var jual3 = document.getElementById("jual3");
var jual4 = document.getElementById("jual4");
var jual5 = document.getElementById("jual5");
var jual6 = document.getElementById("jual6");
var jual7 = document.getElementById("jual7");
var jual8 = document.getElementById("jual8");
var jual9 = document.getElementById("jual9");
var jual10 = document.getElementById("jual10");

if (jual1) {
    jual1.addEventListener("keyup", function (e) {
        jual1.value = formatRupiah(this.value, "Rp. ");
    });
}
if (jual2) {
    jual2.addEventListener("keyup", function (e) {
        jual2.value = formatRupiah(this.value, "Rp. ");
    });
}
if (jual3) {
    jual3.addEventListener("keyup", function (e) {
        jual3.value = formatRupiah(this.value, "Rp. ");
    });
}
if (jual4) {
    jual4.addEventListener("keyup", function (e) {
        jual4.value = formatRupiah(this.value, "Rp. ");
    });
}
if (jual5) {
    jual5.addEventListener("keyup", function (e) {
        jual5.value = formatRupiah(this.value, "Rp. ");
    });
}
if (jual6) {
    jual6.addEventListener("keyup", function (e) {
        jual6.value = formatRupiah(this.value, "Rp. ");
    });
}
if (jual7) {
    jual7.addEventListener("keyup", function (e) {
        jual7.value = formatRupiah(this.value, "Rp. ");
    });
}
if (jual8) {
    jual8.addEventListener("keyup", function (e) {
        jual8.value = formatRupiah(this.value, "Rp. ");
    });
}
if (jual9) {
    jual9.addEventListener("keyup", function (e) {
        jual9.value = formatRupiah(this.value, "Rp. ");
    });
}
if (jual10) {
    jual10.addEventListener("keyup", function (e) {
        jual10.value = formatRupiah(this.value, "Rp. ");
    });
}

var br = document.getElementById("brdg");
var transport = document.getElementById("transport");
var bongkar = document.getElementById("bongkar");
var pegawai = document.getElementById("pegawai");
var gatel = document.getElementById("gatel");
var retri = document.getElementById("retri");
var sewa = document.getElementById("sewa");
var penambahan = document.getElementById("penambahan");

if (br) {
    br.addEventListener("keyup", function (e) {
        br.value = formatRupiah(this.value, "Rp. ");
    });
}
if (transport) {
    transport.addEventListener("keyup", function (e) {
        transport.value = formatRupiah(this.value, "Rp. ");
    });
}
if (bongkar) {
    bongkar.addEventListener("keyup", function (e) {
        bongkar.value = formatRupiah(this.value, "Rp. ");
    });
}
if (pegawai) {
    pegawai.addEventListener("keyup", function (e) {
        pegawai.value = formatRupiah(this.value, "Rp. ");
    });
}
if (gatel) {
    gatel.addEventListener("keyup", function (e) {
        gatel.value = formatRupiah(this.value, "Rp. ");
    });
}
if (retri) {
    retri.addEventListener("keyup", function (e) {
        retri.value = formatRupiah(this.value, "Rp. ");
    });
}
if (sewa) {
    sewa.addEventListener("keyup", function (e) {
        sewa.value = formatRupiah(this.value, "Rp. ");
    });
}
if (penambahan) {
    penambahan.addEventListener("keyup", function (e) {
        penambahan.value = formatRupiah(this.value, "Rp. ");
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

//Hitung persentase
$("#jual1, #hrg1, #laba1, #persen1").on("input", function () {
    var jual = $("#jual1").val();
    var hrg = $("#hrg1").val();
    var laba = "#laba1";
    var persen = "#persen1";
    ubahRP(hrg, jual, laba, persen);
});
$("#jual2, #hrg2, #laba2, #persen2").on("input", function () {
    var jual = $("#jual2").val();
    var hrg = $("#hrg2").val();
    var laba = "#laba2";
    var persen = "#persen2";
    ubahRP(hrg, jual, laba, persen);
});
$("#jual3, #hrg3, #laba3, #persen3").on("input", function () {
    var jual = $("#jual3").val();
    var hrg = $("#hrg3").val();
    var laba = "#laba3";
    var persen = "#persen3";
    ubahRP(hrg, jual, laba, persen);
});
$("#jual4, #hrg4, #laba4, #persen4").on("input", function () {
    var jual = $("#jual4").val();
    var hrg = $("#hrg4").val();
    var laba = "#laba4";
    var persen = "#persen4";
    ubahRP(hrg, jual, laba, persen);
});
$("#jual5, #hrg5, #laba5, #persen5").on("input", function () {
    var jual = $("#jual5").val();
    var hrg = $("#hrg5").val();
    var laba = "#laba5";
    var persen = "#persen5";
    ubahRP(hrg, jual, laba, persen);
});
$("#jual6, #hrg6, #laba6, #persen6").on("input", function () {
    var jual = $("#jual6").val();
    var hrg = $("#hrg6").val();
    var laba = "#laba6";
    var persen = "#persen6";
    ubahRP(hrg, jual, laba, persen);
});
$("#jual7, #hrg7, #laba7, #persen7").on("input", function () {
    var jual = $("#jual7").val();
    var hrg = $("#hrg7").val();
    var laba = "#laba7";
    var persen = "#persen7";
    ubahRP(hrg, jual, laba, persen);
});
$("#jual8, #hrg8, #laba8, #persen8").on("input", function () {
    var jual = $("#jual8").val();
    var hrg = $("#hrg8").val();
    var laba = "#laba8";
    var persen = "#persen8";
    ubahRP(hrg, jual, laba, persen);
});
$("#jual9, #hrg9, #laba9, #persen9").on("input", function () {
    var jual = $("#jual9").val();
    var hrg = $("#hrg9").val();
    var laba = "#laba9";
    var persen = "#persen9";
    ubahRP(hrg, jual, laba, persen);
});
$("#jual10, #hrg10, #laba10, #persen10").on("input", function () {
    var jual = $("#jual10").val();
    var hrg = $("#hrg10").val();
    var laba = "#laba10";
    var persen = "#persen10";
    ubahRP(hrg, jual, laba, persen);
});

function ubahRP(hrg, jual, laba, persen) {
    var jl = parseFloat(jual.replace(/[^\d]/g, ""));
    var hr = parseFloat(hrg.replace(/[^\d]/g, ""));
    hitungPersentase(hr, jl, laba, persen);
}

function hitungPersentase(hrg, jual, laba, pers) {
    var hrg1 = hrg;
    var jual1 = jual;
    var persen = ((jual1 - hrg1) / hrg1) * 100;
    var tlaba = jual - hrg;
    var tl = "Rp. " + tlaba.toLocaleString("id-ID");

    // Menampilkan hasil perhitungan di input "persen"
    $(laba).val(tl);
    $(pers).val(persen.toFixed(2) + "%");
}

//Total BEli
var total = 0;
$(
    "#hrg1, #hrg2, #hrg3, #hrg4, #hrg5, #hrg6, #hrg7, #hrg8, #hrg9, #hrg10"
).keyup(function () {
    total = 0; // Reset total ke 0 setiap kali salah satu input berubah

    for (var i = 1; i <= 10; i++) {
        var hrg = $("#hrg" + i).val();
        var hr = parseFloat(hrg.replace("Rp. ", "").replace(/\./g, "")) || 0;
        total += hr;
    }
    var totalFormatted = "Rp. " + total.toLocaleString("id-ID");
    $("#tbeli").val(totalFormatted);
});

//Total Jual
var tj = 0;
var tl = 0;
var pers = 0;
$(
    "#jual1, #jual2, #jual3, #jual4, #jual5, #jual6, #jual7, #jual8, #jual9, #jual10"
).keyup(function () {
    tj = 0; // Reset total ke 0 setiap kali salah satu input berubah
    tl = 0;
    pers = 0;

    for (var i = 1; i <= 10; i++) {
        var jual = $("#jual" + i).val();
        var jl = parseFloat(jual.replace(/[^\d]/g, "")) || 0;
        tj += jl;
    }
    var totaljual = "Rp. " + tj.toLocaleString("id-ID");
    $("#tjual").val(totaljual);

    //Total Laba
    for (var j = 1; j <= 10; j++) {
        var laba = $("#laba" + j).val();

        var lb = parseFloat(laba.replace("Rp. ", "").replace(/\./g, "")) || 0;
        tl += lb;
    }

    var totallaba = "Rp. " + tl.toLocaleString("id-ID");
    $("#tlaba").val(totallaba);

    //Total persen
    // for (var q = 1; q <= 10; q++) {
    //     var ps = $("#persen" + q).val();

    //     // var pr = parseFloat(ps.replace("Rp. ", "").replace(/\./g, "")) || 0;
    //     var angka = parseFloat(ps.replace("%", ""));
    //     pers += angka || 0;
    // }

    var has = pers / 10;
    $("#tpersen").val(has.toFixed(2) + "%");
});

//Total Stock
var ts = 0;
$(
    "#stock1, #stock2, #stock3, #stock4, #stock5, #stock6, #stock7, #stock8, #stock9, #stock10"
).keyup(function () {
    ts = 0; // Reset total ke 0 setiap kali salah satu input berubah

    for (var i = 1; i <= 10; i++) {
        var stock = $("#stock" + i).val();
        var hr = parseFloat(stock.replace(/[^\d]/g, "")) || 0;
        ts += hr;
    }
    var totalFormatted = ts;
    $("#tstock").val(totalFormatted);
});

//Informasi Perdagangan
$("#brdg").keyup(function () {
    var br = $("#brdg").val();
    var replace = parseFloat(br.replace(/[^\d]/g, "")) || 0;

    var pers = $("#tpersen").val();
    var persen = parseFloat(pers.replace("%", "").replace(/\.00/g, ""));

    //pendapatan harian
    var hpen = (replace * 30 + replace * 30 * (persen / 100)) / 30;
    var tpenhar = "Rp. " + hpen.toLocaleString("id-ID");
    $("#penhar").val(tpenhar);

    //Pokok Penjualan
    var rpenhar = hpen;
    var tpopen = rpenhar / (1 + persen / 100);
    var hpopen = "Rp. " + tpopen.toLocaleString("id-ID");
    $("#popen").val(hpopen);

    //Laba Harian
    var lh = hpen - tpopen;
    var hlaba = "Rp. " + lh.toLocaleString("id-ID");
    $("#lahar").val(hlaba);

    //Laba Bulanan
    var lbulan = lh * 30;
    var hlaba = "Rp. " + lbulan.toLocaleString("id-ID");
    $("#lbulan").val(hlaba);

    //Hasil bersih
    var dg = $("#bdagang").val();
    var pnb = $("#penambahan").val();
    var rdg = parseFloat(dg.replace(/[^\d]/g, "")) || 0;
    var rpnb = parseFloat(pnb.replace(/[^\d]/g, "")) || 0;
    var hs = lbulan - rdg + rpnb;
    var bhasil = "Rp. " + hs.toLocaleString("id-ID");
    $("#hasilbersih").val(bhasil);
});

//Biaya Perdagangan
var bp = 0;
$("#transport, #bongkar, #pegawai, #gatel, #retri, #sewa").keyup(function () {
    var transport = $("#transport").val();
    var bongkar = $("#bongkar").val();
    var pegawai = $("#pegawai").val();
    var gatel = $("#gatel").val();
    var retri = $("#retri").val();
    var sewa = $("#sewa").val();
    var rtransport = parseFloat(transport.replace(/[^\d]/g, "")) || 0;
    var rbongkar = parseFloat(bongkar.replace(/[^\d]/g, "")) || 0;
    var rpegawai = parseFloat(pegawai.replace(/[^\d]/g, "")) || 0;
    var rgatel = parseFloat(gatel.replace(/[^\d]/g, "")) || 0;
    var rretri = parseFloat(retri.replace(/[^\d]/g, "")) || 0;
    var rsewa = parseFloat(sewa.replace(/[^\d]/g, "")) || 0;

    var has = rtransport + rbongkar + rpegawai + rgatel + rretri + rsewa;
    var bp = "Rp. " + has.toLocaleString("id-ID");
    $("#bdagang").val(bp);

    var pn = $("#penambahan").val();
    var lbulan = $("#lbulan").val();
    var rpn = parseFloat(pn.replace(/[^\d]/g, "")) || 0;
    var rbulan = parseFloat(lbulan.replace(/[^\d]/g, "")) || 0;
    var hsl = rbulan - parseFloat(has) + rpn;
    var bs = "Rp. " + hsl.toLocaleString("id-ID");
    $("#hasilbersih").val(bs);
});

//Proyeksi penambahan
$("#penambahan").keyup(function () {
    var pn = $("#penambahan").val();
    var bln = $("#lbulan").val();
    var dgn = $("#bdagang").val();
    var rpn = parseFloat(pn.replace(/[^\d]/g, "")) || 0;
    var rbulan = parseFloat(bln.replace(/[^\d]/g, "")) || 0;
    var rdgn = parseFloat(dgn.replace(/[^\d]/g, "")) || 0;

    var hsl = rbulan - parseFloat(rdgn) + rpn;
    var bs = "Rp. " + hsl.toLocaleString("id-ID");
    $("#hasilbersih").val(bs);
});
