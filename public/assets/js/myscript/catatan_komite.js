$(document).ready(function () {
    $("#modal-catatan").on("show.bs.modal", function (event) {
        $("#komite").empty();
        var button = $(event.relatedTarget);
        var pengajuan = button.data("pengajuan");

        $.ajax({
            url: "/themes/komite/kredit/catatan/" + pengajuan,
            type: "get",
            dataType: "json",
            cache: false,
            success: function (response) {
                $("#staff_analis").val(response.catatan1 ?? null);
                $("#kasi_analis").val(response.catatan2 ?? null);
                $("#kabag_analis").val(response.catatan3 ?? null);
                $("#direksi").val(response.catatan4 ?? null);
            },
            error: function (xhr, status, error) {
                // Tindakan jika terjadi kesalahan dalam permintaan AJAX
                console.error("Error:", xhr.responseText);
            },
        });
    });
});
