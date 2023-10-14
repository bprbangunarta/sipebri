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
