@extends('perhitungan.spreadsheet.menu_ajk')
@section('title', 'Simulasi AJK')

@section('content')
    <div class="tab-content">
        <div class="tab-pane active">
            <div class="box-body table-responsive" style="overflow: auto; width: 100%; height:425px;">
                <form action="{{ route('perhitungan.tlo') }}" method="POST">
                    @csrf
                    <div class="card-body">

                        <table class="table table-vcenter">
                            <thead>
                                <tr>
                                    <th class="text-center fs-4" colspan="7" style="border: none;">
                                        <b>SIMULASI PERHITUNGAN PREMI ASURANSI TLO <i>(TOTAL LOST ONLY)</i></b>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="no-border">
                                        <span class="fw-bold fs-4">Masukan Nama</span>
                                        <input type="text" class="form-control text-uppercase" name="nama1"
                                            id="name" placeholder="Masukan Nama" required>
                                    </td>
                                    <td class="no-border">
                                        <span class="fw-bold fs-4">Jenis Kendaraan</span>
                                        <select class="form-control input-sm form-border text-uppercase"
                                            style="border: 1px solid rgb(196, 196, 196); font-size: 14px; color: black;"
                                            name="jenis_kendaraan1" id="" required>
                                            <option value="" selected>---Pilih---</option>
                                            <option value="SEPEDA MOTOR">SEPEDA MOTOR</option>
                                            <option value="MOBIL BEBAN">MOBIL BEBAN</option>
                                            <option value="MOBIL PENUMPANG">MOBIL PENUMPANG</option>
                                        </select>
                                    </td>
                                    <td class="no-border">
                                        <span class="fw-bold fs-4">Masukan No Polisi</span>
                                        <input type="text" class="form-control text-uppercase" name="nopol1"
                                            placeholder="No Polisi" id="nopol" required>
                                    </td>
                                    <td class="no-border">
                                        <span class="fw-bold fs-4">Jangka Waktu</span>
                                        <input type="text" class="form-control text-uppercase" name="jw1"
                                            placeholder="Jangka Waktu" id="jk" required>
                                    </td>
                                    <td class="no-border">
                                        <span class="fw-bold fs-4">Nilai Pertanggungan</span>
                                        <input type="text" class="form-control" name="pertanggungan1"
                                            placeholder="Nilai Pertanggungan" id="pertanggungan1" required>
                                    </td>
                                    <td class="no-border">
                                        <span class="fw-bold fs-4">Tanggal Realisasi</span>
                                        <input type="text" class="form-control" name="tgl_realisasi1"
                                            placeholder="Tanggal Sekarang" id="hari1" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="no-border">
                                        <span class="fw-bold fs-4">Masukan Nama</span>
                                        <input type="text" class="form-control" name="nama2" id="name"
                                            placeholder="Masukan Nama">
                                    </td>
                                    <td class="no-border">
                                        <span class="fw-bold fs-4">Jenis Kendaraan</span>
                                        <select class="form-control input-sm form-border text-uppercase"
                                            style="border: 1px solid rgb(196, 196, 196); font-size: 14px; color: black;"
                                            name="jenis_kendaraan2" id="">
                                            <option value="" selected>---Pilih---</option>
                                            <option value="SEPEDA MOTOR">SEPEDA MOTOR</option>
                                            <option value="MOBIL BEBAN">MOBIL BEBAN</option>
                                            <option value="MOBIL PENUMPANG">MOBIL PENUMPANG</option>
                                        </select>
                                    </td>
                                    <td class="no-border">
                                        <span class="fw-bold fs-4">Masukan No Polisi</span>
                                        <input type="text" class="form-control text-uppercase" name="nopol2"
                                            placeholder="No Polisi" id="nopol">
                                    </td>
                                    <td class="no-border">
                                        <span class="fw-bold fs-4">Jangka Waktu</span>
                                        <input type="text" class="form-control text-uppercase" name="jw2"
                                            placeholder="Jangka Waktu" id="jk">
                                    </td>
                                    <td class="no-border">
                                        <span class="fw-bold fs-4">Nilai Pertanggungan</span>
                                        <input type="text" class="form-control" name="pertanggungan2"
                                            placeholder="Nilai Pertanggungan" id="pertanggungan2">
                                    </td>
                                    <td class="no-border">
                                        <span class="fw-bold fs-4">Tanggal Realisasi</span>
                                        <input type="text" class="form-control" name="tgl_realisasi2"
                                            placeholder="Tanggal Sekarang" id="hari2">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="no-border">
                                        <span class="fw-bold fs-4">Masukan Nama</span>
                                        <input type="text" class="form-control" name="nama3" id="name"
                                            placeholder="Masukan Nama">
                                    </td>
                                    <td class="no-border">
                                        <span class="fw-bold fs-4">Jenis Kendaraan</span>
                                        <select class="form-control input-sm form-border text-uppercase"
                                            style="border: 1px solid rgb(196, 196, 196); font-size: 14px; color: black;"
                                            name="jenis_kendaraan3" id="">
                                            <option value="" selected>---Pilih---</option>
                                            <option value="SEPEDA MOTOR">SEPEDA MOTOR</option>
                                            <option value="MOBIL BEBAN">MOBIL BEBAN</option>
                                            <option value="MOBIL PENUMPANG">MOBIL PENUMPANG</option>
                                        </select>
                                    </td>
                                    <td class="no-border">
                                        <span class="fw-bold fs-4">Masukan No Polisi</span>
                                        <input type="text" class="form-control text-uppercase" name="nopol3"
                                            placeholder="No Polisi" id="nopol">
                                    </td>
                                    <td class="no-border">
                                        <span class="fw-bold fs-4">Jangka Waktu</span>
                                        <input type="text" class="form-control text-uppercase" name="jw3"
                                            placeholder="Jangka Waktu" id="jk">
                                    </td>
                                    <td class="no-border">
                                        <span class="fw-bold fs-4">Nilai Pertanggungan</span>
                                        <input type="text" class="form-control" name="pertanggungan3"
                                            placeholder="Nilai Pertanggungan" id="pertanggungan3">
                                    </td>
                                    <td class="no-border">
                                        <span class="fw-bold fs-4">Tanggal Realisasi</span>
                                        <input type="text" class="form-control" name="tgl_realisasi3"
                                            placeholder="Tanggal Sekarang" id="hari3">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="no-border">
                                        <span class="fw-bold fs-4">Masukan Nama</span>
                                        <input type="text" class="form-control" name="nama4" id="name"
                                            placeholder="Masukan Nama">
                                    </td>
                                    <td class="no-border">
                                        <span class="fw-bold fs-4">Jenis Kendaraan</span>
                                        <select class="form-control input-sm form-border text-uppercase"
                                            style="border: 1px solid rgb(196, 196, 196); font-size: 14px; color: black;"
                                            name="jenis_kendaraan4" id="">
                                            <option value="" selected>---Pilih---</option>
                                            <option value="SEPEDA MOTOR">SEPEDA MOTOR</option>
                                            <option value="MOBIL BEBAN">MOBIL BEBAN</option>
                                            <option value="MOBIL PENUMPANG">MOBIL PENUMPANG</option>
                                        </select>
                                    </td>
                                    <td class="no-border">
                                        <span class="fw-bold fs-4">Masukan No Polisi</span>
                                        <input type="text" class="form-control text-uppercase" name="nopol4"
                                            placeholder="No Polisi" id="nopol">
                                    </td>
                                    <td class="no-border">
                                        <span class="fw-bold fs-4">Jangka Waktu</span>
                                        <input type="text" class="form-control text-uppercase" name="jw4"
                                            placeholder="Jangka Waktu" id="jk">
                                    </td>
                                    <td class="no-border">
                                        <span class="fw-bold fs-4">Nilai Pertanggungan</span>
                                        <input type="text" class="form-control" name="pertanggungan4"
                                            placeholder="Nilai Pertanggungan" id="pertanggungan4">
                                    </td>
                                    <td class="no-border">
                                        <span class="fw-bold fs-4">Tanggal Realisasi</span>
                                        <input type="text" class="form-control" name="tgl_realisasi4"
                                            placeholder="Tanggal Sekarang" id="hari4">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="no-border">
                                        <span class="fw-bold fs-4">Masukan Nama</span>
                                        <input type="text" class="form-control" name="nama5" id="name"
                                            placeholder="Masukan Nama">
                                    </td>
                                    <td class="no-border">
                                        <span class="fw-bold fs-4">Jenis Kendaraan</span>
                                        <select class="form-control input-sm form-border text-uppercase"
                                            style="border: 1px solid rgb(196, 196, 196); font-size: 14px; color: black;"
                                            name="jenis_kendaraan5" id="">
                                            <option value="" selected>---Pilih---</option>
                                            <option value="SEPEDA MOTOR">SEPEDA MOTOR</option>
                                            <option value="MOBIL BEBAN">MOBIL BEBAN</option>
                                            <option value="MOBIL PENUMPANG">MOBIL PENUMPANG</option>
                                        </select>
                                    </td>
                                    <td class="no-border">
                                        <span class="fw-bold fs-4">Masukan No Polisi</span>
                                        <input type="text" class="form-control text-uppercase" name="nopol5"
                                            placeholder="No Polisi" id="nopol">
                                    </td>
                                    <td class="no-border">
                                        <span class="fw-bold fs-4">Jangka Waktu</span>
                                        <input type="text" class="form-control text-uppercase" name="jw5"
                                            placeholder="Jangka Waktu" id="jk">
                                    </td>
                                    <td class="no-border">
                                        <span class="fw-bold fs-4">Nilai Pertanggungan</span>
                                        <input type="text" class="form-control" name="pertanggungan5"
                                            placeholder="Nilai Pertanggungan" id="pertanggungan5">
                                    </td>
                                    <td class="no-border">
                                        <span class="fw-bold fs-4">Tanggal Realisasi</span>
                                        <input type="text" class="form-control" name="tgl_realisasi5"
                                            placeholder="Tanggal Sekarang" id="hari5">
                                    </td>
                                </tr>
                                <tr>

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
        var pertanggungan1 = document.getElementById("pertanggungan1");
        var pertanggungan2 = document.getElementById("pertanggungan2");
        var pertanggungan3 = document.getElementById("pertanggungan3");
        var pertanggungan4 = document.getElementById("pertanggungan4");
        var pertanggungan5 = document.getElementById("pertanggungan5");

        if (pertanggungan1) {
            pertanggungan1.addEventListener("keyup", function(e) {
                pertanggungan1.value = formatRupiah(this.value, "Rp. ");
            });
        }
        if (pertanggungan2) {
            pertanggungan2.addEventListener("keyup", function(e) {
                pertanggungan2.value = formatRupiah(this.value, "Rp. ");
            });
        }
        if (pertanggungan3) {
            pertanggungan3.addEventListener("keyup", function(e) {
                pertanggungan3.value = formatRupiah(this.value, "Rp. ");
            });
        }
        if (pertanggungan4) {
            pertanggungan4.addEventListener("keyup", function(e) {
                pertanggungan4.value = formatRupiah(this.value, "Rp. ");
            });
        }
        if (pertanggungan5) {
            pertanggungan5.addEventListener("keyup", function(e) {
                pertanggungan5.value = formatRupiah(this.value, "Rp. ");
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
            return prefix == undefined ? rupiah : rupiah ? rupiah : "";
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
            document.getElementById('hari1').value = currentDate;
            document.getElementById('hari2').value = currentDate;
            document.getElementById('hari3').value = currentDate;
            document.getElementById('hari4').value = currentDate;
            document.getElementById('hari5').value = currentDate;
        };


        // $(document).ready(function() {
        //     $('#submit').on('click', function() {
        //         var nama = $('#name').val();
        //         var kendaraan = $('#jenis_kendaraan').val();
        //         var nopol = $('#nopol').val();
        //         var jk = $('#jk').val();
        //         var pertanggungan = $('#pertanggungan').val();
        //         var tgllahir = $('#tgllahir').val();
        //         var hari = $('#hari').val();

        //         $.ajax({
        //             url: "{{ route('perhitungan.tlo') }}",
        //             type: 'POST',
        //             dataType: 'json',
        //             data: {
        //                 nama: nama,
        //                 kendaraan: kendaraan,
        //                 nopol: nopol,
        //                 jk: jk,
        //                 pertanggungan: pertanggungan,
        //                 tgllahir: tgllahir,
        //                 hari: hari,
        //                 _token: "{{ csrf_token() }}",
        //             },
        //             success: function(response) {

        //                 console.log(response);
        //             },
        //             error: function(error) {
        //                 // Handle the error here
        //                 console.error('Error:', error);
        //             }
        //         })
        //     })
        // })
    </script>
@endpush
