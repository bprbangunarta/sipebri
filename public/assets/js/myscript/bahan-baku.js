//Harga Jual RP
var hrg1 = document.getElementById("hrg1");
var hrg2 = document.getElementById("hrg2");
var hrg3 = document.getElementById("hrg3");
var hrg4 = document.getElementById("hrg4");
var hrg5 = document.getElementById("hrg5");
var hrg6 = document.getElementById("hrg6");
var hrg7 = document.getElementById("hrg7");
var hrg8 = document.getElementById("hrg8");
var hrg9 = document.getElementById("hrg9");
var hrg10 = document.getElementById("hrg10");

if (hrg1) {
    hrg1.addEventListener("keyup", function (e) {
        hrg1.value = formatRupiah(this.value, "Rp. ");
    });
}
if (hrg2) {
    hrg2.addEventListener("keyup", function (e) {
        hrg2.value = formatRupiah(this.value, "Rp. ");
    });
}
if (hrg3) {
    hrg3.addEventListener("keyup", function (e) {
        hrg3.value = formatRupiah(this.value, "Rp. ");
    });
}
if (hrg4) {
    hrg4.addEventListener("keyup", function (e) {
        hrg4.value = formatRupiah(this.value, "Rp. ");
    });
}
if (hrg5) {
    hrg5.addEventListener("keyup", function (e) {
        hrg5.value = formatRupiah(this.value, "Rp. ");
    });
}
if (hrg6) {
    hrg6.addEventListener("keyup", function (e) {
        hrg6.value = formatRupiah(this.value, "Rp. ");
    });
}
if (hrg7) {
    hrg7.addEventListener("keyup", function (e) {
        hrg7.value = formatRupiah(this.value, "Rp. ");
    });
}
if (hrg8) {
    hrg8.addEventListener("keyup", function (e) {
        hrg8.value = formatRupiah(this.value, "Rp. ");
    });
}
if (hrg9) {
    hrg9.addEventListener("keyup", function (e) {
        hrg9.value = formatRupiah(this.value, "Rp. ");
    });
}
if (hrg10) {
    hrg10.addEventListener("keyup", function (e) {
        hrg10.value = formatRupiah(this.value, "Rp. ");
    });
}



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


function ubahRP(hrg, jual, laba, persen) {
    var jl = parseFloat(jual.replace(/[^\d]/g, ""));
    var hr = parseFloat(hrg.replace(/[^\d]/g, ""));
    hitungPersentase(hr, jl, laba, persen);
}

function hitungPersentase(hrg, jual, laba, pers) {
    var hrg1 = hrg;
    var jual1 = jual;
    var persen = ((jual1 - hrg1) / hrg1) * 100;
    var tlaba = jual - hrg;
    var tl = "Rp. " + tlaba.toLocaleString("id-ID");

    // Menampilkan hasil perhitungan di input "persen"
    $(laba).val(tl);
    $(pers).val(persen.toFixed(2) + "%");
}



$("#hrg1, #jumlah1").keyup(function () {
    var hrg1 = parseFloat($("#hrg1").val().replace("Rp. ", "").replace(/\./g, "")) || 0;
    var jumlah1 = parseFloat($("#jumlah1").val()) || 0;

    var total = hrg1 * jumlah1;

    var totalFormatted = "Rp. " + total.toLocaleString("id-ID");
    $("#total1").val(totalFormatted);
});

$("#hrg2, #jumlah2").keyup(function () {
    var hrg2 = parseFloat($("#hrg2").val().replace("Rp. ", "").replace(/\./g, "")) || 0;
    var jumlah2 = parseFloat($("#jumlah2").val()) || 0;

    var total = hrg2 * jumlah2;

    var totalFormatted = "Rp. " + total.toLocaleString("id-ID");
    $("#total2").val(totalFormatted);
});

$("#hrg3, #jumlah3").keyup(function () {
    var hrg3 = parseFloat($("#hrg3").val().replace("Rp. ", "").replace(/\./g, "")) || 0;
    var jumlah3 = parseFloat($("#jumlah3").val()) || 0;

    var total = hrg3 * jumlah3;

    var totalFormatted = "Rp. " + total.toLocaleString("id-ID");
    $("#total3").val(totalFormatted);
});
$("#hrg4, #jumlah4").keyup(function () {
    var hrg4 = parseFloat($("#hrg4").val().replace("Rp. ", "").replace(/\./g, "")) || 0;
    var jumlah4 = parseFloat($("#jumlah4").val()) || 0;

    var total = hrg4 * jumlah4;

    var totalFormatted = "Rp. " + total.toLocaleString("id-ID");
    $("#total4").val(totalFormatted);
});
$("#hrg5, #jumlah5").keyup(function () {
    var hrg5 = parseFloat($("#hrg5").val().replace("Rp. ", "").replace(/\./g, "")) || 0;
    var jumlah5 = parseFloat($("#jumlah5").val()) || 0;

    var total = hrg5 * jumlah5;

    var totalFormatted = "Rp. " + total.toLocaleString("id-ID");
    $("#total5").val(totalFormatted);
});
$("#hrg6, #jumlah6").keyup(function () {
    var hrg6 = parseFloat($("#hrg6").val().replace("Rp. ", "").replace(/\./g, "")) || 0;
    var jumlah6 = parseFloat($("#jumlah6").val()) || 0;

    var total = hrg6 * jumlah6;

    var totalFormatted = "Rp. " + total.toLocaleString("id-ID");
    $("#total6").val(totalFormatted);
});
$("#hrg7, #jumlah7").keyup(function () {
    var hrg7 = parseFloat($("#hrg7").val().replace("Rp. ", "").replace(/\./g, "")) || 0;
    var jumlah7 = parseFloat($("#jumlah7").val()) || 0;

    var total = hrg7 * jumlah7;

    var totalFormatted = "Rp. " + total.toLocaleString("id-ID");
    $("#total7").val(totalFormatted);
});
$("#hrg8, #jumlah8").keyup(function () {
    var hrg8 = parseFloat($("#hrg8").val().replace("Rp. ", "").replace(/\./g, "")) || 0;
    var jumlah8 = parseFloat($("#jumlah8").val()) || 0;

    var total = hrg8 * jumlah8;

    var totalFormatted = "Rp. " + total.toLocaleString("id-ID");
    $("#total8").val(totalFormatted);
});
$("#hrg9, #jumlah9").keyup(function () {
    var hrg9 = parseFloat($("#hrg9").val().replace("Rp. ", "").replace(/\./g, "")) || 0;
    var jumlah9 = parseFloat($("#jumlah9").val()) || 0;

    var total = hrg9 * jumlah9;

    var totalFormatted = "Rp. " + total.toLocaleString("id-ID");
    $("#total9").val(totalFormatted);
});

$("#hrg10, #jumlah10").keyup(function () {
    var hrg10 = parseFloat($("#hrg10").val().replace("Rp. ", "").replace(/\./g, "")) || 0;
    var jumlah10 = parseFloat($("#jumlah10").val()) || 0;

    var total = hrg10 * jumlah10;

    var totalFormatted = "Rp. " + total.toLocaleString("id-ID");
    $("#total10").val(totalFormatted);
});

