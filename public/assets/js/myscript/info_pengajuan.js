$("#info-pengajuan").on("show.bs.modal", function (event) {
    var button = $(event.relatedTarget); // Tombol yang membuka modal
    var kode = button.data("id"); // Ambil data-id dari tombol
    
    // Kirim permintaan AJAX ke route yang mengambil data berdasarkan ID
    $.ajax({
        url: "/data/info/" + kode,
        type: "GET",
        dataType: "json",
        cache: false,
        success: function (response) {
            
            $('#nama_nasabah').val(response[0].nama)
            $('#produk').val(response[0].kode_produk+" "+"-"+" "+response[0].nama_produk)

            var plafon = parseFloat(response[0].plafon)
            var pl = "Rp. " + plafon.toLocaleString("id-ID");
            $("#plafon").val(pl);
            $('#jw').val(response[0].jk+" "+"BULAN"+" "+"-"+" "+response[0].metode_rps)
            $('#desa').val(response[0].kelurahan+" "+"-"+" "+response[0].kecamatan)
            $('#surveyor').val(response[0].surveyor)
            $('#tracking').val(response[0].tracking)

            if(response[0].status == 'Disetujui' || response[0].status == 'Ditolak' || response[0].status == 'Dibatalkan'){
                $('#persetujuan').val(response[0].status)
            }else{
                $('#persetujuan').val("BELUM ADA PERSETUJUAN")
            }

        },
        error: function (xhr, status, error) {
            // Tindakan jika terjadi kesalahan dalam permintaan AJAX
            console.error("Error:", xhr.responseText);
        },
    });
});
