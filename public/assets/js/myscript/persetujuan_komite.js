$(document).ready(function () {
    $("#modal-edit").on("show.bs.modal", function (event) {
        var button = $(event.relatedTarget);
        var pengajuan = button.data("pengajuan");
        var token = $('meta[name="csrf-token"]').attr("content");
        console.log(button);
        var token = $('meta[name="csrf-token"]').attr("content");
        var data = {
            field: pengajuan,
            _token: token,
        };
    });
});
