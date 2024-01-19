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
        $("#jenis").empty();
        $("#dokumen").empty();
        var button = $(event.relatedTarget); // Tombol yang membuka modal
        var id = button.data("id"); // Ambil data-id dari tombol

        // Kirim permintaan AJAX ke route yang mengambil data berdasarkan ID
        $.ajax({
            url: "/themes/analisa/jaminan/kendaraan/" + id + "/edit",
            type: "GET",
            dataType: "json",
            cache: false,
            success: function (response) {
                $("#id").val(response.id);
                $("#kode").val(response.jenis_agunan_kode);
                $("#jenis").val(response.jenis_agunan);
                $("#dokumen").val(response.jenis_dokumen);
                $("#no_dok").val(response.no_dokumen);
                $("#nama").val(response.atas_nama);
                $("#no_mesin").val(response.no_mesin);
                $("#no_polisi").val(response.no_polisi);
                $("#no_rangka").val(response.no_rangka);
                $("#tipe").val(response.tipe_kendaraan);
                $("#merk").val(response.merek_kendaraan);
                $("#tahun_kendaraan").val(response.tahun_kendaraan);
                $("#warna").val(response.warna_kendaraan);
                $("#lok").val(response.lokasi_kendaraan);

                var np = response.nilai_pasar;
                if (np !== null) {
                    var ps = formatRupiah(np);
                } else {
                    var ps = 0;
                }
                $("#nilai_pasar").val("Rp. " + " " + ps);

                var nt = response.nilai_taksasi;
                if (nt !== null) {
                    var ts = formatRupiah(nt);
                } else {
                    var ts = 0;
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
