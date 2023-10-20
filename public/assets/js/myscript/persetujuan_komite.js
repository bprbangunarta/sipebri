$(document).ready(function () {
    $("#modal-edit").on("show.bs.modal", function (event) {
        $("#komite").empty();
        var button = $(event.relatedTarget);
        var pengajuan = button.data("pengajuan");
        var token = $('meta[name="csrf-token"]').attr("content");

        var token = $('meta[name="csrf-token"]').attr("content");
        var data = {
            field: pengajuan,
            _token: token,
        };
        $.ajax({
            url: "/themes/komite/kredit/data",
            type: "post",
            dataType: "json",
            cache: false,
            data: data,
            success: function (response) {
                var da = JSON.stringify(response);
                var data = JSON.parse(da);
                var hasil = data[0];
                $("#kode").val(hasil.kode_pengajuan);
                $("#nama").val(hasil.nama_nasabah);
                var komite = $("#komite");
                var role = hasil.role_user;

                var pal = hasil.plafon;
                var pl = formatRupiah(pal);
                $("#plafon").val("Rp. " + " " + pl);

                if (role === "Staff Analis" && pal <= 10000000) {
                    var options = [
                        { value: "Disetujui", text: "Disetujui" },
                        { value: "Ditolak", text: "Ditolak" },
                        { value: "Dibatalkan", text: "Dibatalkan" },
                        { value: "Dibatalkan", text: "Dibatalkan" },
                        { value: "Proses Analisa", text: "Proses Analisa" },
                    ];
                } else if (role == "Staff Analis" && pal > 1000000) {
                    var options = [
                        { value: "Ditolak", text: "Ditolak" },
                        { value: "Dibatalkan", text: "Dibatalkan" },
                        { value: "Dibatalkan", text: "Dibatalkan" },
                        { value: "Proses Analisa", text: "Proses Analisa" },
                    ];
                }

                if (role === "Kasi Analis" && pal <= 20000000) {
                    var options = [
                        { value: "Disetujui", text: "Disetujui" },
                        { value: "Ditolak", text: "Ditolak" },
                        { value: "Dibatalkan", text: "Dibatalkan" },
                        { value: "Dibatalkan", text: "Dibatalkan" },
                        { value: "Proses Analisa", text: "Proses Analisa" },
                    ];
                } else if (role == "Staff Analis" && pal > 20000000) {
                    var options = [
                        { value: "Ditolak", text: "Ditolak" },
                        { value: "Dibatalkan", text: "Dibatalkan" },
                        { value: "Dibatalkan", text: "Dibatalkan" },
                        { value: "Proses Analisa", text: "Proses Analisa" },
                    ];
                }

                if (role === "Kabag" && pal <= 30000000) {
                    var options = [
                        { value: "Disetujui", text: "Disetujui" },
                        { value: "Ditolak", text: "Ditolak" },
                        { value: "Dibatalkan", text: "Dibatalkan" },
                        { value: "Dibatalkan", text: "Dibatalkan" },
                        { value: "Proses Analisa", text: "Proses Analisa" },
                    ];
                } else if (role == "Staff Analis" && pal > 30000000) {
                    var options = [
                        { value: "Ditolak", text: "Ditolak" },
                        { value: "Dibatalkan", text: "Dibatalkan" },
                        { value: "Dibatalkan", text: "Dibatalkan" },
                        { value: "Proses Analisa", text: "Proses Analisa" },
                    ];
                }

                $.each(options, function (index, option) {
                    komite.append(
                        $("<option></option>")
                            .attr("value", option.value)
                            .text(option.text)
                    );
                });

                // var options = [
                //     { value: "Disetujui", text: "Disetujui" },
                //     { value: "Ditolak", text: "Ditolak" },
                //     { value: "Dibatalkan", text: "Dibatalkan" },
                //     { value: "Dibatalkan", text: "Dibatalkan" },
                //     { value: "Proses Analisa", text: "Proses Analisa" },
                // ];

                // $.each(options, function (index, option) {
                //     komite.append(
                //         $("<option></option>")
                //             .attr("value", option.value)
                //             .text(option.text)
                //     );
                // });
            },
            error: function (xhr, status, error) {
                // Tindakan jika terjadi kesalahan dalam permintaan AJAX
                console.error("Error:", xhr.responseText);
            },
        });

        function formatRupiah(angka, prefix) {
            var number_string = angka.replace(/[^,\d]/g, "").toString(),
                split = number_string.split(","),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                separator = sisa ? "." : "";
                rupiah += separator + ribuan.join(".");
            }

            rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
            return prefix == undefined ? rupiah : rupiah ? "Rp. " + rupiah : "";
        }
    });
});
