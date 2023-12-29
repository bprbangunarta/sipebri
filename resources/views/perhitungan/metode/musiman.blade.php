@extends('perhitungan.metode.menu')
@section('title', 'Perhitungan Efektif Musiman')

@section('content')
    <div class="tab-content">
        <div class="tab-pane active">

            <form action="#" method="post">
                @csrf
                <div class="box-body table-responsive" style="overflow: auto; width: 100%; height:425px;">
                    <form action="#" method="get" id="flat">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center fs-2" colspan="5">
                                        <span class="fw-bold" style="font-size: 16px;">PERHITUNGAN
                                            EFEKTIF MUSIMAN</span>
                                    </th>
                                </tr>
                            </thead>
                            <thead>
                                <tr>
                                    <td>
                                        <span class="fw-bold fs-4">PLAFON</span>
                                        <input type="text" class="form-control" name="plafon"
                                            placeholder="Masukan Plafon" id="plafon">
                                    </td>
                                    <td>
                                        <span class="fw-bold">JANGKA WAKTU</span>
                                        <input type="text" class="form-control" name="plafon"
                                            placeholder="Masukan Jangka Waktu" id="jw">
                                    </td>
                                    <td>
                                        <span class="fw-bold">SUKU BUNGA</span>
                                        <input type="text" class="form-control" name="plafon"
                                            placeholder="Masukan Bunga Pinjaman" id="bunga">
                                    </td>
                                    <td class="d-flex">
                                        <button type="submit" class="btn btn-primary"
                                            style="float: right; margin-top: 19px" id="hitung">
                                            Hitung
                                        </button>
                                    </td>
                                </tr>
                            </thead>
                        </table>
                    </form>
                    <table class="table table-bordered table-vcenter fs-5" id="hasil_flat">
                        <thead>
                            <tr class="bg-primary">
                                <th class="text-center">No</th>
                                <th class="text-center">Tanggal</th>
                                <th class="text-center">Sisa Kredit</th>
                                <th class="text-center">Tagihan Poko</th>
                                <th class="text-center">Tagihan Bunga</th>
                                <th class="text-center">Total Angsuran</th>
                                <th class="text-center">Rate</th>
                            </tr>
                        </thead>
                        <tbody id="data">

                        </tbody>
                    </table>
                </div>
            </form>
        </div>

    </div>
    </div>
@endsection
@push('myscript')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script type='text/javascript'>
        var plafon = document.getElementById("plafon");
        if (plafon) {
            plafon.addEventListener("keyup", function(e) {
                plafon.value = formatRupiah(this.value, "Rp. ");
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


        $(document).ready(function() {
            // Ketika form di-submit
            $("#hitung").click(function(e) {
                e.preventDefault(); // Mencegah pengiriman form
                var plafon = $("#plafon").val();
                var waktu = $("#jw").val();
                var bunga = $("#bunga").val();
                var tbody = $('#data')

                var rplafon = parseFloat(plafon.replace(/[^\d]/g, "")) || 0;
                var tplapon = rplafon
                var jangka = waktu / 6
                var b = rplafon / jangka


                var hpokok = b;

                var p = 0
                var bulan = 1;

                var hariini = new Date();
                var bulansekarang = hariini.getMonth() + 1;
                var tahunsekarang = hariini.getFullYear();
                var tglskrng = hariini.getDate();
                var idMonths = [
                    "Januari", "Februari", "Maret", "April", "Mei", "Juni",
                    "Juli", "Agustus", "September", "Oktober", "November", "Desember"
                ];
                tbody.empty();

                var total_bunga = 0;
                for (var i = 0; i < waktu; i++) {
                    // Menghitung bulan target
                    var tahunsekarang = tahunsekarang;
                    var bulanberikut = bulansekarang + i
                    var plp = rplafon

                    while (bulanberikut > 12) {
                        bulanberikut = bulanberikut - 12
                    }

                    // Penyesuaian bulan jika tahun bertambah
                    if (bulanberikut == 1) {
                        tahunsekarang++;
                    }

                    var jmlhari = new Date(tahunsekarang, bulanberikut, 0).getDate();

                    // Mendapatkan nama bulan sesuai dengan indeks bulan
                    var namaBulan = idMonths[bulanberikut - 1];

                    // Mendapatkan tanggal dengan bulan dan tahun yang sesuai
                    var formattedDate = tglskrng + " " + namaBulan + " " + tahunsekarang;

                    // Menghitung jumlah hari dalam bulan
                    var a = (((plp * bunga) / 100) * jmlhari) / 365;
                    var hbunga = a;
                    var angsuran = hbunga + hpokok
                    var indek = 6

                    if ((i + 1) % indek == 0) {
                        var row = "<tr><td>" + (i + 1) + "</td><td>" + formattedDate + "</td><td>" +
                            "Rp. " +
                            plp.toLocaleString("id-ID", {
                                maximumFractionDigits: 0
                            }) + "</td><td>" +
                            "Rp. " + b.toLocaleString("id-ID", {
                                maximumFractionDigits: 0
                            }) + "</td><td>" + "Rp. " + hbunga
                            .toLocaleString(
                                "id-ID", {
                                    maximumFractionDigits: 0
                                }) +
                            "</td><td>" + "Rp. " + angsuran.toLocaleString("id-ID", {
                                maximumFractionDigits: 0
                            }) +
                            "</td><td>" + bunga.toLocaleString("id-ID", {
                                maximumFractionDigits: 0
                            }) + " " + "%" +
                            "</td></tr>";
                        tbody.append(row);
                        rplafon -= hpokok;
                    } else {
                        var row = "<tr><td>" + (i + 1) + "</td><td>" + formattedDate + "</td><td>" +
                            "Rp. " +
                            plp.toLocaleString("id-ID", {
                                maximumFractionDigits: 0
                            }) + "</td><td>" +
                            "Rp. " + p.toLocaleString("id-ID", {
                                maximumFractionDigits: 0
                            }) + "</td><td>" + "Rp. " + hbunga
                            .toLocaleString(
                                "id-ID", {
                                    maximumFractionDigits: 0
                                }) +
                            "</td><td>" + "Rp. " + hbunga.toLocaleString("id-ID", {
                                maximumFractionDigits: 0
                            }) +
                            "</td><td>" + bunga.toLocaleString("id-ID", {
                                maximumFractionDigits: 0
                            }) + " " + "%" +
                            "</td></tr>";
                        tbody.append(row);
                    }
                    total_bunga += a
                }

                var tpl = tplapon + total_bunga
                var row2 = "<tr><td colspan='2'>" + "</td><td colspan='2'><b>" + "Total :" +
                    " " + "Rp. " + tpl.toLocaleString(
                        "id-ID", {
                            maximumFractionDigits: 0
                        }) + "</b></td><td colspan='3'><b>" + "Total Bunga :" + " " + "Rp. " + total_bunga
                    .toLocaleString(
                        "id-ID", {
                            maximumFractionDigits: 0
                        }) +
                    "</b></td></tr>";
                tbody.append(row2);

            });
        });
    </script>
@endpush
