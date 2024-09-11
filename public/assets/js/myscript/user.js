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
                var da = JSON.stringify(response);
                var data = JSON.parse(da);
                var hasil = data[0];
                var name = hasil.name;
                var email = hasil.email;
                var username = hasil.username;
                var kode = hasil.code_user;
                var status = hasil.is_active;

                $("#names").val(name);
                $("#emails").val(email);
                $("#usernames").val(username);
                $("#kode_surveyors").val(hasil.kode_surveyor) ?? null;
                $("#kode_kolektors").val(hasil.kode_kolektor) ?? null;
                $("#user_kodes").val(kode);

                //Kantor
                $("#kode_kantors").append(
                    $("<option>", {
                        value: hasil.kantor_kode,
                        text: hasil.nama_kantor,
                    }).prop("selected", true)
                );

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
        $("#roles_id").empty(); // Kosongkan selec modal ketika modal diaktifkan
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
                var nmrole = data.nama_roles;
                var dtrole = data.kode_roles;
                
                $("#model_id").val(id);
                $("#nama").val(name);
                $("#myForm").attr("action", "/admin/akses/" + kode);

                // Role
                $("#roles_id").append(
                    $("<option>", {
                        value: dtrole,
                        text: nmrole,
                    }).prop("selected", true)
                );

                var role = response[1];
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

                if (dtrole !== undefined) {
                    $("#roles_id").append(
                        $("<option>", {
                            value: "-",
                            text: "--Pilih Role--",
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

    //Reset Password
    $("#modal-password").on("show.bs.modal", function (event) {
        var button = $(event.relatedTarget); // Tombol yang membuka modal
        var user = button.data("user"); // Ambil data-id dari tombol

        //Kirim permintaan AJAX ke route yang mengambil data berdasarkan ID
        $.ajax({
            url: "/admin/user/reset/" + user + "/password",
            type: "get",
            dataType: "json",
            cache: false,
            success: function (response) {
                // Isi modal dengan data yang diterima
                var da = JSON.stringify(response);
                var data = JSON.parse(da);
                var hasil = data;
                var name = hasil[0].name;
                var code = hasil[0].code_user;

                $("#name_user").val(name);
                $("#code").val(code);
            },
            error: function (xhr, status, error) {
                // Tindakan jika terjadi kesalahan dalam permintaan AJAX
                console.error("Error:", xhr.responseText);
            },
        });
    });

    //Show password
    $("#modal-password").on("show.bs.modal", function (event) {
        $("#reset").click(function () {
            var passwordInput = $("#reset");

            if (passwordInput.attr("type") === "password") {
                passwordInput.attr("type", "text");
            } else {
                passwordInput.attr("type", "password");
            }
        });
    });
});
