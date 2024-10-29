$(document).ready(function () {
    $("#modaledit").on("show.bs.modal", function (event) {
        var button = $(event.relatedTarget); // Tombol yang membuka modal
        var id = button.data("id"); // Ambil data-id dari tombol
        
        // Kirim permintaan AJAX ke route yang mengambil data berdasarkan ID
        $.ajax({
            url: "/admin/role/" + id + "/edit",
            type: "get",
            dataType: "json",
            cache: false,
            success: function (response) {
                
                $("#names").val(response.name);
                $("#id_role").val(response.id);
                
            },
            error: function (xhr, status, error) {
                // Tindakan jika terjadi kesalahan dalam permintaan AJAX
                console.error("Error:", xhr.responseText);
            },
        });
    });
});
