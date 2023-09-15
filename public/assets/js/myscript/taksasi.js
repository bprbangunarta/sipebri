var taksasi = document.getElementById("taksasi");

if (taksasi) {
    taksasi.addEventListener("keyup", function (e) {
        taksasi.value = formatRupiah(this.value, "Rp. ");
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
        var jaminan = button.data("id"); // Ambil data-id dari tombol

        // Kirim permintaan AJAX ke route yang mengambil data berdasarkan ID
        $.ajax({
            url: "jaminan/" + jaminan + "/edit",
            type: "GET",
            dataType: "json",
            cache: false,
            success: function (response) {
                // Isi modal dengan data yang diterima
                var da = JSON.stringify(response[0]);
                var data = JSON.parse(da);
                var hasil = data[0];
                var nama = hasil.atas_nama;

                $("#data").val(hasil.id);
                $("#input_user").val(hasil.auth);
                $("#jenis").val(hasil.jenis_agunan);
                $("#dokumen").val(hasil.jenis_dokumen);

                $("#no_dok").val(hasil.no_dokumen);
                $("#atas_nama").val(nama);
                $("#data").val(hasil.id);

                var taksasi = parseInt(hasil.nilai_taksasi);
                var tak =
                    "Rp. " + (taksasi ? taksasi.toLocaleString("id-ID") : 0);
                $("#taksasi").val(tak);
            },
            error: function (xhr, status, error) {
                // Tindakan jika terjadi kesalahan dalam permintaan AJAX
                console.error("Error:", xhr.responseText);
            },
        });
    });
});
