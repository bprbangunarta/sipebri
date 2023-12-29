@extends('perhitungan.spreadsheet.menu_ajk')
@section('title', 'Simulasi AJK')

@section('content')
    <div class="tab-content">
        <div class="tab-pane active">
            <div class="box-body table-responsive" style="overflow: auto; width: 100%; height:425px;">
                <form action="{{ route('premi') }}" method="get">
                    @csrf
                    <div class="card-body">

                        <table class="table table-vcenter">
                            <thead>
                                <tr>
                                    <th class="text-center fs-4" colspan="5" style="border: none;">
                                        <b>SIMULASI PERHITUNGAN PREMI ASURANSI JIWA
                                            KREDIT</b>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="no-border">
                                        <span class="fw-bold fs-4">Masukan Nama</span>
                                        <input type="text" class="form-control" name="nama" id="nama" required>
                                    </td>
                                    <td class="no-border">
                                        <span class="fw-bold fs-4">Masukan Plafon</span>
                                        <input type="text" class="form-control" name="plafon" id="plafons" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="no-border">
                                        <span class="fw-bold fs-4">Pilih Metode RPS</span>
                                        <select class="form-control input-sm form-border"
                                            style="border: 1px solid rgb(196, 196, 196); font-size: 14px; color: black;"
                                            name="rps" id="">
                                            <option value="" selected>---Pilih---</option>
                                            <option value="FLAT">FLAT</option>
                                            <option value="ANUITAS">ANUITAS</option>
                                            <option value="EFEKTIF">EFEKTIF</option>
                                            <option value="EFEKTIF MUSIMAN">EFEKTIF MUSIMAN
                                            </option>
                                            <option value="REKENING KORAN">REKENING KORAN
                                            </option>
                                        </select>
                                    </td>
                                    <td class="no-border">
                                        <span class="fw-bold fs-4">Pilih Produk</span>
                                        <select class="form-control input-sm form-border"
                                            style="border: 1px solid rgb(196, 196, 196); font-size: 14px; color: black;"
                                            name="produk" id="">
                                            <option value="" selected>---Pilih---</option>
                                            <option value="KRU">KRU</option>
                                            <option value="KUP">KUP</option>
                                            <option value="KM">KM</option>
                                            <option value="PRK">PRK</option>
                                            <option value="KTO">KTO</option>
                                            <option value="KBT">KBT</option>
                                            <option value="KPS">KPS</option>
                                            <option value="KIH">KIH</option>
                                            <option value="KPJ">KPJ</option>
                                            <option value="KRS">KRS</option>
                                            <option value="KPN">KPN</option>
                                            <option value="UMROH">UMROH</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="no-border">
                                        <span class="fw-bold fs-4">Masukan Jangka Waktu</span>
                                        <input type="text" class="form-control" name="jw"
                                            placeholder="Masukan Jangka Waktu" id="jk" required>
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
                                        <button type="submit" class="btn btn-primary"
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
    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> --}}
    <script type='text/javascript'>
        var plafon = document.getElementById("plafons");
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



        document.addEventListener("DOMContentLoaded", function() {
            window.Litepicker && (new Litepicker({
                element: document.getElementById('tgllahir'),
                format: 'DD-MM-YYYY',
                buttonText: {
                    previousMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-left -->
    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 6l-6 6l6 6" /></svg>`,
                    nextMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-right -->
    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg>`,
                },
            }));
        });
    </script>
@endpush
