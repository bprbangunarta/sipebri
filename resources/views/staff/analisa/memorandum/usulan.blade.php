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
                            <input type="text" id="metode" value="{{ $data->metode_rps }}" hidden>
                            <input type="text" id="suku_bunga" value="{{ $data->suku_bunga }}" hidden>
                            <input type="text" id="keuangan" value="{{ $data->keuangan }}" hidden>
                            <input type="text" id="laba_usaha_pertanian" value="{{ $data->laba_usaha_pertanian }}"
                                hidden>
                            <input type="text" class="form-control text-uppercase"
                                value="{{ 'Rp.' . ' ' . number_format($usulan->kebutuhan_dana, 0, ',', '.') }}" readonly>
                        </div>

                        <div style="width: 49.5%;float:right;">
                            <span class="fw-bold">MAX PLAFOND</span>
                            <input type="text" class="form-control text-uppercase" name="max_plafond" id="max"
                                value="{{ 'Rp.' . ' ' . number_format($data->maxplafon, 0, ',', '.') }}" readonly>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">USULAN PLAFOND</span>
                            <input type="text" id="total_taksasi" value="{{ $total_taksasi }}" hidden>
                            <input type="text" class="form-control text-uppercase" name="usulan_plafond"
                                id="usulan_plafon" placeholder="RP."
                                value="{{ 'Rp.' . ' ' . number_format($usulan->usulan_plafond, 0, ',', '.') ?? null }}"
                                required>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">JANGKA WAKTU</span>
                            <input type="number" class="form-control text-uppercase" name="jangka_waktu" id="jw"
                                placeholder="0" value="{{ $data->jangka_waktu }}">
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">REPAYMENT CAPACITY</span>
                            <input type="text" class="form-control text-uppercase" id='rc'
                                value="{{ $data->rc . ' ' . '%' }}" readonly>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">TAKSASI AGUNAN</span>
                            <input type="text" id="taksasi_agunan" class="form-control text-uppercase"
                                value="{{ $data->taksasiagunan . ' ' . '%' }}" readonly>
                        </div>
                    </div>


                    <div class="div-right">
                        <div style="width: 49.5%;float:left;">
                            <span class="fw-bold">BIAYA ADMIN (%)</span>
                            <input type="text" class="form-control text-uppercase" name="b_admin" id="b_admin"
                                placeholder="ENTRI" value="{{ $data->b_admin }}" required>
                        </div>

                        <div style="width: 49.5%;float:right;">
                            <span class="fw-bold">SEBELUM REALISASI</span>
                            <input type="text" class="form-control text-uppercase" name="sebelum_realisasi"
                                id="" placeholder="ENTRI" value="{{ $usulan->sebelum_realisasi }}">
                        </div>  

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <div style="width: 49.5%;float:left;">
                                <span class="fw-bold">B.PROVISI (%)</span>
                                <input type="text" class="form-control text-uppercase" name="b_provisi" id="b_provisi"
                                placeholder="ENTRI" value="{{ $data->b_provisi }}" required>
                            </div>

                            <div style="width: 49.5%;float:right;">
                                <span class="fw-bold">B.PENALTI (%)</span>
                                <input type="text" class="form-control text-uppercase" name="b_penalti" id="b_penalti"
                                placeholder="ENTRI" value="{{ $data->b_penalti }}" required>
                            </div>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">SYARAT TAMBAHAN</span>
                            <input type="text" class="form-control text-uppercase" name="syarat_tambahan" id=""
                                placeholder="ENTRI" value="{{ $usulan->syarat_tambahan }}">
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">PENGIKATAN ASURANSI</span>

                            <select class="form-control" name="pengikatan" id="pengikatan" required>
                                <option value="1"
                                    {{ old('pengikatan') == 1 || $usulan->pengikatan == '1' ? 'selected' : '' }}>Tanpa
                                    Pengikatan</option>
                                <option value="2"
                                    {{ old('pengikatan') == 2 || $usulan->pengikatan == '2' ? 'selected' : '' }}>APHT
                                </option>
                                <option value="3"
                                    {{ old('pengikatan') == 3 || $usulan->pengikatan == '3' ? 'selected' : '' }}>Fiducia
                                </option>
                                <option value="4"
                                    {{ old('pengikatan') == 4 || $usulan->pengikatan == '4' ? 'selected' : '' }}>APHT dan
                                    FIDUCIA</option>
                            </select>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">SYARAT LAINNYA</span>
                            <input type="text" class="form-control text-uppercase" name="syarat_lainnya"
                                id="" placeholder="ENTRI" value="{{ $usulan->syarat_lainnya }}">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-sm btn-primary"
                        style="margin-top:10px;width:100%">SIMPAN</button>
                </div>
            </form>

        </div>
    </div>
@endsection

@push('myscript')
    {{-- <script src="{{ asset('assets/js/myscript/kualitatif.js') }}"></script> --}}
    <script src="{{ asset('assets/js/myscript/usulan_kredit_rc.js') }}"></script>

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

            // Memeriksa apakah nilai input1 lebih besar dari input2
            if (value1 > value2) {
                // Jika lebih besar, atur nilai input1 menjadi nilai input2
                $(this).val(value2);
            }
        });



        $('#b_admin').on('input', function() {
            // Mengambil nilai dari input field
            var inputValue = $(this).val();

            // Mengganti koma (,) menjadi titik (.)
            var convertedValue = inputValue.replace(/,/g, '.');

            // Memasukkan nilai yang sudah diubah ke dalam input field
            $(this).val(convertedValue);
        });

        $('#b_provisi').on('input', function() {
            // Mengambil nilai dari input field
            var inputValue = $(this).val();

            // Mengganti koma (,) menjadi titik (.)
            var convertedValue = inputValue.replace(/,/g, '.');

            // Memasukkan nilai yang sudah diubah ke dalam input field
            $(this).val(convertedValue);
        });
    </script>
@endpush
