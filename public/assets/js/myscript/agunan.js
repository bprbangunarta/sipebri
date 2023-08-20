$(document).ready(function () {
    $("#modal-edit").on("show.bs.modal", function (event) {
        $("#jenis").empty();
        $("#dokumen").empty();
        var button = $(event.relatedTarget); // Tombol yang membuka modal
        var id = button.data("id"); // Ambil data-id dari tombol

        // Kirim permintaan AJAX ke route yang mengambil data berdasarkan ID
        $.ajax({
            url: "/pengajuan/editagunan/" + id + "/edit",
            type: "GET",
            dataType: "json",
            cache: false,
            success: function (response) {
                // Isi modal dengan data yang diterima
                var da = JSON.stringify(response[0]);
                var data = JSON.parse(da);
                var hasil = data[0];
                var nama = hasil.atas_nama;

                var dti = JSON.stringify(response[1]);
                var dat = JSON.parse(dti);

                var ag = JSON.stringify(response[2]);
                var agun = JSON.parse(ag);

                var dok = JSON.stringify(response[3]);
                var dokumen = JSON.parse(dok);

                $("#data").val(hasil.id);
                $("#input_user").val(hasil.auth);
                console.log(hasil);
                $("#jenis").append(
                    $("<option>", {
                        value: hasil.jenis_agunan_kode,
                        text: hasil.jenis_agunan,
                    }).prop("selected", true)
                );

                //Data agunan
                agun.forEach((data) => {
                    if (data.jenis_agunan != hasil.jenis_agunan) {
                        $("#jenis").append(
                            $("<option>", {
                                value: data.kode,
                                text: data.jenis_agunan,
                            })
                        );
                    }
                });

                $("#dokumen").append(
                    $("<option>", {
                        value: hasil.jenis_dokumen_kode,
                        text: hasil.jenis_dokumen,
                    }).prop("selected", true)
                );

                //Data Dokumen
                dokumen.forEach((data) => {
                    if (data.jenis_dokumen != hasil.jenis_dokumen) {
                        $("#dokumen").append(
                            $("<option>", {
                                value: data.kode,
                                text: data.jenis_dokumen,
                            })
                        );
                    }
                });

                $("#no_dok").val(hasil.no_dokumen);

                var kapital = nama.toUpperCase();
                $("#nama").val(kapital);
                $("#masa_agunans").val(hasil.masa_agunan);

                //Dati
                $("#dati").append(
                    $("<option>", {
                        value: hasil.kode_dati,
                        text: hasil.nama_dati,
                    }).prop("selected", true)
                );
                dat.forEach((data) => {
                    if (data.nama_dati != hasil.nama_dati) {
                        $("#dati").append(
                            $("<option>", {
                                value: data.kode_dati,
                                text: data.nama_dati,
                            })
                        );
                    }
                });

                $("#lok").val(hasil.lokasi);
                $("#catatans").val(hasil.catatan);
            },
            error: function (xhr, status, error) {
                // Tindakan jika terjadi kesalahan dalam permintaan AJAX
                console.error("Error:", xhr.responseText);
            },
        });
    });
});
