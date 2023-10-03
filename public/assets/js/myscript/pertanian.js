var sendiri = document.getElementById("lsendiri");
var sewa = document.getElementById("lsewa");
var gadai = document.getElementById("lgadai");
var panen = document.getElementById("hpanen");
var hrg = document.getElementById("hrg");
var pengolahan = document.getElementById("pengolahan");
var bibit = document.getElementById("bibit");
var pupuk = document.getElementById("pupuk");
var pestisida = document.getElementById("pestisida");
var tenaga_kerja = document.getElementById("tenaga_kerja");
var pengairan = document.getElementById("pengairan");
var panen = document.getElementById("panen");
var penggarap = document.getElementById("penggarap");
var pajak = document.getElementById("pajak");
var iuran = document.getElementById("iuran_desa");
var amortisasi = document.getElementById("amortisasi");
var luas_tanah = document.getElementById("luas_tanah");
var pinjaman_bank = document.getElementById("pinjaman_bank");
var penambahan = document.getElementById("penambahan");
var hpanen = document.getElementById("hpanen");

if (sendiri) {
    sendiri.addEventListener("keyup", function (e) {
        sendiri.value = format(this.value, "Rp. ");
    });
}
if (sewa) {
    sewa.addEventListener("keyup", function (e) {
        sewa.value = format(this.value, "Rp. ");
    });
}
if (gadai) {
    gadai.addEventListener("keyup", function (e) {
        gadai.value = format(this.value, "Rp. ");
    });
}
if (panen) {
    panen.addEventListener("keyup", function (e) {
        panen.value = format(this.value, "Rp. ");
    });
}
if (hrg) {
    hrg.addEventListener("keyup", function (e) {
        hrg.value = formatRupiah(this.value, "Rp. ");
    });
}
if (pengolahan) {
    pengolahan.addEventListener("keyup", function (e) {
        pengolahan.value = formatRupiah(this.value, "Rp. ");
    });
}
if (bibit) {
    bibit.addEventListener("keyup", function (e) {
        bibit.value = formatRupiah(this.value, "Rp. ");
    });
}
if (pupuk) {
    pupuk.addEventListener("keyup", function (e) {
        pupuk.value = formatRupiah(this.value, "Rp. ");
    });
}
if (pestisida) {
    pestisida.addEventListener("keyup", function (e) {
        pestisida.value = formatRupiah(this.value, "Rp. ");
    });
}
if (tenaga_kerja) {
    tenaga_kerja.addEventListener("keyup", function (e) {
        tenaga_kerja.value = formatRupiah(this.value, "Rp. ");
    });
}
if (pengairan) {
    pengairan.addEventListener("keyup", function (e) {
        pengairan.value = formatRupiah(this.value, "Rp. ");
    });
}
if (panen) {
    panen.addEventListener("keyup", function (e) {
        panen.value = formatRupiah(this.value, "Rp. ");
    });
}
if (penggarap) {
    penggarap.addEventListener("keyup", function (e) {
        penggarap.value = formatRupiah(this.value, "Rp. ");
    });
}
if (pajak) {
    pajak.addEventListener("keyup", function (e) {
        pajak.value = formatRupiah(this.value, "Rp. ");
    });
}
if (iuran) {
    iuran.addEventListener("keyup", function (e) {
        iuran.value = formatRupiah(this.value, "Rp. ");
    });
}
if (amortisasi) {
    amortisasi.addEventListener("keyup", function (e) {
        amortisasi.value = formatRupiah(this.value, "Rp. ");
    });
}
if (luas_tanah) {
    luas_tanah.addEventListener("keyup", function (e) {
        luas_tanah.value = format(this.value, "Rp. ");
    });
}
if (hpanen) {
    hpanen.addEventListener("keyup", function (e) {
        hpanen.value = format(this.value, "Rp. ");
    });
}
if (pinjaman_bank) {
    pinjaman_bank.addEventListener("keyup", function (e) {
        pinjaman_bank.value = formatRupiah(this.value, "Rp. ");
    });
}
if (penambahan) {
    penambahan.addEventListener("keyup", function (e) {
        penambahan.value = formatRupiah(this.value, "Rp. ");
    });
}

function format(angka, prefix) {
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
    return prefix == undefined ? rupiah : rupiah ? rupiah : "";
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
    "#pengolahan, #bibit, #pupuk, #pestisida, #tenaga_kerja, #pengairan, #panen, #penggarap, #pajak, #iuran_desa"
).keyup(function () {
    var pengolahan = $("#pengolahan").val();
    var bibit = $("#bibit").val();
    var pupuk = $("#pupuk").val();
    var pestisida = $("#pestisida").val();
    var tenaga_kerja = $("#tenaga_kerja").val();
    var pengairan = $("#pengairan").val();
    var spanen = $("#panen").val();
    var penggarap = $("#penggarap").val();
    var pajak = $("#pajak").val();
    var iuran_desa = $("#iuran_desa").val();
    var pinjaman = $("#pinjaman").val();
    var penambahan = $("#penambahan").val();
    var pendapatan = $("#pendapatan").val();
    var amortisasi = $("#amortisasi").val();
    var plafon = $("#plafon").val();
    var jk_waktu = $("#jangka_waktu").val();

    var rpengolahan = parseFloat(pengolahan.replace(/[^\d]/g, "")) || 0;
    var rbibit = parseFloat(bibit.replace(/[^\d]/g, "")) || 0;
    var rpupuk = parseFloat(pupuk.replace(/[^\d]/g, "")) || 0;
    var rpestisida = parseFloat(pestisida.replace(/[^\d]/g, "")) || 0;
    var rtenaga_kerja = parseFloat(tenaga_kerja.replace(/[^\d]/g, "")) || 0;
    var rpengairan = parseFloat(pengairan.replace(/[^\d]/g, "")) || 0;
    var rspanen = parseFloat(spanen.replace(/[^\d]/g, "")) || 0;
    var rpenggarap = parseFloat(penggarap.replace(/[^\d]/g, "")) || 0;
    var rpajak = parseFloat(pajak.replace(/[^\d]/g, "")) || 0;
    var riuran_desa = parseFloat(iuran_desa.replace(/[^\d]/g, "")) || 0;
    var rpinjaman = parseFloat(pinjaman.replace(/[^\d]/g, "")) || 0;
    var rpenambahan = parseFloat(penambahan.replace(/[^\d]/g, "")) || 0;
    var rpendapatan = parseFloat(pendapatan.replace(/[^\d]/g, "")) || 0;
    var ramortisasi = parseFloat(amortisasi.replace(/[^\d]/g, "")) || 0;
    var rplafon = parseFloat(plafon.replace(/[^\d]/g, "")) || 0;

    var has =
        rpengolahan +
        rbibit +
        rpupuk +
        rpestisida +
        rtenaga_kerja +
        rpengairan +
        rspanen +
        rpenggarap +
        rpajak +
        riuran_desa;

    var a = rpendapatan + rpenambahan;
    var b = has + rpinjaman + ramortisasi;
    var hs = a - b;

    var bs = "Rp. " + has.toLocaleString("id-ID");
    $("#pengeluaran").val(bs);

    //Hasil panen perbulan nilai 1 diberikan ketika kondisi kosong
    var waktupanen = $("#jwp").val();
    var pembagi = jk_waktu / waktupanen;
    //70% (Plafond Permusim)
    var p_musim = (hs * 70) / 100;
    //Cadangkan untuk BPR
    var cd = rplafon / pembagi;
    // Sisa Pendapatan Setelah dikurangi Setoran Pokok
    var sisa = p_musim - cd;
    //Pendapatan Bersih Bulanan
    var pendapatan_bersih = sisa / waktupanen;
    var idr = Math.ceil(parseFloat(pendapatan_bersih));
    var nilaiIDRFormatted = "Rp. " + idr.toLocaleString("id-ID");
    $("#lb_perbulan").val(nilaiIDRFormatted);

    //Total
    var hasil = "Rp. " + hs.toLocaleString("id-ID");
    $("#laba_bersih").val(hasil);
});

$("#hpanen, #hrg").keyup(function () {
    var panen = $("#hpanen").val();
    var harga = $("#hrg").val();
    var pinjaman = $("#pinjaman").val();
    var penambahan = $("#penambahan").val();
    var pengeluaran = $("#pengeluaran").val();
    var amortisasi = $("#amortisasi").val();
    var plafon = $("#plafon").val();
    var jk_waktu = $("#jangka_waktu").val();

    var rpanen = parseFloat(panen.replace(/[^\d]/g, "")) || 0;
    var rharga = parseFloat(harga.replace(/[^\d]/g, "")) || 0;
    var rpinjaman = parseFloat(pinjaman.replace(/[^\d]/g, "")) || 0;
    var rpenambahan = parseFloat(penambahan.replace(/[^\d]/g, "")) || 0;
    var rpengeluaran = parseFloat(pengeluaran.replace(/[^\d]/g, "")) || 0;
    var ramortisasi = parseFloat(amortisasi.replace(/[^\d]/g, "")) || 0;
    var rplafon = parseFloat(plafon.replace(/[^\d]/g, "")) || 0;

    var has = rpanen * rharga;
    var a = rpinjaman + rpengeluaran + ramortisasi;
    var jml = has + rpenambahan - a;

    var bs = "Rp. " + has.toLocaleString("id-ID");
    $("#pendapatan").val(bs);

    //Hasil panen perbulan nilai 1 diberikan ketika kondisi kosong
    var waktupanen = $("#jwp").val();
    var pembagi = jk_waktu / waktupanen;
    //70% (Plafond Permusim)
    var p_musim = (jml * 70) / 100;
    //Cadangkan untuk BPR
    var cd = rplafon / pembagi;
    // Sisa Pendapatan Setelah dikurangi Setoran Pokok
    var sisa = p_musim - cd;
    //Pendapatan Bersih Bulanan
    var pendapatan_bersih = sisa / waktupanen;
    var idr = Math.ceil(parseFloat(pendapatan_bersih));
    var nilaiIDRFormatted = "Rp. " + idr.toLocaleString("id-ID");
    $("#lb_perbulan").val(nilaiIDRFormatted);

    //Total
    var as = "Rp. " + jml.toLocaleString("id-ID");
    $("#laba_bersih").val(as);
});

$("#amortisasi, #pinjaman_bank").keyup(function () {
    var amortisasi = $("#amortisasi").val();
    var pin = $("#pinjaman_bank").val();
    var penambahan = $("#penambahan").val();
    var pengeluaran = $("#pengeluaran").val();
    var pendapatan = $("#pendapatan").val();
    var plafon = $("#plafon").val();
    var jk_waktu = $("#jangka_waktu").val();

    var ramortisasi = parseFloat(amortisasi.replace(/[^\d]/g, "")) || 0;
    var rpin = parseFloat(pin.replace(/[^\d]/g, "")) || 0;
    var rpenambahan = parseFloat(penambahan.replace(/[^\d]/g, "")) || 0;
    var rpengeluaran = parseFloat(pengeluaran.replace(/[^\d]/g, "")) || 0;
    var rpendapatan = parseFloat(pendapatan.replace(/[^\d]/g, "")) || 0;
    var rplafon = parseFloat(plafon.replace(/[^\d]/g, "")) || 0;

    // var has = ramortisasi + rpin;
    var jml = rpin + rpengeluaran + ramortisasi;
    var a = rpenambahan + rpendapatan - jml;

    var bs = "Rp. " + rpin.toLocaleString("id-ID");
    $("#pinjaman").val(bs);

    //Hasil panen perbulan nilai 1 diberikan ketika kondisi kosong
    var waktupanen = $("#jwp").val();
    var pembagi = jk_waktu / waktupanen;
    //70% (Plafond Permusim)
    var p_musim = (a * 70) / 100;
    //Cadangkan untuk BPR
    var cd = rplafon / pembagi;
    // Sisa Pendapatan Setelah dikurangi Setoran Pokok
    var sisa = p_musim - cd;
    //Pendapatan Bersih Bulanan
    var pendapatan_bersih = sisa / waktupanen;
    var idr = Math.ceil(parseFloat(pendapatan_bersih));
    var nilaiIDRFormatted = "Rp. " + idr.toLocaleString("id-ID");
    $("#lb_perbulan").val(nilaiIDRFormatted);

    //Total
    var as = "Rp. " + a.toLocaleString("id-ID");
    $("#laba_bersih").val(as);
});

$("#penambahan").keyup(function () {
    var amortisasi = $("#amortisasi").val();
    var pin = $("#pinjaman").val();
    var pengeluaran = $("#pengeluaran").val();
    var pendapatan = $("#pendapatan").val();
    var penambahan = $("#penambahan").val();
    var plafon = $("#plafon").val();
    var jk_waktu = $("#jangka_waktu").val();

    var ramortisasi = parseFloat(amortisasi.replace(/[^\d]/g, "")) || 0;
    var rpin = parseFloat(pin.replace(/[^\d]/g, "")) || 0;
    var rpengeluaran = parseFloat(pengeluaran.replace(/[^\d]/g, "")) || 0;
    var rpendapatan = parseFloat(pendapatan.replace(/[^\d]/g, "")) || 0;
    var rpenambahan = parseFloat(penambahan.replace(/[^\d]/g, "")) || 0;
    var rplafon = parseFloat(plafon.replace(/[^\d]/g, "")) || 0;

    var jml = rpin + rpengeluaran + ramortisasi;
    var a = rpenambahan + rpendapatan - jml;

    var bs = "Rp. " + rpenambahan.toLocaleString("id-ID");
    $("#penambahan").val(bs);

    //Hasil panen perbulan nilai 1 diberikan ketika kondisi kosong
    var waktupanen = $("#jwp").val();
    var pembagi = jk_waktu / waktupanen;
    //70% (Plafond Permusim)
    var p_musim = (a * 70) / 100;
    //Cadangkan untuk BPR
    var cd = rplafon / pembagi;
    // Sisa Pendapatan Setelah dikurangi Setoran Pokok
    var sisa = p_musim - cd;
    //Pendapatan Bersih Bulanan
    var pendapatan_bersih = sisa / waktupanen;
    var idr = Math.ceil(parseFloat(pendapatan_bersih));
    var nilaiIDRFormatted = "Rp. " + idr.toLocaleString("id-ID");
    $("#lb_perbulan").val(nilaiIDRFormatted);

    //Total
    var as = "Rp. " + a.toLocaleString("id-ID");
    $("#laba_bersih").val(as);
});

$("#lsendiri, #lsewa, #lgadai").keyup(function () {
    var sendiri = $("#lsendiri").val();
    var sewa = $("#lsewa").val();
    var gadai = $("#lgadai").val();

    var rsendiri = parseFloat(sendiri.replace(/[^\d]/g, "")) || 0;
    var rsewa = parseFloat(sewa.replace(/[^\d]/g, "")) || 0;
    var rgadai = parseFloat(gadai.replace(/[^\d]/g, "")) || 0;

    var jml = rsendiri + rsewa + rgadai;

    var bs = jml.toLocaleString("id-ID");
    var hasil = bs + " " + "M2";
    $("#total_tanah").val(hasil);
});
