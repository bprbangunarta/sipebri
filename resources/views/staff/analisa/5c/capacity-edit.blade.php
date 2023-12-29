@extends('staff.analisa.5c.menu', [$data, 'pengajuan' => $data->kd_pengajuan])
@section('title', 'Analisa Karakter')

@section('content')
    <div class="tab-content">
        <div class="tab-pane active">

            <form action="{{ route('analisa5c.updatecapacity', ['pengajuan' => $data->kd_pengajuan]) }}" method="POST">
                @method('put')
                @csrf
                <div class="box-body" style="margin-top: -10px;font-size:12px;">

                    <div class="div-left">
                        <div style="width: 49.5%;float:left;">
                            <span class="fw-bold">KONTINUITAS USAHA</span>
                            <input type="text" name="kode_analisa" value="{{ $capacity->kode_analisa }}" hidden>
                            <select class="form-control input-sm form-border text-uppercase" name="kontinuitas"
                                id="capacity1" required>
                                <option value="">--Pilih--</option>
                                <option value="1"
                                    {{ old('kontinuitas') == 1 || $capacity->kontinuitas == '1' ? 'selected' : '' }}>Tidak
                                    Tentu</option>
                                <option value="3"
                                    {{ old('kontinuitas') == 3 || $capacity->kontinuitas == '3' ? 'selected' : '' }}>Terus
                                    Menerus
                                </option>
                                <option value="2"
                                    {{ old('kontinuitas') == 2 || $capacity->kontinuitas == '2' ? 'selected' : '' }}>
                                    Kadang-Kadang
                                </option>
                            </select>
                        </div>

                        <div style="width: 49.5%;float:right;">
                            <span class="fw-bold">PENGALAMAN USAHA</span>
                            <select class="form-control input-sm form-border text-uppercase" name="pengalaman_usaha"
                                id="capacity3" required>
                                <option value="">--Pilih--</option>
                                <option value="1"
                                    {{ old('pengalaman_usaha') == '1' || $capacity->pengalaman_usaha == '1' ? 'selected' : '' }}>
                                    0 Tahun
                                </option>
                                <option value="2"
                                    {{ old('pengalaman_usaha') == '2' || $capacity->pengalaman_usaha == '2' ? 'selected' : '' }}>
                                    &lt; 1 Tahun
                                </option>
                                <option value="3"
                                    {{ old('pengalaman_usaha') == '3' || $capacity->pengalaman_usaha == '3' ? 'selected' : '' }}>
                                    1 - 3 Tahun
                                </option>
                                <option value="4"
                                    {{ old('pengalaman_usaha') == '4' || $capacity->pengalaman_usaha == '4' ? 'selected' : '' }}>
                                    &gt; 3 - 5
                                    Tahun</option>
                                <option value="5"
                                    {{ old('pengalaman_usaha') == '5' || $capacity->pengalaman_usaha == '5' ? 'selected' : '' }}>
                                    &gt; 5 Tahun
                                </option>
                            </select>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">PERTUMBUHAN USAHA</span>
                            <select class="form-control input-sm form-border text-uppercase" name="pertumbuhan_usaha"
                                id="capacity5" required>
                                <option value="">--Pilih--</option>
                                <option value="2"
                                    {{ old('pertumbuhan_usaha') == 2 || $capacity->pertumbuhan_usaha == '2' ? 'selected' : '' }}>
                                    Tetap</option>
                                <option value="1"
                                    {{ old('pertumbuhan_usaha') == 1 || $capacity->pertumbuhan_usaha == '1' ? 'selected' : '' }}>
                                    Turun</option>
                                <option value="3"
                                    {{ old('pertumbuhan_usaha') == 3 || $capacity->pertumbuhan_usaha == '3' ? 'selected' : '' }}>
                                    Meningkat
                                </option>
                            </select>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">CATATAN LAPORAN KEUANGAN</span>
                            <select class="form-control input-sm form-border text-uppercase" name="laporan_keuangan"
                                id="capacity7" required>
                                <option value="">--Pilih--</option>
                                <option value="1"
                                    {{ old('laporan_keuangan') == '1' || $capacity->laporan_keuangan == '1' ? 'selected' : '' }}>
                                    Tidak Ada
                                </option>
                                <option value="2"
                                    {{ old('laporan_keuangan') == '2' || $capacity->laporan_keuangan == '2' ? 'selected' : '' }}>
                                    Transaksi
                                    Harian</option>
                                <option value="3"
                                    {{ old('laporan_keuangan') == '3' || $capacity->laporan_keuangan == '3' ? 'selected' : '' }}>
                                    Mengumpulkan
                                    Bukti</option>
                            </select>
                        </div>

                        <div style="margin-top:5px;width: 100%;float:left;">
                            <span class="fw-bold">CATATAN KREDIT MASA LALU</span>
                            <select class="form-control input-sm form-border text-uppercase" name="catatan_kredit"
                                id="capacity8" required>
                                <option value="">--Pilih--</option>
                                <option value="3"
                                    {{ old('catatan_kredit') == '3' || $capacity->catatan_kredit == '3' ? 'selected' : '' }}>
                                    Lancar</option>
                                <option value="2"
                                    {{ old('catatan_kredit') == '2' || $capacity->catatan_kredit == '2' ? 'selected' : '' }}>
                                    Menunggak > 2
                                    Bulan</option>
                                <option value="1"
                                    {{ old('catatan_kredit') == '1' || $capacity->catatan_kredit == '1' ? 'selected' : '' }}>
                                    Lancar
                                    Menunggak 2 Bulan</option>
                            </select>
                        </div>
                    </div>


                    <div class="div-right">
                        <div style="width: 49.5%;float:left;">
                            <span class="fw-bold">KONDISI SLIK</span>
                            <select class="form-control input-sm form-border text-uppercase" name="kondisi_slik"
                                id="capacity2" required>
                                <option value="">--Pilih--</option>
                                <option value="3"
                                    {{ old('kondisi_slik') == 3 || $capacity->kondisi_slik == '3' ? 'selected' : '' }}>
                                    Lancar</option>
                                <option value="2"
                                    {{ old('kondisi_slik') == 2 || $capacity->kondisi_slik == '2' ? 'selected' : '' }}>
                                    Tidak Ada</option>
                                <option value="1"
                                    {{ old('kondisi_slik') == 1 || $capacity->kondisi_slik == '1' ? 'selected' : '' }}>
                                    Tidak Baik</option>
                            </select>
                        </div>

                        <div style="width: 49.5%;float:right;">
                            <span class="fw-bold">ASET DILUAR USAHA</span>
                            <select class="form-control input-sm form-border text-uppercase" name="aset_diluar_usaha"
                                id="capacity4" required>
                                <option value="">--Pilih--</option>
                                <option value="3"
                                    {{ old('aset_diluar_usaha') == 3 || $capacity->aset_diluar_usaha == '3' ? 'selected' : '' }}>
                                    Liquid
                                </option>
                                <option value="2"
                                    {{ old('aset_diluar_usaha') == 2 || $capacity->aset_diluar_usaha == '2' ? 'selected' : '' }}>
                                    Cukup Liquid
                                </option>
                                <option value="1"
                                    {{ old('aset_diluar_usaha') == 1 || $capacity->aset_diluar_usaha == '1' ? 'selected' : '' }}>
                                    Tidak Liquid
                                </option>
                            </select>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">ASET TERKAIT USAHA</span>
                            <select class="form-control input-sm form-border text-uppercase" name="aset_terkait_usaha"
                                id="capacity6" required>
                                <option value="">--Pilih--</option>
                                <option value="3"
                                    {{ old('aset_terkait_usaha') == 3 || $capacity->aset_terkait_usaha == '3' ? 'selected' : '' }}>
                                    Mengcover
                                </option>
                                <option value="2"
                                    {{ old('aset_terkait_usaha') == 2 || $capacity->aset_terkait_usaha == '2' ? 'selected' : '' }}>
                                    Cukup
                                    Mengcover</option>
                                <option value="1"
                                    {{ old('aset_terkait_usaha') == 1 || $capacity->aset_terkait_usaha == '1' ? 'selected' : '' }}>
                                    Tidak
                                    Mengcover</option>
                            </select>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">REPAYMENT CAPACITY</span>
                            <input class="form-control input-sm form-border text-uppercase" name="rc"
                                value="{{ $capacity->rc }} %" readonly>
                        </div>

                        <div style="margin-top:5px;width: 100%;float:right;">
                            <span class="fw-bold">EVALUASI</span>
                            <input class="form-control input-sm form-border text-uppercase bg-blue" type="text"
                                name="evaluasi_capacity" id="evaluasi_capacity" value="{{ $capacity->evaluasi_capacity }}"
                                readonly required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-sm btn-primary" style="margin-top:10px;width:100%">SIMPAN</button>
                </div>
            </form>

        </div>
    </div>
@endsection

@push('myscript')
    <script>
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
    </script>
@endpush
