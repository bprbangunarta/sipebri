var nilaipasar = document.getElementById("nilai_pasar");
var nilaitaksasi = document.getElementById("nilai_taksasi");
var luas = document.getElementById("luas");
if (luas) {
    luas.addEventListener("keyup", function (e) {
        luas.value = format(this.value, "Rp. ");
    });
}
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

$(document).ready(function () {
    $("#modal-edit").on("show.bs.modal", function (event) {
        var button = $(event.relatedTarget); // Tombol yang membuka modal
        var id = button.data("id"); // Ambil data-id dari tombol

        // Kirim permintaan AJAX ke route yang mengambil data berdasarkan ID
        $.ajax({
            url: "/themes/analisa/jaminan/tanah/" + id + "/edit",
            type: "GET",
            dataType: "json",
            cache: false,
            success: function (response) {
                
                $("#id").val(response.jaminan_id);
                $("#jenis_agunan").val(response.jenis_agunan);
                $("#jenis_dokumen").val(response.jenis_dokumen);
                $("#no_dok").val(response.no_dokumen);
                $("#atas_nama").val(response.atas_nama);
                var ls = format(response.luas);
                $("#luas").val(ls);
                $("#lokasi").val(response.lokasi);

                if (response.nilai_pasar == null) {
                    return 0;
                } else {
                    var np = response.nilai_pasar;
                    var ps = formatRupiah(np);
                    $("#nilai_pasar").val("Rp. " + " " + ps);
                }
                if (response.nilai_taksasi == null) {
                    return 0;
                } else {
                    var nt = response.nilai_taksasi;
                    var ts = formatRupiah(nt);
                    $("#nilai_taksasi").val("Rp. " + " " + ts);
                }
            },
            error: function (xhr, status, error) {
                // Tindakan jika terjadi kesalahan dalam permintaan AJAX
                console.error("Error:", xhr.responseText);
            },
        });
    });
});
