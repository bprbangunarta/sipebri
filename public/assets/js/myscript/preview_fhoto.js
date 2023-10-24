$(document).ready(function () {
    $("#modal-foto").on("show.bs.modal", function (event) {
        var button = $(event.relatedTarget); // Tombol yang membuka modal
        var id = button.data("id"); // Ambil data-id dari tombol
        var dataId = id.split(",");

        // Kirim permintaan AJAX ke route yang mengambil data berdasarkan ID
        $.ajax({
            url:
                "/themes/analisa/jaminan/fhoto/kendaraan/data/" +
                dataId[0] +
                "/edit",
            type: "GET",
            dataType: "json",
            cache: false,
            success: function (response) {
                $("#prevdepan").click(function (e) {
                    e.preventDefault();
                    $("#modal-foto").modal("show");
                });
                $("#name_img_1").val(response.foto1);
                $("#image-1").attr("src", response.foto1);
                $("#name_img_2").val(response.foto2);
                $("#name_img_3").val(response.foto3);
                $("#name_img_4").val(response.foto4);
            },
            error: function (xhr, status, error) {
                // Tindakan jika terjadi kesalahan dalam permintaan AJAX
                console.error("Error:", xhr.responseText);
            },
        });

        function notif() {
            Swal.fire({
                text: "Anda tidak memiliki gambar",
                icon: "warning",
                showCancelButton: no,
                confirmButtonText: "Ok!",
                cancelButtonText: "Tidak, batalkan",
            });
        }
    });
});
