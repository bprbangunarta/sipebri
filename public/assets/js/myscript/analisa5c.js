//====ANALISA CHARACTER====//
var select1 = document.getElementById("select1");
var select2 = document.getElementById("select2");
var select3 = document.getElementById("select3");
var select4 = document.getElementById("select4");
var select5 = document.getElementById("select5");
var select6 = document.getElementById("select6");
var select7 = document.getElementById("select7");
var analisa1 = document.getElementById("n1");

select1.addEventListener("change", character);
select2.addEventListener("change", character);
select3.addEventListener("change", character);
select4.addEventListener("change", character);
select5.addEventListener("change", character);
select6.addEventListener("change", character);
select7.addEventListener("change", character);

function character() {
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
}

//====ANALISA CAPACITY====//
var capacity1 = document.getElementById("capacity1");
var capacity2 = document.getElementById("capacity2");
var capacity3 = document.getElementById("capacity3");
var capacity4 = document.getElementById("capacity4");
var capacity5 = document.getElementById("capacity5");
var capacity6 = document.getElementById("capacity6");
var capacity7 = document.getElementById("capacity7");
var capacity8 = document.getElementById("capacity8");
var analisa2 = document.getElementById("evaluasi_capacity");

capacity1.addEventListener("change", capacity);
capacity2.addEventListener("change", capacity);
capacity3.addEventListener("change", capacity);
capacity4.addEventListener("change", capacity);
capacity5.addEventListener("change", capacity);
capacity6.addEventListener("change", capacity);
capacity7.addEventListener("change", capacity);
capacity8.addEventListener("change", capacity);

function capacity() {
    // Mendapatkan nilai yang dipilih dari ketiga elemen select
    var selectedcapacity1 = parseInt(capacity1.value) || 0;
    var selectedcapacity2 = parseInt(capacity2.value) || 0;
    var selectedcapacity3 = parseInt(capacity3.value) || 0;
    var selectedcapacity4 = parseInt(capacity4.value) || 0;
    var selectedcapacity5 = parseInt(capacity5.value) || 0;
    var selectedcapacity6 = parseInt(capacity6.value) || 0;
    var selectedcapacity7 = parseInt(capacity7.value) || 0;
    var selectedcapacity8 = parseInt(capacity8.value) || 0;

    // penjumlahan nilai perselect
    var jml =
        selectedcapacity1 +
        selectedcapacity2 +
        selectedcapacity3 +
        selectedcapacity4 +
        selectedcapacity5 +
        selectedcapacity6 +
        selectedcapacity7 +
        selectedcapacity8;

    if (jml === 0 || jml <= 8) {
        var nilai = "Kurang Baik";
    } else if (jml === 9 || jml <= 16) {
        var nilai = "Cukup Baik";
    } else if (jml === 17 || jml <= 27) {
        var nilai = "Baik";
    }

    // Menampilkan nilai yang digabungkan dalam elemen input
    analisa2.value = nilai;
}
//====ANALISA CAPACITY====//

//====ANALISA CAPITAL====//
var capital = document.getElementById("capital");
var analisa3 = document.getElementById("evaluasi_capital");

capital.addEventListener("change", capitals);
function capitals() {
    var selectedcapital = parseInt(capital.value) || 0;

    if (selectedcapital === 0 || selectedcapital == 1) {
        var nilai = "Kurang Baik";
    } else if (selectedcapital === 9 || selectedcapital == 2) {
        var nilai = "Cukup Baik";
    } else if (selectedcapital === 17 || selectedcapital == 3) {
        var nilai = "Baik";
    }
    analisa3.value = nilai;
}
//====ANALISA CAPITAL====//

//====ANALISA COLLATERAL====//
var collateral1 = document.getElementById("collateral1");
var collateral2 = document.getElementById("collateral2");
var collateral3 = document.getElementById("collateral3");
var collateral4 = document.getElementById("collateral4");
var collateral5 = document.getElementById("collateral5");
var collateral6 = document.getElementById("collateral6");
var collateral7 = document.getElementById("collateral7");
var collateral8 = document.getElementById("collateral8");
var collateral9 = document.getElementById("collateral9");
var analisa4 = document.getElementById("evaluasi_collateral");

collateral1.addEventListener("change", collateral);
collateral2.addEventListener("change", collateral);
collateral3.addEventListener("change", collateral);
collateral4.addEventListener("change", collateral);
collateral5.addEventListener("change", collateral);
collateral6.addEventListener("change", collateral);
collateral7.addEventListener("change", collateral);
collateral8.addEventListener("change", collateral);
collateral9.addEventListener("change", collateral);

function collateral() {
    var selectedcollateral1 = parseInt(collateral1.value) || 0;
    var selectedcollateral2 = parseInt(collateral2.value) || 0;
    var selectedcollateral3 = parseInt(collateral3.value) || 0;
    var selectedcollateral4 = parseInt(collateral4.value) || 0;
    var selectedcollateral5 = parseInt(collateral5.value) || 0;
    var selectedcollateral6 = parseInt(collateral6.value) || 0;
    var selectedcollateral7 = parseInt(collateral7.value) || 0;
    var selectedcollateral8 = parseInt(collateral8.value) || 0;
    var selectedcollateral9 = parseInt(collateral9.value) || 0;

    var jml =
        selectedcollateral1 +
        selectedcollateral2 +
        selectedcollateral3 +
        selectedcollateral4 +
        selectedcollateral5 +
        selectedcollateral6 +
        selectedcollateral7 +
        selectedcollateral8 +
        selectedcollateral9;

    if (jml === 0 || jml <= 9) {
        var nilai = "Kurang Baik";
    } else if (jml === 10 || jml <= 18) {
        var nilai = "Cukup Baik";
    } else if (jml === 19 || jml <= 27) {
        var nilai = "Baik";
    }

    analisa4.value = nilai;
}
//====ANALISA COLLATERAL====//

//====ANALISA CONDITION====//
var condition1 = document.getElementById("condition1");
var condition2 = document.getElementById("condition2");
var condition3 = document.getElementById("condition3");
var analisa5 = document.getElementById("evaluasi_condition");

condition1.addEventListener("change", condition);
condition2.addEventListener("change", condition);
condition3.addEventListener("change", condition);

function condition() {
    var selectedcondition1 = parseInt(condition1.value) || 0;
    var selectedcondition2 = parseInt(condition2.value) || 0;
    var selectedcondition3 = parseInt(condition3.value) || 0;

    var jml = selectedcondition1 + selectedcondition2 + selectedcondition3;

    if (jml === 0 || jml <= 4) {
        var nilai = "Kurang Baik";
    } else if (jml === 5 || jml <= 8) {
        var nilai = "Cukup Baik";
    } else if (jml === 9 || jml <= 12) {
        var nilai = "Baik";
    }
    analisa5.value = nilai;
}
//====ANALISA CONDITION====//
