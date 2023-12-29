$("#bukti-realisasi").on("show.bs.modal", function (event) {
    var button = $(event.relatedTarget); // Tombol yang membuka modal
    var kode = button.data("id"); // Ambil data-id dari tombol
    
    // Kirim permintaan AJAX ke route yang mengambil data berdasarkan ID
    $.ajax({
        url: "/themes/notifikasi/realisasi/kredit/" + kode,
        type: "GET",
        dataType: "json",
        cache: false,
        success: function (response) {
            $('#catatan').val(response.catatan)            
        },
        error: function (xhr, status, error) {
            // Tindakan jika terjadi kesalahan dalam permintaan AJAX
            console.error("Error:", xhr.responseText);
        },
    });
    
    $("#pemohon")
            .off("click")
            .on("click", function (e) {
                e.preventDefault();
                var button = $(event.relatedTarget); // Tombol yang membuka modal
                var kode = button.data("id");

                $.ajax({
                    url: "/themes/notifikasi/realisasi/kredit/foto/" + [kode, 'pemohon'],
                    type: "GET",
                    dataType: "json",
                     cache: false,
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

    $("#pendamping")
            .off("click")
            .on("click", function (e) {
                e.preventDefault();
                var button = $(event.relatedTarget); // Tombol yang membuka modal
                var kode = button.data("id");

                $.ajax({
                    url: "/themes/notifikasi/realisasi/kredit/foto/" + [kode, 'pendamping'],
                    type: "GET",
                    dataType: "json",
                     cache: false,
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
