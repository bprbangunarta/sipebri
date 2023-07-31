$(document).ready(function () {
    $("#modal-edit").on("show.bs.modal", function (event) {
        var button = $(event.relatedTarget); // Tombol yang membuka modal
        var id = button.data("id"); // Ambil data-id dari tombol

        // Kirim permintaan AJAX ke route yang mengambil data berdasarkan ID
        $.ajax({
            url: "/admin/produk/" + id + "/edit",
            type: "GET",
            dataType: "json",
            success: function (response) {
                // Isi modal dengan data yang diterima
                var da = JSON.stringify(response);
                var data = JSON.parse(da);
                var hasil = data[0];
                var kode = hasil.kode_produk;
                var nama = hasil.nama_produk;

                $("#kode_pro").val(kode);
                $("#nama_pro").val(nama);
            },
            error: function (xhr, status, error) {
                // Tindakan jika terjadi kesalahan dalam permintaan AJAX
                console.error("Error:", xhr.responseText);
            },
        });
    });

    // //Simpan
    // $("#formEdit").submit(function (e) {
    //     e.preventDefault();
    //     var kode = $("#kode_pro").val();
    //     var nama = $("#nama_pro").val();

    //     // Kirim permintaan AJAX ke route yang mengambil data berdasarkan ID
    //     $.ajax({
    //         url: "/admin/produk/" + kode,
    //         method: "put",
    //         dataType: "json",
    //         data: {
    //             kode_produk: kode,
    //             nama_produk: nama,
    //             rate: 0,
    //             jumlah_pengajuan: 0,
    //             _token: "{{ csrf_token() }}",
    //         },
    //         success: function (response) {
    //             // Tangani respon sukses dari server
    //             if (response.status === "success") {
    //                 Swal.fire({
    //                     title: "Succes",
    //                     text: "Data berhasil diupdate",
    //                     icon: "success", // success, error, warning, info, question
    //                     confirmButtonText: "OK",
    //                 }).then(function () {
    //                     $("#modal-edit").hide();
    //                     location.reload();
    //                 });
    //             } else if (response.status === "error") {
    //                 // Jika error, tampilkan SweetAlert error
    //                 Swal.fire({
    //                     icon: "error",
    //                     title: "Data anda gagal diupdate",
    //                     text: response.message,
    //                 });
    //             }
    //             console.log(response);
    //         },
    //         error: function (xhr, status, error) {
    //             // Tangani kesalahan pada AJAX request (jika terjadi)
    //             // Tampilkan SweetAlert error
    //             Swal.fire({
    //                 icon: "error",
    //                 title: "Kesalahan!",
    //                 text: "Terjadi kesalahan saat mengirim permintaan ke server.",
    //             });
    //         },
    //     });
    // });
});
