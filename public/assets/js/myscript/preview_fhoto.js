$(document).ready(function () {
    $("#modal-foto").on("show.bs.modal", function (event) {
        var button = $(event.relatedTarget); // Tombol yang membuka modal
        var id = button.data("id"); // Ambil data-id dari tombol
        var dataId = id.split(",");
        // console.log(dataId[0]);
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
                console.log(response);
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

$(document).ready(function () {
    $("#modal-foto").on("show.bs.modal", function (event) {
        var button = $(event.relatedTarget); // Tombol yang membuka modal
        var id = button.data("id"); // Ambil data-id dari tombol
        var dataId = id.split(",");
        // console.log(dataId[0]);

        // Kirim permintaan AJAX ke route yang mengambil data berdasarkan ID
        $.ajax({
            url:
                "/themes/analisa/jaminan/fhoto/kendaraan/" +
                dataId[0] +
                "/edit",
            type: "GET",
            dataType: "json",
            cache: false,
            success: function (response) {
                $("#prevdepan").click(function (e) {
                    e.preventDefault();
                    if (
                        response.gambar1 === null ||
                        response.gambar1 === undefined
                    ) {
                        Swal.fire({
                            icon: "error", // Jenis ikon notifikasi (info, success, error, warning)
                            title: "",
                            text: "Tidak ada Fhoto",
                            confirmButtonText: "Tutup",
                        });
                    } else {
                        window.open(response.gambar1, "_blank");
                    }
                });

                // $("#prevbelakang").click(function (e) {
                //     e.preventDefault();
                //     if (response[1] === null) {
                //         notif();
                //     } else {
                //         window.open(response[1], "_blank");
                //     }
                // });
                // $("#prevkiri").click(function (e) {
                //     e.preventDefault();
                //     if (response[2] === null) {
                //         notif();
                //     } else {
                //         window.open(response[2], "_blank");
                //     }
                // });
                // $("#prevkanan").click(function (e) {
                //     e.preventDefault();
                //     if (response[3] === null) {
                //         notif();
                //     } else {
                //         window.open(response[3], "_blank");
                //     }
                // });
            },
            error: function (xhr, status, error) {
                // Tindakan jika terjadi kesalahan dalam permintaan AJAX
                // console.error("Error:", xhr.responseText);
            },
        });
    });
});
