@extends('perhitungan.spreadsheet.menu_ajk')
@section('title', 'Simulasi AJK')

@section('content')
    <div class="tab-content">
        <div class="tab-pane active">
            <div class="box-body table-responsive" style="overflow: auto; width: 100%; height:425px;">
                <form action="#" method="get">
                    @csrf
                    <div class="card-body">

                        <table class="table table-vcenter">
                            <thead>
                                <tr>
                                    <th class="text-center fs-4" colspan="5" style="border: none;">
                                        <b>SIMULASI PERHITUNGAN PREMI ASURANSI TLO <i>(TOTAL LOST ONLY)</i></b>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="no-border">
                                        <span class="fw-bold fs-4">Masukan Nama</span>
                                        <input type="text" class="form-control" name="nama" id="name"
                                            placeholder="Masukan Nama" required>
                                    </td>
                                    <td class="no-border">
                                        <span class="fw-bold fs-4">Jenis Kendaraan</span>
                                        <select class="form-control input-sm form-border text-uppercase"
                                            style="border: 1px solid rgb(196, 196, 196); font-size: 14px; color: black;"
                                            name="jenis_kendaraan" id="">
                                            <option value="" selected>---Pilih---</option>
                                            <option value="SEPEDA MOTOR">SEPEDA MOTOR</option>
                                            <option value="MOBIL BEBAN">MOBIL BEBAN</option>
                                            <option value="MOBIL PENUMPANG">MOBIL PENUMPANG</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="no-border">
                                        <span class="fw-bold fs-4">Masukan No Polisi</span>
                                        <input type="text" class="form-control text-uppercase" name="nopol"
                                            placeholder="Masukan No Polisi" id="nopol" required>
                                    </td>
                                    <td class="no-border">
                                        <span class="fw-bold fs-4">Masukan Jangka Waktu</span>
                                        <input type="text" class="form-control text-uppercase" name="jw"
                                            placeholder="Masukan Jangka Waktu" id="jk" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="no-border">
                                        <span class="fw-bold fs-4">Nilai Pertanggungan</span>
                                        <input type="text" class="form-control" name="pertanggungan"
                                            placeholder="Masukan Nilai Pertanggungan" id="pertanggungan" required>
                                    </td>
                                    <td class="no-border">
                                        <span class="fw-bold fs-4">Tanggal Lahir
                                            (12-27-1994)</span>
                                        <input type="text" class="form-control" name="tgl_lahir"
                                            placeholder="Masukan Tanggal Lahir" id="tgllahir" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="no-border">
                                        <span class="fw-bold fs-4">Tanggal Realisasi</span>
                                        <input type="text" class="form-control" name="tgl_realisasi"
                                            placeholder="Tanggal Sekarang" id="hari">
                                    </td>
                                    <td class="no-border">
                                        <button type="submit" class="btn btn-primary" id="submit"
                                            style="margin-top: 20px; width:100%;">
                                            Hitung
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>

    </div>
    </div>
@endsection

@push('myscript')
    <script>
        var plafon = document.getElementById("pertanggungan");
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

        window.onload = function() {
            var today = new Date();
            var day = today.getDate();
            var month = today.getMonth() + 1; // Karena bulan dimulai dari 0 (Januari)
            var year = today.getFullYear();

            if (day < 10) {
                day = "0" + day; // Tambahkan "0" di depan jika kurang dari 10
            }
            if (month < 10) {
                month = "0" + month; // Tambahkan "0" di depan jika kurang dari 10
            }

            var currentDate = day + "-" + month + "-" + year;
            document.getElementById('hari').value = currentDate;
        };


        $(document).ready(function() {
            $('#submit').on('click', function() {
                var nama = $('#name').val();
                var kendaraan = $('#jenis_kendaraan').val();
                var nopol = $('#nopol').val();
                var jk = $('#jk').val();
                var pertanggungan = $('#pertanggungan').val();
                var tgllahir = $('#tgllahir').val();
                var hari = $('#hari').val();

                $.ajax({
                    url: "{{ route('perhitungan.tlo') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        nama: nama,
                        kendaraan: kendaraan,
                        nopol: nopol,
                        jk: jk,
                        pertanggungan: pertanggungan,
                        tgllahir: tgllahir,
                        hari: hari,
                        _token: "{{ csrf_token() }}",
                    },
                    success: function(response) {

                        console.log(response);
                    },
                    error: function(error) {
                        // Handle the error here
                        console.error('Error:', error);
                    }
                })
            })
        })
    </script>
@endpush
