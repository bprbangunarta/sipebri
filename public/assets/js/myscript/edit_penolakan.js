$("#edit-penolakan").on("show.bs.modal", function (event) {
    var button = $(event.relatedTarget); // Tombol yang membuka modal
    var kode = button.data("kd"); // Ambil data-id dari tombol
    var token = $('meta[name="csrf-token"]').attr("content");
    $('#alasan_id').empty()

    var data = {
        kd: kode,
        _token: token,
    };

    // Kirim permintaan AJAX ke route yang mengambil data berdasarkan ID
    $.ajax({
        url: "/themes/penolakan/edit",
        type: "POST",
        data: data,
        dataType: "json",
        cache: false,
        success: function (response) {

            $("#id_tolak").val(response[0].no_penolakan);
            $("#name_nasabah").val(response[0].nama_nasabah);
            $("#nmr").val(response[0].nomor);
            $("#kode").val(response[0].pengajuan_kode);
            var ket = response[0].keterangan;
            $("#ket").val(ket.toUpperCase());

            var select = $('#alasan_id');
            var selectedCategoryId = response[0].alasan_id; // Misalkan ID kategori yang ingin Anda pilih
            $.each(response[1], function (index, category) {
                var selected = '';
                if (category.id == selectedCategoryId) {
                    selected = 'selected';
                }
                select.append('<option value="' + category.id + '" ' + selected + '>' + category.alasan + '</option>');
            });
        },
        error: function (xhr, status, error) {
            // Tindakan jika terjadi kesalahan dalam permintaan AJAX
            console.error("Error:", xhr.responseText);
        },
    });
});
