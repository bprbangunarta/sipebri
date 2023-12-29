var penambahan = document.getElementById("penambahan");
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
$(document).ready(function () {
    $("#penambahan").keyup(function () {
        var penambahan = $("#penambahan").val();
        var lb_perbulan = $("#perbulan").val();
        var rpenambahan = parseFloat(penambahan.replace(/[^\d]/g, "")) || 0;
        var rperbulan = parseFloat(lb_perbulan.replace(/[^\d.-]/g, "")) || 0;

        var jml = rpenambahan + rperbulan;

        var hasil = "Rp. " + jml.toLocaleString("id-ID");
        $("#lb_perbulan").val(hasil);
    });
});
