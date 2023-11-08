$(document).ready(function () {
    $("#modal-catatan").on("show.bs.modal", function (event) {
        $("#staff_analis").empty();
        $("#kasi_analiss").empty();
        $("#kabag_analis").empty();
        $("#direksi").empty();

        var button = $(event.relatedTarget);
        var pengajuan = button.data("pengajuan");
        
        $('#kd_pengajuan').val(pengajuan)
        $.ajax({
            url: "/themes/komite/kredit/catatan/" + pengajuan,
            type: "get",
            dataType: "json",
            cache: false,
            success: function (response) {
              
                if (response[0]) {
                    var usulan1 = response[0].usulan_plafon ?? null
                    var text = "USULAN : " + " " +"Rp. "+ formatRupiah(usulan1)+" "+"-"+" "+"METODE : "+" "+ response[0].metode_rps+"\n"+
                                "B. PROVISI : "+" "+ response[0].b_provisi+" "+"-"+" "+"B. ADMIN : "+" "+ response[0].b_admin+"\n"+
                                "CATATAN : "+" "+ response[0].catatan
                    $("#staff_analis").val(text);
                }else{
                    $("#staff_analis").val('Tidak Ada Catatan');
                }

                if(response[1]) {
                    var usulan2 = response[1].usulan_plafon ?? null
                    var text2 = "USULAN : " + " " +"Rp. "+ formatRupiah(usulan2)+" "+"-"+" "+"METODE : "+" "+ response[1].metode_rps+"\n"+
                                "B. PROVISI : "+" "+ response[1].b_provisi+" "+"-"+" "+"B. ADMIN : "+" "+ response[1].b_admin+"\n"+
                                "CATATAN : "+" "+ response[1].catatan
                    $("#kasi_analiss").val(text2);
                }else{
                    $("#kasi_analiss").val('Tidak Ada Catatan');
                }

                if(response[2]) {
                    var usulan3 = response[2].usulan_plafon ?? null
                    var text3 = "USULAN : " + " " +"Rp. "+ formatRupiah(usulan3)+" "+"-"+" "+"METODE : "+" "+ response[2].metode_rps+"\n"+
                                "B. PROVISI : "+" "+ response[2].b_provisi+" "+"-"+" "+"B. ADMIN : "+" "+ response[2].b_admin+"\n"+
                                "CATATAN : "+" "+ response[2].catatan
                    $("#kabag_analis").val(text3);
                }else{
                    $("#kabag_analis").val('Tidak Ada Catatan');
                }

                if(response[3]) {
                    var usulan4 = response[3].usulan_plafon ?? null
                    var text4 = "USULAN : " + " " +"Rp. "+ formatRupiah(usulan4)+" "+"-"+" "+"METODE : "+" "+ response[3].metode_rps+"\n"+
                                "B. PROVISI : "+" "+ response[3].b_provisi+" "+"-"+" "+"B. ADMIN : "+" "+ response[3].b_admin+"\n"+
                                "CATATAN : "+" "+ response[3].catatan
                    $("#direksi").val(text4);
                }else{
                    $("#direksi").val('Tidak Ada Catatan');
                }

            },
            error: function (xhr, status, error) {
                // Tindakan jika terjadi kesalahan dalam permintaan AJAX
                console.error("Error:", xhr.responseText);
            },
        });
    });
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
