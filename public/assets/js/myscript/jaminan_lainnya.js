var nilaipasar = document.getElementById("nilai_pasar");
var nilaitaksasi = document.getElementById("nilai_taksasi");
if (nilaipasar) {
    nilaipasar.addEventListener("keyup", function (e) {
        nilaipasar.value = formatRupiah(this.value, "Rp. ");
    });
}
if (nilaitaksasi) {
    nilaitaksasi.addEventListener("keyup", function (e) {
        nilaitaksasi.value = formatRupiah(this.value, "Rp. ");
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
    $("#modal-edit").on("show.bs.modal", function (event) {
        var button = $(event.relatedTarget); // Tombol yang membuka modal
        var id = button.data("id"); // Ambil data-id dari tombol

        // Kirim permintaan AJAX ke route yang mengambil data berdasarkan ID
        $.ajax({
            url: "/themes/analisa/jaminan/lainnya/" + id + "/edit",
            type: "GET",
            dataType: "json",
            cache: false,
            success: function (response) {
                $("#id").val(response.jaminan_id);
                $("#jenis_agunan").val(response.jenis_agunan);
                $("#dokumen").val(response.jenis_dokumen);
                $("#no_dok").val(response.no_dokumen);
                $("#nama").val(response.atas_nama);
                $("#lokasi").val(response.lokasi);
                $("#catatan").val(response.catatan);

                var np = response.nilai_pasar ?? 0;
                if (np == 0) {
                    var ps = 0;
                } else {
                    var ps = formatRupiah(np);
                }
                $("#nilai_pasar").val("RP. " + " " + ps);

                var nt = response.nilai_taksasi ?? 0;
                if (nt == 0) {
                    var ts = 0;
                } else {
                    var ts = formatRupiah(nt);
                }

                $("#nilai_taksasi").val("Rp. " + " " + ts);
            },
            error: function (xhr, status, error) {
                // Tindakan jika terjadi kesalahan dalam permintaan AJAX
                console.error("Error:", xhr.responseText);
            },
        });
    });
});
