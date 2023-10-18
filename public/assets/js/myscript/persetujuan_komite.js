$(document).ready(function () {
    $("#modal-edit").on("show.bs.modal", function (event) {
        var button = $(event.relatedTarget);
        var pengajuan = button.data("pengajuan");
        var token = $('meta[name="csrf-token"]').attr("content");

        var token = $('meta[name="csrf-token"]').attr("content");
        var data = {
            field: pengajuan,
            _token: token,
        };
        $.ajax({
            url: "/themes/komite/kredit/data",
            type: "post",
            dataType: "json",
            cache: false,
            data: data,
            success: function (response) {
                var da = JSON.stringify(response);
                var data = JSON.parse(da);
                var hasil = data[0];
                $("#kode").val(hasil.kode_pengajuan);
                $("#nama").val(hasil.nama_nasabah);

                var pal = hasil.plafon;
                var pl = "Rp. " + pal.toLocaleString("id-ID");
                $("#plafon").val(pl);
            },
            error: function (xhr, status, error) {
                // Tindakan jika terjadi kesalahan dalam permintaan AJAX
                console.error("Error:", xhr.responseText);
            },
        });
    });
});
