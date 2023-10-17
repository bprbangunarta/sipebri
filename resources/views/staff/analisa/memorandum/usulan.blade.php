@extends('staff.analisa.memorandum.menu', [$data, 'pengajuan' => $data->kd_pengajuan])
@section('title', 'Analisa Memorandum')

@section('content')
    <div class="tab-content">
        <div class="tab-pane active">

            <form action="{{ route('memorandum.updateusulan', ['pengajuan' => $data->kd_pengajuan]) }}" method="post">
                @method('put')
                @csrf
                <div class="box-body" style="margin-top: -10px;font-size:12px;">

                    <div class="div-left">
                        <div style="width: 49.5%;float:left;">
                            <span class="fw-bold">KEBUTUHAN DANA</span>
                            <input type="text" class="form-control text-uppercase" value="RP." readonly>
                        </div>

                        <div style="width: 49.5%;float:right;">
                            <span class="fw-bold">MAX PLAFOND</span>
                            <input type="text" class="form-control text-uppercase" name="max_plafond" id="max"
                                value="{{ 'Rp.' . ' ' . number_format($data->maxplafon, 0, ',', '.') }}" readonly>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">USULAN PLAFOND</span>
                            <input type="text" class="form-control text-uppercase" name="usulan_plafond"
                                id="usulan_plafon" placeholder="RP." value="" required>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">JANGKA WAKTU</span>
                            <input type="number" class="form-control text-uppercase" name="jangka_waktu" id=""
                                placeholder="0" value="{{ $data->jangka_waktu }}">
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">REPAYMENT CAPACITY</span>
                            <input type="text" class="form-control text-uppercase" value="{{ $data->rc . ' ' . '%' }}"
                                readonly>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">TAKSASI AGUNAN</span>
                            <input type="text" class="form-control text-uppercase"
                                value="{{ $data->taksasiagunan . ' ' . '%' }}" readonly>
                        </div>
                    </div>


                    <div class="div-right">
                        <div style="width: 100%;float:left;">
                            <span class="fw-bold">SEBELUM REALISASI</span>
                            <input type="text" class="form-control text-uppercase" name="sebelum_realisasi"
                                id="" placeholder="ENTRI" value="{{ $usulan->sebelum_realisasi }}">
                        </div>

                        <div style="margin-top:5px;width: 100%;float:right;">
                            <span class="fw-bold">SYARAT TAMBAHAN</span>
                            <input type="text" class="form-control text-uppercase" name="syarat_tambahan" id=""
                                placeholder="ENTRI" value="{{ $usulan->syarat_tambahan }}">
                        </div>

                        <div style="margin-top:5px;width: 100%;float:left;">
                            <span class="fw-bold">SYARAT LAINNYA</span>
                            <input type="text" class="form-control text-uppercase" name="syarat_lainnya" id=""
                                placeholder="ENTRI" value="{{ $usulan->syarat_lainnya }}">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-sm btn-primary" style="margin-top:10px;width:100%">SIMPAN</button>
                </div>
            </form>

        </div>
    </div>
@endsection

@push('myscript')
    <script src="{{ asset('assets/js/myscript/kualitatif.js') }}"></script>

    <script>
        $(function() {
            //Initialize Select2 Elements
            $('.bi_sifat_kode').select2()
            $('.bi_penggunaan_kode').select2()
            $('.bi_gol_penjamin_kode').select2()
            $('.bi_sumber_pelunasan_kode').select2()
            $('.bi_jenis_usaha_kode').select2()
            $('.bi_sek_ekonomi_kode').select2()
            $('.bi_sek_ekonomi_slik').select2()
            $('.bi_gol_debitur_kode').select2()
            $('.bi_gol_debitur_slik').select2()
        })


        var usulan_plafon = document.getElementById("usulan_plafon");
        if (usulan_plafon) {
            usulan_plafon.addEventListener("keyup", function(e) {
                usulan_plafon.value = formatRupiah(this.value, "Rp. ");
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


        // Mendengarkan perubahan pada input1
        $("#usulan_plafon").on("input", function() {
            var value1 = parseFloat($(this).val().replace(/[^\d]/g, ""));
            var value2 = parseFloat($("#max").val().replace(/[^\d]/g, ""));
            console.log(value1, value2)
            // Memeriksa apakah nilai input1 lebih besar dari input2
            if (value1 > value2) {
                // Jika lebih besar, atur nilai input1 menjadi nilai input2
                $(this).val(value2);
            }
        });
    </script>
@endpush
