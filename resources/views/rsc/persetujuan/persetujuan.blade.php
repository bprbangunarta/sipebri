@extends('theme.app')
@section('title', 'Persetujuan RSC')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-3">
                    @include('theme.menu-persetujuan', ['data' => $data])
                </div>

                <div class="col-xs-9">
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active text-bold">
                                <a href="#persetujuan" data-toggle="tab">PERSETUJUAN KOMITE</a>
                            </li>

                        </ul>

                        <div class="tab-content">

                            <div id="persetujuan" class="tab-pane fade in active">

                                <form action="{{ route('rsc.persetujuan.simpan', ['rsc' => $data->rsc]) }}" method="POST">
                                    @method('post')
                                    @csrf
                                    <div class="tab-content">

                                        <div class="tab-pane active" id="pengajuan">
                                            <div class="box-body" style="margin-top: -10px;font-size:12px;">

                                                <div class="div-left">
                                                    <div style="margin-top:5px;width: 49.5%;float:left;">
                                                        <span class="fw-bold">PLAFON RSC</span>
                                                        <input type="hidden" id='keuangan'
                                                            value="{{ $keuangan->keuangan_perbulan }}">
                                                        <input type="text" class="form-control" name="plafon"
                                                            id="plafon" placeholder="10.000.000"
                                                            value="{{ number_format($data->plafon, '0', ',', '.') }}"
                                                            readonly>
                                                    </div>

                                                    <div style="margin-top:5px;width: 49.5%;float:right;">
                                                        <span class="fw-bold">PERSENTASE ADM(%)</span>
                                                        <input type="text" class="form-control" name="persen_adm"
                                                            id="persen_adm" placeholder="ENTRI"
                                                            value="{{ old('persen_adm', $biaya->administrasi) }}" required
                                                            readonly>
                                                    </div>

                                                    <div style="margin-top:5px;width: 49.5%;float:left;">
                                                        <span class="fw-bold">JK KREDIT (BULAN)</span>
                                                        <input type="number" class="form-control" name="jangka_waktu"
                                                            id="jangka_waktu" placeholder="ENTRI"
                                                            value="{{ old('jangka_waktu', $pengusulan->jangka_waktu) }}"
                                                            required readonly>
                                                    </div>

                                                    <div style="margin-top:5px;width: 49.5%;float:right;">
                                                        <span class="fw-bold">JK BUNGA (BULAN)</span>
                                                        <input type="number" class="form-control" name="jangka_bunga"
                                                            id="jangka_bunga" placeholder="ENTRI"
                                                            value="{{ old('jangka_bunga', $pengusulan->jangka_bunga) }}"
                                                            required readonly>
                                                    </div>

                                                    <div style="margin-top:5px;width: 49.5%;float:left;">
                                                        <span class="fw-bold">SUKU BUNGA</span>
                                                        <input type="text" class="form-control" name="suku_bunga"
                                                            id="suku_bunga" placeholder="ENTRI"
                                                            value="{{ old('suku_bunga', $pengusulan->suku_bunga) }}"
                                                            required readonly>
                                                    </div>

                                                    <div style="margin-top:5px;width: 49.5%;float:right;">
                                                        <span class="fw-bold">RC (%)</span>
                                                        <input type="text" class="form-control" name="rc"
                                                            id="rc" placeholder="ENTRI"
                                                            value="{{ old('rc', $pengusulan->rc) }}" required readonly>
                                                    </div>

                                                    <div style="margin-top:5px;width: 49.5%;float:left;">
                                                        <span class="fw-bold">TOTAL ANGSURAN</span>
                                                        <input type="text" class="form-control" name="total_angsuran"
                                                            id="total_angsuran" placeholder="ENTRI"
                                                            value="{{ old('total_angsuran', number_format($pengusulan->total_angsuran, '0', ',', '.')) }}"
                                                            required readonly>
                                                    </div>

                                                </div>


                                                <div class="div-right">
                                                    <div style="margin-top:5px;width: 49.5%;float:left;">
                                                        <span class="fw-bold">ADMINISTRASI</span>
                                                        <input type="text" class="form-control" name="nominal_adm"
                                                            id="nominal_adm" placeholder="ENTRI"
                                                            value="{{ old('nominal_adm', number_format($biaya->administrasi_nominal, '0', ',', '.')) }}"
                                                            required readonly>
                                                    </div>

                                                    <div style="margin-top:5px;width: 49.5%;float:right;">
                                                        <span class="fw-bold">UJROH KIH</span>
                                                        <input type="text" class="form-control" name="ujroh"
                                                            id="ujroh" placeholder="ENTRI" value="{{ old('ujroh') }}"
                                                            @if ($data->produk_kode != 'KIH') readonly @endif>
                                                    </div>

                                                    <div style="margin-top:5px;width: 49.5%;float:left;">
                                                        <span class="fw-bold">JK POKOK (BULAN)</span>
                                                        <input type="number" class="form-control" name="jangka_pokok"
                                                            id="jangka_pokok" placeholder="ENTRI"
                                                            value="{{ old('jangka_pokok', $pengusulan->jangka_pokok) }}"
                                                            required readonly>
                                                    </div>

                                                    <div style="margin-top:5px;width: 49.5%;float:right;">
                                                        <span class="fw-bold">METODE RPS</span>
                                                        <select class="form-control text-uppercase metode"
                                                            name="metode_rps" id="select-metodes" required>
                                                            <option value="">-- PILIH -- </option>
                                                            <option value="FLAT"
                                                                {{ old('metode_rps') == 'FLAT' || $pengusulan->metode_rps == 'FLAT' ? 'selected' : '' }}>
                                                                FLAT
                                                            </option>
                                                            <option value="PRK"
                                                                {{ old('metode_rps') == 'PRK' || $pengusulan->metode_rps == 'PRK' ? 'selected' : '' }}>
                                                                PRK
                                                            </option>
                                                            <option value="EFEKTIF"
                                                                {{ old('metode_rps') == 'EFEKTIF' || $pengusulan->metode_rps == 'EFEKTIF' ? 'selected' : '' }}>
                                                                EFEKTIF
                                                            </option>
                                                            <option value="EFEKTIF ANUITAS"
                                                                {{ old('metode_rps') == 'EFEKTIF ANUITAS' || $pengusulan->metode_rps == 'EFEKTIF ANUITAS' ? 'selected' : '' }}>
                                                                EFEKTIF ANUITAS
                                                            </option>
                                                            <option value="EFEKTIF MUSIMAN"
                                                                {{ old('metode_rps') == 'EFEKTIF MUSIMAN' || $pengusulan->metode_rps == 'EFEKTIF MUSIMAN' ? 'selected' : '' }}>
                                                                EFEKTIF MUSIMAN
                                                            </option>
                                                        </select>
                                                    </div>

                                                    <div style="margin-top:5px;width: 49.5%;float:left;">
                                                        <span class="fw-bold">ANGSURAN POKOK</span>
                                                        <input type="text" class="form-control" name="angsuran_pokok"
                                                            id="angsuran_pokok" placeholder="ENTRI"
                                                            value="{{ old('angsuran_pokok', number_format($pengusulan->angsuran_pokok, '0', ',', '.')) }}"
                                                            required readonly>
                                                    </div>

                                                    <div style="margin-top:5px;width: 49.5%;float:right;">
                                                        <span class="fw-bold">ANGSURAN BUNGA</span>
                                                        <input type="text" class="form-control" name="angsuran_bunga"
                                                            id="angsuran_bunga" placeholder="ENTRI"
                                                            value="{{ old('angsuran_bunga', number_format($pengusulan->angsuran_bunga, '0', ',', '.')) }}"
                                                            required readonly>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        <div class="box-body" style="margin-top:-20px;">
                                            <div style="margin-top:5px;width: 100%;float:left;">
                                                <label>CATATAN</label>
                                                <textarea class="form-control text-uppercase" style="resize: none;" rows="5" name="catatan" id="catatan"
                                                    required=""></textarea>
                                            </div>
                                        </div>

                                        <div class="box-body" style="margin-top:-20px;">
                                            <button type="submit" class="btn btn-sm btn-primary"
                                                style="margin-top:10px;width:100%">SIMPAN</button>
                                        </div>
                                    </div>
                                </form>

                            </div>

                        </div>
                    </div>
                </div>
        </section>
    </div>
@endsection
@push('myscript')
    <script>
        $('.metode').select2()

        $(document).ready(function() {
            $('#suku_bunga').on('input', function() {
                this.value = this.value.replace(/,/g, '.');
            });
        });

        var nm_adm = document.getElementById("nominal_adm");
        var ujroh = document.getElementById("ujroh");

        if (nm_adm) {
            nm_adm.addEventListener("keyup", function(e) {
                nm_adm.value = formatRupiah(this.value, "");
            });
        }
        if (ujroh) {
            ujroh.addEventListener("keyup", function(e) {
                ujroh.value = formatRupiah(this.value, "");
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
            return prefix == undefined ? rupiah : rupiah ? "" + rupiah : "";
        }


        //--> KALKULASI BIAYA RSC <--//
        $('#persen_adm').on('input', function() {
            var inputValue = $(this).val();
            const nominal = parseFloat($("#plafon").val().replace(/[^\d]/g, ""))

            const jml = nominal * inputValue / 100

            const hasil = Math.floor(jml);

            const bs = hasil.toLocaleString("id-ID");
            $('#nominal_adm').val(bs)

            console.log(inputValue, nominal, bs)

        });

        $('#nominal_adm').on('input', function() {
            var inputValue = $(this).val().replace(/[^\d]/g, "");
            const nominal = parseFloat($("#plafon").val().replace(/[^\d]/g, ""))

            const jml = inputValue / nominal * 100

            const formattedPercentage = jml.toFixed(2);

            const bs = parseFloat(formattedPercentage).toLocaleString("id-ID", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            $('#persen_adm').val(formattedPercentage)

        });
        //--> KALKULASI BIAYA RSC <--//
    </script>
    <script src="{{ asset('assets/js/myscript/rsc_usulan_plafon.js') }}"></script>
@endpush
