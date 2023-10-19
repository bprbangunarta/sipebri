// $(document).ready(function () {
//     $("#modal-foto").on("show.bs.modal", function (event) {
//         var button = $(event.relatedTarget); // Tombol yang membuka modal
//         var id = button.data("id"); // Ambil data-id dari tombol

//         // Kirim permintaan AJAX ke route yang mengambil data berdasarkan ID
//         $.ajax({
//             url: "/themes/analisa/jaminan/fhoto/kendaraan/" + id + "/edit",
//             type: "GET",
//             dataType: "json",
//             cache: false,
//             success: function (response) {
//                 console.log(response);
//                 $("#prevdepan").click(function (e) {
//                     e.preventDefault();
//                     if (response[0] == null) {
//                         notif();
//                     }
//                     window.open(response[0], "_blank");
//                 });
//                 $("#prevbelakang").click(function (e) {
//                     e.preventDefault();
//                     window.open(response[1], "_blank");
//                 });
//                 $("#prevkiri").click(function (e) {
//                     e.preventDefault();
//                     window.open(response[2], "_blank");
//                 });
//                 $("#prevkanan").click(function (e) {
//                     e.preventDefault();
//                     window.open(response[3], "_blank");
//                 });
//             },
//             error: function (xhr, status, error) {
//                 // Tindakan jika terjadi kesalahan dalam permintaan AJAX
//                 console.error("Error:", xhr.responseText);
//             },
//         });

//         function notif() {
//             Swal.fire({
//                 text: "Anda tidak memiliki gambar",
//                 icon: "warning",
//                 showCancelButton: no,
//                 confirmButtonText: "Ok!",
//                 cancelButtonText: "Tidak, batalkan",
//             });
//         }
//     });
// });

$(document).ready(function () {
    $("#modal-foto").on("show.bs.modal", function (event) {
        var button = $(event.relatedTarget); // Tombol yang membuka modal
        var id = button.data("id"); // Ambil data-id dari tombol

        // Kirim permintaan AJAX ke route yang mengambil data berdasarkan ID
        $.ajax({
            url: "/themes/analisa/jaminan/fhoto/kendaraan/" + id + "/edit",
            type: "GET",
            dataType: "json",
            cache: false,
            success: function (response) {
                console.log(response);
                $("#prevdepan").click(function (e) {
                    e.preventDefault();
                    if (gambar1 === null || gambar1 === undefined) {
                    } else {
                        window.open(response[0], "_blank");
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
                console.error("Error:", xhr.responseText);
            },
        });
    });
});
