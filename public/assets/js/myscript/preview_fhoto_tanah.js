$(document).ready(function () {
    $("#modal-foto-tanah").on("show.bs.modal", function (event) {
        $("#prevdepan")
            .off("click")
            .on("click", function (e) {
                e.preventDefault();
                var button = $(event.relatedTarget); // Tombol yang membuka modal
                var token = $('meta[name="csrf-token"]').attr("content");
                var id = button.data("id"); // Ambil data-id dari tombol
                var dataId = id.split(",");

                var data = {
                    iddata: dataId[0],
                    no: "foto1",
                    _token: token,
                };
                $.ajax({
                    url: "/themes/analisa/jaminan/fhoto/kendaraan/prev",
                    type: "POST",
                    data: data,
                    dataType: "json",
                    success: function (response) {
                        if ($.isEmptyObject(response)) {
                            Swal.fire({
                                title: "",
                                text: "Tidak Ada Gambar",
                                icon: "error",
                                confirmButtonText: "Ok",
                            });
                        } else {
                            window.open(response, "_blank");
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Error:", xhr.responseText);
                    },
                });
            });

        $("#prevbelakang")
            .off("click")
            .on("click", function (e) {
                e.preventDefault();
                var button = $(event.relatedTarget); // Tombol yang membuka modal
                var token = $('meta[name="csrf-token"]').attr("content");
                var id = button.data("id"); // Ambil data-id dari tombol
                var dataId = id.split(",");
                var data = {
                    iddata: dataId[0],
                    no: "foto2",
                    _token: token,
                };
                $.ajax({
                    url: "/themes/analisa/jaminan/fhoto/kendaraan/prev",
                    type: "POST",
                    data: data,
                    dataType: "json",
                    success: function (response) {
                        if ($.isEmptyObject(response)) {
                            Swal.fire({
                                title: "",
                                text: "Tidak Ada Gambar",
                                icon: "error",
                                confirmButtonText: "Ok",
                            });
                        } else {
                            window.open(response, "_blank");
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Error:", xhr.responseText);
                    },
                });
            });

        $("#prevkiri")
            .off("click")
            .on("click", function (e) {
                e.preventDefault();
                var button = $(event.relatedTarget); // Tombol yang membuka modal
                var token = $('meta[name="csrf-token"]').attr("content");
                var id = button.data("id"); // Ambil data-id dari tombol
                var dataId = id.split(",");
                var data = {
                    iddata: dataId[0],
                    no: "foto3",
                    _token: token,
                };
                $.ajax({
                    url: "/themes/analisa/jaminan/fhoto/kendaraan/prev",
                    type: "POST",
                    data: data,
                    dataType: "json",
                    success: function (response) {
                        if ($.isEmptyObject(response)) {
                            Swal.fire({
                                title: "",
                                text: "Tidak Ada Gambar",
                                icon: "error",
                                confirmButtonText: "Ok",
                            });
                        } else {
                            window.open(response, "_blank");
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Error:", xhr.responseText);
                    },
                });
            });

        $("#prevkanan")
            .off("click")
            .on("click", function (e) {
                e.preventDefault();
                var button = $(event.relatedTarget); // Tombol yang membuka modal
                var token = $('meta[name="csrf-token"]').attr("content");
                var id = button.data("id"); // Ambil data-id dari tombol
                var dataId = id.split(",");
                var data = {
                    iddata: dataId[0],
                    no: "foto4",
                    _token: token,
                };
                $.ajax({
                    url: "/themes/analisa/jaminan/fhoto/kendaraan/prev",
                    type: "POST",
                    data: data,
                    dataType: "json",
                    success: function (response) {
                        if ($.isEmptyObject(response)) {
                            Swal.fire({
                                title: "",
                                text: "Tidak Ada Gambar",
                                icon: "error",
                                confirmButtonText: "Ok",
                            });
                        } else {
                            window.open(response, "_blank");
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Error:", xhr.responseText);
                    },
                });
            });
    });
});
