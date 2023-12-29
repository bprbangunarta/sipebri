var select1 = document.getElementById("provisi");
var select2 = document.getElementById("materai");
var select3 = document.getElementById("asuransi_jiwa_menurun1");
var select4 = document.getElementById("asuransi_jiwa_menurun2");
var select5 = document.getElementById("asuransi_jiwa_menurun3");
var select6 = document.getElementById("asuransi_jiwa_tetap1");
var select7 = document.getElementById("asuransi_jiwa_tetap2");
var select8 = document.getElementById("asuransi_jiwa");
var select9 = document.getElementById("asuransi_kendaraan_motor");
var select10 = document.getElementById("transaksi_kredit");
var select11 = document.getElementById("proses_shm");
var select12 = document.getElementById("polis_materai");
var select13 = document.getElementById("pajak_stnk");
var select14 = document.getElementById("proses_apht");
var select15 = document.getElementById("lainnya");
var select16 = document.getElementById("administrasi");
var select17 = document.getElementById("by_fiducia");

if (select1) {
    select1.addEventListener("keyup", function (e) {
        select1.value = formatRupiah(this.value, "Rp. ");
    });
}
if (select2) {
    select2.addEventListener("keyup", function (e) {
        select2.value = formatRupiah(this.value, "Rp. ");
    });
}
if (select3) {
    select3.addEventListener("keyup", function (e) {
        select3.value = formatRupiah(this.value, "Rp. ");
    });
}
if (select4) {
    select4.addEventListener("keyup", function (e) {
        select4.value = formatRupiah(this.value, "Rp. ");
    });
}
if (select5) {
    select5.addEventListener("keyup", function (e) {
        select5.value = formatRupiah(this.value, "Rp. ");
    });
}
if (select6) {
    select6.addEventListener("keyup", function (e) {
        select6.value = formatRupiah(this.value, "Rp. ");
    });
}
if (select7) {
    select7.addEventListener("keyup", function (e) {
        select7.value = formatRupiah(this.value, "Rp. ");
    });
}
if (select8) {
    select8.addEventListener("keyup", function (e) {
        select8.value = formatRupiah(this.value, "Rp. ");
    });
}
if (select9) {
    select9.addEventListener("keyup", function (e) {
        select9.value = formatRupiah(this.value, "Rp. ");
    });
}
if (select10) {
    select10.addEventListener("keyup", function (e) {
        select10.value = formatRupiah(this.value, "Rp. ");
    });
}
if (select11) {
    select11.addEventListener("keyup", function (e) {
        select11.value = formatRupiah(this.value, "Rp. ");
    });
}
if (select12) {
    select12.addEventListener("keyup", function (e) {
        select12.value = formatRupiah(this.value, "Rp. ");
    });
}
if (select13) {
    select13.addEventListener("keyup", function (e) {
        select13.value = formatRupiah(this.value, "Rp. ");
    });
}
if (select14) {
    select14.addEventListener("keyup", function (e) {
        select14.value = formatRupiah(this.value, "Rp. ");
    });
}
if (select15) {
    select15.addEventListener("keyup", function (e) {
        select15.value = formatRupiah(this.value, "Rp. ");
    });
}
if (select16) {
    select16.addEventListener("keyup", function (e) {
        select16.value = formatRupiah(this.value, "Rp. ");
    });
}
if (select17) {
    select17.addEventListener("keyup", function (e) {
        select17.value = formatRupiah(this.value, "Rp. ");
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

// $(document).ready(function(){
//     $('#proses_apht, #by_fiducia').on('input', function(){
//         var apht = $('#proses_apht').val()
//         var fiducia = $('#by_fiducia').val()
//         // let adm = document.getElementsByName('administrasi');
//         var adm = $('input[name="administrasi"]');
//         var rapht = parseFloat(apht.replace(/[^\d]/g, "")) || 0;
//         var rfiducia = parseFloat(fiducia.replace(/[^\d]/g, "")) || 0;
//         var radmin = parseFloat(adm.val().replace(/[^\d]/g, "")) || 0;
        
//         var jml = radmin - rapht + rfiducia;
        
//         var hasil = "Rp " + jml.toLocaleString("id-ID");
//         adm.val(hasil);
//         console.log(jml)
//     })
// })