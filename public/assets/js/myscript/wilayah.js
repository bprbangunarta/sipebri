$(document).ready(function () {
    // $("#form_kab").hide();
    // $("#form_kec").hide();
    // $("#form_des").hide();
    // $("#form_pos").hide();
    // $("#kab").hide();
    // $("#kec").hide();
    // $("#des").hide();
    // $("#pos").hide();

    // Menggunakan Ajax untuk mengirim data Kabupaten ke controller
    $("#select-kab").on("change", function () {
        $("#form_kec").empty();
        $("#form_des").empty();
        $("#form_pos").empty();
        var nama = $("#select-kabupaten").val();
        $.ajax({
            url: "{{ route('kabupaten') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                name: nama,
            },
            success: function (response) {
                let st = JSON.stringify(response);
                let obj = Object.values(response);
                var ch = [];
                for (var i = 0; i < obj.length; i++) {
                    if (ch.indexOf(obj[i].kecamatan) === -1) {
                        ch.push(obj[i].kecamatan);
                    }
                }
                ch.sort();
                // $("#form_kec").show();
                // $("#kec").show();
                ch.forEach((data) => {
                    $("#select-kecamatan").append(
                        $("<option>", {
                            value: data,
                            text: data,
                        })
                    );
                });
            },
            error: function (xhr) {
                // Tindakan jika terjadi error
                console.log(xhr.responseText);
            },
        });
    });

    // Menggunakan Ajax untuk mengirim data Kecamatan ke controller
    $("#form_kec").on("change", function () {
        $("#form_des").empty();
        $("#form_pos").empty();
        var nama = $("#form_kec").val();
        $.ajax({
            url: "{{ route('kecamatan') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                name: nama,
            },
            success: function (response) {
                let st = JSON.stringify(response);
                let obj = Object.values(response);
                var ch = [];
                for (var i = 0; i < obj.length; i++) {
                    if (ch.indexOf(obj[i].KELURAHAN) === -1) {
                        ch.push(obj[i].KELURAHAN);
                    }
                }
                ch.sort();
                $("#form_des").show();
                $("#des").show();
                ch.forEach((data) => {
                    $("#form_des").append(
                        $("<option>", {
                            value: data,
                            text: data,
                        })
                    );
                });
            },
            error: function (xhr) {
                // Tindakan jika terjadi error
                console.log(xhr.responseText);
            },
        });
    });

    // Menggunakan Ajax untuk mengirim data Kelurahan ke controller
    $("#form_des").on("change", function () {
        $("#form_pos").empty();
        var nama = $("#form_des").val();
        $.ajax({
            url: "{{ route('kelurahan') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                name: nama,
            },
            success: function (response) {
                let st = JSON.stringify(response);
                let obj = Object.values(response);
                var ch = [];
                for (var i = 0; i < obj.length; i++) {
                    if (ch.indexOf(obj[i].KODE_POS) === -1) {
                        ch.push(obj[i].KODE_POS);
                    }
                }
                ch.sort();
                $("#form_pos").show();
                $("#pos").show();
                ch.forEach((data) => {
                    $("#form_pos").append(
                        $("<option>", {
                            value: data,
                            text: data,
                        })
                    );
                });
            },
            error: function (xhr) {
                // Tindakan jika terjadi error
                console.log(xhr.responseText);
            },
        });
    });
});
