var modal = document.getElementById("modal");
var inves = document.getElementById("inves");
var konsumtif = document.getElementById("konsumtif");
var pelunasan = document.getElementById("pelunasan");
var take_over = document.getElementById("take_over");

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

$("#modal, #inves, #konsumtif, #pelunasan, #take_over").keyup(function () {
    var modal = $("#modal").val();
    var inves = $("#inves").val();
    var konsumtif = $("#konsumtif").val();
    var pelunasan = $("#pelunasan").val();
    var take_over = $("#take_over").val();

    var rmodal = parseFloat(modal.replace(/[^\d]/g, "")) || 0;
    var rinves = parseFloat(inves.replace(/[^\d]/g, "")) || 0;
    var rkonsumtif = parseFloat(konsumtif.replace(/[^\d]/g, "")) || 0;
    var rpelunasan = parseFloat(pelunasan.replace(/[^\d]/g, "")) || 0;
    var rtake_over = parseFloat(take_over.replace(/[^\d]/g, "")) || 0;

    var jml = rmodal + rinves + rkonsumtif + rpelunasan + rtake_over;
    var hasil = "Rp " + jml.toLocaleString("id-ID");
    $("#kebutuhan_dana").val(hasil);
});
