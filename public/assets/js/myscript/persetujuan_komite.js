$(document).ready(function () {
    $("#modal-edit").on("show.bs.modal", function (event) {
        $("#status_wil").empty();
        var button = $(event.relatedTarget); // Tombol yang membuka modal
        var pengajuan = button.data("pengajuan"); // Ambil data-id dari tombol
        var token = $('meta[name="csrf-token"]').attr("content");
        var data = {
            field: pengajuan,
            _token: token,
        };
        // Kirim permintaan AJAX ke route yang mengambil data berdasarkan ID
        $.ajax({
            url: "/themes/komite/kredit",
            type: "post",
            dataType: "json",
            cache: false,
            data: data,
            success: function (response) {
                // Isi modal dengan data yang diterima
                var da = JSON.stringify(response);
                var data = JSON.parse(da);
                var hasil = data[0];
                $("#kode").val(hasil.kode_pengajuan);
                $("#nama").val(hasil.nama_nasabah);

                var pal = hasil.plafon;
                var pl = "Rp. " + pal.toLocaleString("id-ID");
                $("#plafon").val(pl);

                // var sl = [
                //     "DISETUJUI",
                //     "DITOLAK",
                //     "DIBATALKAN",
                //     "NAIK KASI",
                //     "PROSES ANALISA",
                // ];

                // $.each(sl, function (index, item) {
                //     $("#komite").append(
                //         $("<option>", {
                //             value: sl,
                //             text: sl,
                //         })
                //     );
                // });
            },
            error: function (xhr, status, error) {
                // Tindakan jika terjadi kesalahan dalam permintaan AJAX
                console.error("Error:", xhr.responseText);
            },
        });
    });
});
