//====ANALISA CHARACTER====//
var select1 = document.getElementById("select1");
var select2 = document.getElementById("select2");
var select3 = document.getElementById("select3");
var select4 = document.getElementById("select4");
var select5 = document.getElementById("select5");
var select6 = document.getElementById("select6");
var select7 = document.getElementById("select7");
var analisa1 = document.getElementById("n1");

select1.addEventListener("change", kalkulasi);
select2.addEventListener("change", kalkulasi);
select3.addEventListener("change", kalkulasi);
select4.addEventListener("change", kalkulasi);
select5.addEventListener("change", kalkulasi);
select6.addEventListener("change", kalkulasi);
select7.addEventListener("change", kalkulasi);

function kalkulasi() {
    // Mendapatkan nilai yang dipilih dari ketiga elemen select
    var selectedValue1 = parseInt(select1.value) || 0;
    var selectedValue2 = parseInt(select2.value) || 0;
    var selectedValue3 = parseInt(select3.value) || 0;
    var selectedValue4 = parseInt(select4.value) || 0;
    var selectedValue5 = parseInt(select5.value) || 0;
    var selectedValue6 = parseInt(select6.value) || 0;
    var selectedValue7 = parseInt(select7.value) || 0;

    // penjumlahan nilai perselect
    var jml =
        selectedValue1 +
        selectedValue2 +
        selectedValue3 +
        selectedValue4 +
        selectedValue5 +
        selectedValue6 +
        selectedValue7;

    if (jml === 0 || jml <= 7) {
        var nilai = "Kurang Baik";
    } else if (jml === 8 || jml <= 14) {
        var nilai = "Cukup Baik";
    } else if (jml === 15 || jml <= 21) {
        var nilai = "Baik";
    }

    // Menampilkan nilai yang digabungkan dalam elemen input
    analisa1.value = nilai;
    //====ANALISA CHARACTER====//

    //====ANALISA CAPACITY====//
    //====ANALISA CAPACITY====//
}
