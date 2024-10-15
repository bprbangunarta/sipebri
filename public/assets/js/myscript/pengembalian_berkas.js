$(document).ready(function () {
    $("#modal-pengembalian").on("show.bs.modal", function (event) {
        var button = $(event.relatedTarget);
        var pengajuan = button.data("pengajuan");
        
        $("#kd_pengajuan").val(pengajuan);
        $.ajax({
            url: "/themes/komite/update/pengembalian/berkas",
            type: "get",
            dataType: "json",
            cache: false,
            data: {
                data: pengajuan
            },
            success: function (response) {
                $('#catatans').val(response.catatan)
            },
            error: function (xhr, status, error) {
                // Tindakan jika terjadi kesalahan dalam permintaan AJAX
                console.error("Error:", xhr.responseText);
            },
        });
    });
});

