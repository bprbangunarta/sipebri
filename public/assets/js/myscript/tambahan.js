var modal = document.getElementById("modal");
var inves = document.getElementById("inves");
var konsumtif = document.getElementById("konsumtif");
var pelunasan = document.getElementById("pelunasan");
var take_over = document.getElementById("take_over");
var administrasi = document.getElementById("administrasi");
var asuransi = document.getElementById("asuransi");
var dana = document.getElementById("dana");
var nilai_lain = document.getElementById("nilai_lain");

if (modal) {
    modal.addEventListener("keyup", function (e) {
        modal.value = formatRupiah(this.value, "Rp. ");
    });
}

if (inves) {
    inves.addEventListener("keyup", function (e) {
        inves.value = formatRupiah(this.value, "Rp. ");
    });
}
if (konsumtif) {
    konsumtif.addEventListener("keyup", function (e) {
        konsumtif.value = formatRupiah(this.value, "Rp. ");
    });
}
if (pelunasan) {
    pelunasan.addEventListener("keyup", function (e) {
        pelunasan.value = formatRupiah(this.value, "Rp. ");
    });
}
if (take_over) {
    take_over.addEventListener("keyup", function (e) {
        take_over.value = formatRupiah(this.value, "Rp. ");
    });
}
if (administrasi) {
    administrasi.addEventListener("keyup", function (e) {
        administrasi.value = formatRupiah(this.value, "Rp. ");
    });
}
if (asuransi) {
    asuransi.addEventListener("keyup", function (e) {
        asuransi.value = formatRupiah(this.value, "Rp. ");
    });
}
if (dana) {
    dana.addEventListener("keyup", function (e) {
        dana.value = formatRupiah(this.value, "Rp. ");
    });
}
if (nilai_lain) {
    nilai_lain.addEventListener("keyup", function (e) {
        nilai_lain.value = formatRupiah(this.value, "Rp. ");
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
