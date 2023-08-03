$(document).ready(function () {
    $("#modal-edit").on("show.bs.modal", function (event) {
        $("#kantor_kodes").empty(); // Kosongkan selec modal ketika modal diaktifkan
        $("#is_actives").empty(); // Kosongkan selec modal ketika modal diaktifkan
        var button = $(event.relatedTarget); // Tombol yang membuka modal
        var id = button.data("id"); // Ambil data-id dari tombol

        // Kirim permintaan AJAX ke route yang mengambil data berdasarkan ID
        $.ajax({
            url: "/admin/user/" + id + "/edit",
            type: "GET",
            dataType: "json",
            cache: false,
            success: function (response) {
                // Isi modal dengan data yang diterima
                var da = JSON.stringify(response[0]);
                var data = JSON.parse(da);
                var hasil = data[0];
                var name = hasil.name;
                var email = hasil.email;
                var username = hasil.username;
                var kode = hasil.code_user;
                var kodekntr = hasil.kantor_kode;
                var nmkantor = hasil.nama_kantor;
                var status = hasil.is_active;

                $("#nama").val(name);
                $("#emails").val(email);
                $("#usernames").val(username);
                $("#code_users").val(kode);

                const nmkntr = response[1];
                var kantor = JSON.stringify(nmkntr);
                $("#kantor_kodes").append(
                    $("<option>", {
                        value: kodekntr,
                        text: nmkantor,
                    }).prop("selected", true)
                );

                //Kantor
                $.each(nmkntr, function (index, item) {
                    if (item.nama_kantor != kodekntr) {
                        $("#kantor_kodes").append(
                            $("<option>", {
                                value: item.kode_kantor,
                                text: item.nama_kantor,
                            })
                        );
                    }
                });

                //Status
                if (status == 0) {
                    $("#is_actives").append(
                        $("<option>", {
                            value: 0,
                            text: "Tidak Aktif",
                        }).prop("selected", true)
                    );
                    $("#is_actives").append(
                        $("<option>", {
                            value: 1,
                            text: "Aktif",
                        })
                    );
                } else if (status == 1) {
                    $("#is_actives").append(
                        $("<option>", {
                            value: 1,
                            text: "Aktif",
                        }).prop("selected", true)
                    );
                    $("#is_actives").append(
                        $("<option>", {
                            value: 0,
                            text: "Tidak Aktif",
                        })
                    );
                }
            },
            error: function (xhr, status, error) {
                // Tindakan jika terjadi kesalahan dalam permintaan AJAX
                console.error("Error:", xhr.responseText);
            },
        });
    });

    $("#modal-akses").on("show.bs.modal", function (event) {
        $("#kantor_kodes").empty(); // Kosongkan selec modal ketika modal diaktifkan
        $("#is_actives").empty(); // Kosongkan selec modal ketika modal diaktifkan
        var button = $(event.relatedTarget); // Tombol yang membuka modal
        var id = button.data("id"); // Ambil data-id dari tombol

        // Kirim permintaan AJAX ke route yang mengambil data berdasarkan ID
        $.ajax({
            url: "/admin/akses/" + id + "/editakses",
            type: "GET",
            dataType: "json",
            cache: false,
            success: function (response) {
                // Isi modal dengan data yang diterima
                var da = JSON.stringify(response[0]);
                var data = JSON.parse(da);
                var hasil = data[0];
                var id = hasil.id;
                var name = hasil.name;
                var kode = hasil.code_user;
                var nmrole = data.name_roles;

                $("#model_id").val(id);
                $("#names").val(name);
                $("#myForm").attr("action", "/admin/akses/" + kode);
                console.log(response[0]);
                console.log(response[1]);

                var role = response[1];

                // Role
                if (condition) {
                }
                $.each(role, function (index, item) {
                    if (item.name != nmrole) {
                        $("#roles_id").append(
                            $("<option>", {
                                value: item.id,
                                text: item.name,
                            })
                        );
                    }
                });
            },
            error: function (xhr, status, error) {
                // Tindakan jika terjadi kesalahan dalam permintaan AJAX
                console.error("Error:", xhr.responseText);
            },
        });
    });
});
