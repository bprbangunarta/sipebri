@extends('theme.app')
@section('title', 'Data Nasabah')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-3">
                    @include('theme.menu-rsc', ['data' => $data])
                </div>

                <div class="col-xs-9">
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#data_kredit" data-toggle="tab">DATA KREDIT & NASABAH</a>
                            </li>

                            <li>
                                <a href="#biaya_rsc" data-toggle="tab">BIAYA RSC</a>
                            </li>
                        </ul>

                        <div class="tab-content">

                            <div id="data_kredit" class="tab-pane fade in active">
                                <form
                                    action="{{ route('rsc.update.data.kredit', ['kode' => $data_rsc->pengajuan_kode, 'rsc' => $data_rsc->kode_rsc]) }}"
                                    method="POST" enctype="multipart/form-data">
                                    @method('put')
                                    @csrf
                                    <div class="box-body" style="margin-top: -10px;font-size:12px;">

                                        {{-- Left --}}
                                        <div class="div-left">
                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">NAMA NASABAH</span>
                                                <input type="text" class="form-control" name="nama_nasabah"
                                                    value="{{ old('nama_nasabah', $data->nama_nasabah) }}" required
                                                    readonly>
                                            </div>
                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">NO HP</span>
                                                <input type="text" class="form-control" name="no_telp"
                                                    value="{{ old('no_telp', $data->no_telp) }}" required readonly>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">PLAFON</span>
                                                <input type="text" class="form-control" name="plafon"
                                                    value="{{ number_format($data->plafon_awal, '0', ',', '.') }}" required
                                                    readonly>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">NO SPK</span>
                                                <input type="text" class="form-control" name="no_spk"
                                                    value="{{ old('no_spk', $data->no_spk) }}" required readonly>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">JENIS PERSETUJUAN</span>
                                                <select type="text" class="form-control text-uppercase jenis_persetujuan"
                                                    style="width: 100%;" name="jenis_persetujuan" required>
                                                    <option value=""
                                                        {{ $data_rsc->jenis_persetujuan == '' ? 'selected' : '' }}>
                                                        --Pilih--</option>
                                                    <option value="RESCHEDULLING"
                                                        {{ $data_rsc->jenis_persetujuan == 'RESCHEDULLING' ? 'selected' : '' }}>
                                                        RESCHEDULLING
                                                    </option>
                                                    <option value="RECONDITIONING"
                                                        {{ $data_rsc->jenis_persetujuan == 'RECONDITIONING' ? 'selected' : '' }}>
                                                        RECONDITIONING</option>
                                                    <option value="RESTRUCTURING"
                                                        {{ $data_rsc->jenis_persetujuan == 'RESTRUCTURING' ? 'selected' : '' }}>
                                                        RESTRUCTURING</option>
                                                </select>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">BAKI DEBET</span>
                                                <input type="text" placeholder="ENTRI" class="form-control"
                                                    name="baki_debet" id="baki_debet"
                                                    value="{{ number_format($data_rsc->baki_debet, '0', ',', '.') }}"
                                                    required>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">TGK POKOK (BULAN)</span>
                                                <input type="number" class="form-control" placeholder="ENTRI"
                                                    name="jml_tunggakan_pokok" id="jml_tunggakan_pokok"
                                                    value="{{ old('jml_tunggakan_pokok', $data_rsc->jml_tgk_pokok) }}"
                                                    required>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">TUNGGAKAN BUNGA</span>
                                                <input class="form-control" placeholder="ENTRI" name="tunggakan_bunga"
                                                    id="tunggakan_bunga"
                                                    value="{{ old('tunggakan_bunga', number_format($data_rsc->tunggakan_bunga, '0', ',', '.')) }}"
                                                    required>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">POKOK DIBAYAR</span>
                                                <input type="text" class="form-control" name="pk_dibayar" id="pk_dibayar"
                                                    placeholder="ENTRI"
                                                    value="{{ old('pk_dibayar', number_format($data_rsc->pokok_dibayar, '0', ',', '.')) }}">
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">BUNGA DIBAYAR</span>
                                                <input type="text" class="form-control" name="bg_dibayar" id="bg_dibayar"
                                                    placeholder="ENTRI"
                                                    value="{{ old('bg_dibayar', number_format($data_rsc->bunga_dibayar, '0', ',', '.')) }}">
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <label>JK KREDIT (BULAN)</label>
                                                <input type="number" class="form-control" name="jangka_waktu"
                                                    id="jangka_waktu" placeholder="ENTRI"
                                                    value="{{ old('jangka_waktu', $data->jangka_waktu) }}" required>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <label>METODE RPS</label>
                                                <select class="form-control text-uppercase metode" name="metode_rps"
                                                    id="select-metodes" required>
                                                    <option value="">-- PILIH -- </option>
                                                    <option value="FLAT"
                                                        {{ old('metode_rps') == 'FLAT' || $data->metode_rps == 'FLAT' ? 'selected' : '' }}>
                                                        FLAT
                                                    </option>
                                                    <option value="PRK"
                                                        {{ old('metode_rps') == 'PRK' || $data->metode_rps == 'PRK' ? 'selected' : '' }}>
                                                        PRK
                                                    </option>
                                                    <option value="EFEKTIF"
                                                        {{ old('metode_rps') == 'EFEKTIF' || $data->metode_rps == 'EFEKTIF' ? 'selected' : '' }}>
                                                        EFEKTIF
                                                    </option>
                                                    <option value="EFEKTIF ANUITAS"
                                                        {{ old('metode_rps') == 'EFEKTIF ANUITAS' || $data->metode_rps == 'EFEKTIF ANUITAS' ? 'selected' : '' }}>
                                                        EFEKTIF ANUITAS
                                                    </option>
                                                    <option value="EFEKTIF MUSIMAN"
                                                        {{ old('metode_rps') == 'EFEKTIF MUSIMAN' || $data->metode_rps == 'EFEKTIF MUSIMAN' ? 'selected' : '' }}>
                                                        EFEKTIF MUSIMAN
                                                    </option>
                                                </select>
                                            </div>


                                        </div>
                                        {{-- Left --}}

                                        {{-- Right --}}
                                        <div class="div-right">
                                            <div style="margin-top:5px;width: 100%;float:left;">
                                                <span class="fw-bold">ALAMAT KTP</span>
                                                <input type="text" class="form-control" style="font-size: 12px;"
                                                    name="alamat_ktp" id="alamat_ktp"
                                                    value="{{ old('alamat_ktp', $data->alamat_ktp) }}"
                                                    placeholder="ENTRI" required readonly>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">TANGGAL JTH TEMPO</span>
                                                <input type="text" class="form-control" name="tgl_jth_tempo"
                                                    value="{{ old('tgl_jth_tempo', $data->tgl_jth_tempo) }}" required
                                                    readonly>
                                            </div>
                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">INFO JENIS USAHA</span>
                                                <select type="text" class="form-control text-uppercase jns_usaha"
                                                    style="width: 100%; font-size: 12px;" name="jns_usaha">
                                                    @foreach ($usaha as $item)
                                                        <option value="{{ $item->nama_usaha }}">
                                                            {{ $item->nama_usaha }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">KLASIFIKASI KREDIT</span>
                                                <select type="text"
                                                    class="form-control text-uppercase klasifikasi_kredit"
                                                    style="width: 100%;" name="klasifikasi_kredit" required>
                                                    <option value="KURANG LANCAR"
                                                        {{ $data_rsc->klasifikasi_kredit == '' ? 'selected' : '' }}>
                                                        --Pilih--</option>
                                                    <option value="LANCAR"
                                                        {{ $data_rsc->klasifikasi_kredit == 'LANCAR' ? 'selected' : '' }}>
                                                        Lancar
                                                    </option>
                                                    <option value="KURANG LANCAR"
                                                        {{ $data_rsc->klasifikasi_kredit == 'KURANG LANCAR' ? 'selected' : '' }}>
                                                        Kurang Lancar</option>
                                                    <option value="DIRAGUKAN"
                                                        {{ $data_rsc->klasifikasi_kredit == 'DIRAGUKAN' ? 'selected' : '' }}>
                                                        Diragukan</option>
                                                    <option value="MACET"
                                                        {{ $data_rsc->klasifikasi_kredit == 'MACET' ? 'selected' : '' }}>
                                                        Macet</option>
                                                </select>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">TUNGGAKAN POKOK</span>
                                                <input type="text" class="form-control" name=" tunggakan_pokok"
                                                    id="tunggakan_pokok" placeholder="ENTRI"
                                                    value="{{ old('tunggakan_pokok', number_format($data_rsc->tunggakan_poko, '0', ',', '.')) }}"
                                                    required>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">TGK BUNGA (BULAN)</span>
                                                <input type="number" class="form-control" placeholder="ENTRI"
                                                    name="jml_tunggakan_bunga" id="jml_tunggakan_bunga"
                                                    value="{{ old('jml_tunggakan_bunga', $data_rsc->jml_tgk_bunga) }}"
                                                    required>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">TUNGGAKAN DENDA</span>
                                                <input class="form-control" placeholder="ENTRI" name="tunggakan_denda"
                                                    id="tunggakan_denda"
                                                    value="{{ old('tunggakan_denda', number_format($data_rsc->tunggakan_denda, '0', ',', '.')) }}"
                                                    required>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <label>TOTAL TUNGGAKAN</label>
                                                <input class="form-control" placeholder="ENTRI" name="total_tunggakan"
                                                    id="total_tunggakan"
                                                    value="{{ old('total_tunggakan', number_format($data_rsc->total_tunggakan, '0', ',', '.')) }}"
                                                    required readonly>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <label>PLAFON</label>
                                                <input type="hidden" class="form-control" name=""
                                                    id="pn_plafons" placeholder="ENTRI"
                                                    value="{{ old('penentuan_plafon', number_format($data_rsc->penentuan_plafon_temp, '0', ',', '.')) }}"
                                                    readonly>
                                                <input type="text" class="form-control" name="penentuan_plafon"
                                                    id="pn_plafon" placeholder="ENTRI"
                                                    value="{{ old('penentuan_plafon', number_format($data_rsc->penentuan_plafon, '0', ',', '.')) }}"
                                                    readonly>
                                            </div>
                                        </div>
                                        {{-- Right --}}

                                    </div>
                                    <div class="box-body" style="margin-top:-20px;">
                                        <button type="submit" class="btn btn-sm btn-primary"
                                            style="margin-top:10px;width:100%">SIMPAN</button>
                                    </div>
                                </form>
                            </div>

                            <div id="biaya_rsc" class="tab-pane fade">
                                @if (!is_null($biaya_rsc))
                                    <form
                                        action="{{ route('rsc.update.biaya.rsc', ['kode' => $data_rsc->pengajuan_kode, 'rsc' => $data_rsc->kode_rsc]) }}"
                                        method="POST" enctype="multipart/form-data">
                                        @method('put')
                                        @csrf
                                        <div class="box-body" style="margin-top: -10px;font-size:12px;">

                                            <div class="div-left">
                                                <div style="margin-top:5px;width: 49.5%;float:left;">
                                                    <label>ADMINISTRASI (%)</label>
                                                    <input type="text" class="form-control" name="adm"
                                                        id="adm" placeholder="ENTRI"
                                                        value="{{ old('adm', $biaya_rsc->administrasi) }}">
                                                </div>
                                                <div style="margin-top:5px;width: 49.5%;float:right;">
                                                    <label>NOMINAL ADMINISTRASI</label>
                                                    <input type="hidden" class="form-control" name="persentase"
                                                        id="persentases" placeholder="ENTRI"
                                                        value="{{ number_format($data_rsc->penentuan_plafon, '0', ',', '.') }}"
                                                        readonly>
                                                    <input type="text" class="form-control" name="persentase"
                                                        id="persentase" placeholder="ENTRI"
                                                        value="{{ number_format($biaya_rsc->administrasi_nominal, '0', ',', '.') }}">
                                                </div>
                                                <div style="margin-top:5px;width: 49.5%;float:left;">
                                                    <label>POKOK DIBAYAR</label>
                                                    <input type="text" class="form-control" name="pokok_dibayar"
                                                        id="pok_dibayar" placeholder="ENTRI"
                                                        value="{{ old('pokok_dibayar', number_format($biaya_rsc->poko_dibayar, '0', ',', '.')) }}">
                                                </div>
                                                <div style="margin-top:5px;width: 49.5%;float:right;">
                                                    <label>BUNGA DIBAYAR</label>
                                                    <input type="text" class="form-control" name="bunga_dibayar"
                                                        id="bung_dibayar" placeholder="ENTRI"
                                                        value="{{ old('bunga_dibayar', number_format($biaya_rsc->bunga_dibayar, '0', ',', '.')) }}">
                                                </div>

                                                <div style="margin-top:5px;width: 49.5%;float:left;">
                                                    <label>TOTAL BIAYA</label>
                                                    <input type="text" class="form-control" name="total_biaya"
                                                        id="tot_biaya" placeholder="ENTRI"
                                                        value="{{ old('total_biaya', number_format($biaya_rsc->total, '0', ',', '.')) }}"
                                                        readonly>
                                                </div>

                                            </div>

                                            <div class="div-right">
                                                <div style="margin-top:5px;width: 49.5%;float:left;">
                                                    <label>ASURANSI JIWA</label>
                                                    <input type="text" class="form-control" name="asuransi_jiwa"
                                                        id="a_jiwa" placeholder="ENTRI"
                                                        value="{{ old('asuransi_jiwa', number_format($biaya_rsc->asuransi_jiwa, '0', ',', '.')) }}">
                                                </div>

                                                <div style="margin-top:5px;width: 49.5%;float:right;">
                                                    <label>ASURANSI TLO</label>
                                                    <input type="text" class="form-control" name="asuransi_tlo"
                                                        id="a_tlo" placeholder="ENTRI"
                                                        value="{{ old('asuransi_tlo', number_format($biaya_rsc->asuransi_tlo, '0', ',', '.')) }}">
                                                </div>

                                                <div style="margin-top:5px;width: 49.5%;float:left;">
                                                    <label>DENDA DIBAYAR</label>
                                                    <input type="text" class="form-control" name="denda_dibayar"
                                                        id="den_dibayar" placeholder="ENTRI"
                                                        value="{{ number_format($data_rsc->tunggakan_denda, '0', ',', '.') }}"
                                                        readonly>
                                                </div>

                                                <div style="margin-top:5px;width: 49.5%;float:right;">
                                                    <label>UJROH KIH</label>
                                                    <input type="text" class="form-control" name="ujroh"
                                                        id="ujroh" placeholder="ENTRI"
                                                        value="{{ old('ujroh', number_format($biaya_rsc->ujroh, '0', ',', '.')) }}"
                                                        @if ($data->produk_kode != 'KIH') readonly @endif>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="box-body" style="margin-top:-20px;">
                                            <button type="submit" class="btn btn-sm btn-primary"
                                                style="margin-top:10px;width:100%">SIMPAN</button>
                                        </div>
                                    </form>
                                @else
                                    <div class="box-body" style="margin-top: -10px;font-size:12px;">
                                        <center>
                                            <div>
                                                <span class="text-center" style="font-size: 14px;">Data tidak ditemukan,
                                                    harap
                                                    simpan penentuan
                                                    plafon.</span>
                                            </div>
                                        </center>
                                    </div>
                                @endif
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
        $('.jns_usaha').select2()
        $('.klasifikasi_kredit').select2()
        $('.jenis_persetujuan').select2()

        var baki = document.getElementById('baki_debet')
        var tpokok = document.getElementById('tunggakan_pokok')
        var tbunga = document.getElementById('tunggakan_bunga')
        var tdenda = document.getElementById('tunggakan_denda')

        var tasuransi_jiwa = document.getElementById('asuransi_jiwa')
        var tasuransi_tlo = document.getElementById('asuransi_tlo')
        var tpokok_dibayar = document.getElementById('pokok_dibayar')
        var tbunga_dibayar = document.getElementById('bunga_dibayar')
        var tdenda_dibayar = document.getElementById('denda_dibayar')

        var pk_dibayar = document.getElementById('pk_dibayar')
        var bg_dibayar = document.getElementById('bg_dibayar')

        var a_jiwa = document.getElementById('a_jiwa')
        var a_tlo = document.getElementById('a_tlo')
        var pok_dibayar = document.getElementById('pok_dibayar')
        var bung_dibayar = document.getElementById('bung_dibayar')
        var persentase = document.getElementById('persentase')


        if (a_jiwa) {
            a_jiwa.addEventListener("keyup", function(e) {
                a_jiwa.value = formatRupiah(this.value, "Rp. ");
            });
        }
        if (a_tlo) {
            a_tlo.addEventListener("keyup", function(e) {
                a_tlo.value = formatRupiah(this.value, "Rp. ");
            });
        }
        if (pok_dibayar) {
            pok_dibayar.addEventListener("keyup", function(e) {
                pok_dibayar.value = formatRupiah(this.value, "Rp. ");
            });
        }
        if (bung_dibayar) {
            bung_dibayar.addEventListener("keyup", function(e) {
                bung_dibayar.value = formatRupiah(this.value, "Rp. ");
            });
        }


        if (pk_dibayar) {
            pk_dibayar.addEventListener("keyup", function(e) {
                pk_dibayar.value = formatRupiah(this.value, "Rp. ");
            });
        }
        if (bg_dibayar) {
            bg_dibayar.addEventListener("keyup", function(e) {
                bg_dibayar.value = formatRupiah(this.value, "Rp. ");
            });
        }

        if (baki) {
            baki.addEventListener("keyup", function(e) {
                baki.value = formatRupiah(this.value, "Rp. ");
            });
        }

        if (tpokok) {
            tpokok.addEventListener("keyup", function(e) {
                tpokok.value = formatRupiah(this.value, "Rp. ");
            });
        }
        if (tbunga) {
            tbunga.addEventListener("keyup", function(e) {
                tbunga.value = formatRupiah(this.value, "Rp. ");
            });
        }
        if (tdenda) {
            tdenda.addEventListener("keyup", function(e) {
                tdenda.value = formatRupiah(this.value, "Rp. ");
            });
        }

        if (tasuransi_jiwa) {
            tasuransi_jiwa.addEventListener("keyup", function(e) {
                tasuransi_jiwa.value = formatRupiah(this.value, "Rp. ");
            });
        }
        if (tasuransi_tlo) {
            tasuransi_tlo.addEventListener("keyup", function(e) {
                tasuransi_tlo.value = formatRupiah(this.value, "Rp. ");
            });
        }
        if (tpokok_dibayar) {
            tpokok_dibayar.addEventListener("keyup", function(e) {
                tpokok_dibayar.value = formatRupiah(this.value, "Rp. ");
            });
        }
        if (tbunga_dibayar) {
            tbunga_dibayar.addEventListener("keyup", function(e) {
                tbunga_dibayar.value = formatRupiah(this.value, "Rp. ");
            });
        }
        if (tdenda_dibayar) {
            tdenda_dibayar.addEventListener("keyup", function(e) {
                tdenda_dibayar.value = formatRupiah(this.value, "Rp. ");
            });
        }
        if (persentase) {
            persentase.addEventListener("keyup", function(e) {
                persentase.value = formatRupiah(this.value, "Rp. ");
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

        //--> KALKULASI TUNGGAKAN <--//
        $('#tunggakan_pokok, #tunggakan_bunga, #tunggakan_denda').keyup(function() {
            const tgk_poko = $('#tunggakan_pokok').val()
            const tgk_bunga = $('#tunggakan_bunga').val()
            const tgk_denda = $('#tunggakan_denda').val()

            var rpoko = parseFloat(tgk_poko.replace(/[^\d]/g, "")) || 0;
            var rbunga = parseFloat(tgk_bunga.replace(/[^\d]/g, "")) || 0;
            var rdenda = parseFloat(tgk_denda.replace(/[^\d]/g, "")) || 0;

            const jml = rpoko + rbunga + rdenda

            var bs = jml.toLocaleString("id-ID");
            $("#total_tunggakan").val(bs);
        })

        $('#tunggakan_bunga,#baki_debet').keyup(function() {
            const tgk_bunga = $('#tunggakan_bunga').val()
            const baki_debet = $('#baki_debet').val()

            var rbunga = parseFloat(tgk_bunga.replace(/[^\d]/g, "")) || 0;
            var baki = parseFloat(baki_debet.replace(/[^\d]/g, "")) || 0;

            const jmlh = rbunga + baki

            var has = jmlh.toLocaleString("id-ID");
            $("#pn_plafons").val(has);
            $("#pn_plafon").val(has);
        })
        //--> KALKULASI TUNGGAKAN <--//

        $('#adm').on('input', function() {
            var inputValue = $(this).val();
            inputValue = inputValue.replace(/[^0-9.]/g, '');
            inputValue = inputValue.replace(/\.(?=.*\.)/g, '');

            inputValue = inputValue.replace(/^\./g, '0.');
            var number = parseFloat(inputValue);
        });

        //--> KALKULASI PENENTUAN PLAFON <--//
        $('#pk_dibayar, #bg_dibayar').keyup(function() {
            const pk_dibayar = parseFloat($('#pk_dibayar').val().replace(/[^\d]/g, "")) || 0;
            const bg_dibayar = parseFloat($('#bg_dibayar').val().replace(/[^\d]/g, "")) || 0;
            const penentuan_plafon = parseFloat($('#pn_plafons').val().replace(/[^\d]/g, "")) || 0;

            const jml = penentuan_plafon - (pk_dibayar + bg_dibayar);
            const bs = jml.toLocaleString("id-ID");

            $("#pn_plafon").val(bs);
        });

        //--> KALKULASI PENENTUAN PLAFON <--//

        //--> KALKULASI BIAYA RSC <--//
        $('#adm').on('input', function() {
            var inputValue = $(this).val();
            const persentase = parseFloat($("#persentases").val().replace(/[^\d]/g, ""))

            const jiwa = parseFloat($('#a_jiwa').val().replace(/[^\d]/g, "")) || 0
            const tlo = parseFloat($('#a_tlo').val().replace(/[^\d]/g, "")) || 0
            const den_dibayar = parseFloat($('#den_dibayar').val().replace(/[^\d]/g, "")) || 0
            const pok_dibayar = parseFloat($('#pok_dibayar').val().replace(/[^\d]/g, "")) || 0
            const bung_dibayar = parseFloat($('#bung_dibayar').val().replace(/[^\d]/g, "")) || 0


            var convertedValue = inputValue.replace(/,/g, '.');

            $(this).val(convertedValue);

            const dec = (parseFloat(persentase) * parseFloat(inputValue)) / 100;

            const hasil = Math.floor(dec);

            const bs = hasil.toLocaleString("id-ID");

            $("#persentase").val(bs);

            const jml = jiwa + tlo + den_dibayar + pok_dibayar + bung_dibayar + hasil;

            var jl = jml.toLocaleString("id-ID");
            $("#tot_biaya").val(jl);
        });


        $('#a_jiwa, #a_tlo, #den_dibayar, #tot_biaya, #pok_dibayar, #bung_dibayar, #ujroh').keyup(function() {
            const jiwa = parseFloat($('#a_jiwa').val().replace(/[^\d]/g, "")) || 0
            const tlo = parseFloat($('#a_tlo').val().replace(/[^\d]/g, "")) || 0
            const den_dibayar = parseFloat($('#den_dibayar').val().replace(/[^\d]/g, "")) || 0
            const pok_dibayar = parseFloat($('#pok_dibayar').val().replace(/[^\d]/g, "")) || 0
            const bung_dibayar = parseFloat($('#bung_dibayar').val().replace(/[^\d]/g, "")) || 0
            const persentase = parseFloat($("#persentase").val().replace(/[^\d]/g, "")) || 0
            const ujroh = parseFloat($("#ujroh").val().replace(/[^\d]/g, "")) || 0

            const jml = jiwa + tlo + den_dibayar + pok_dibayar + bung_dibayar + persentase + ujroh;
            console.log(ujroh)
            var bs = jml.toLocaleString("id-ID");
            $("#tot_biaya").val(bs);
        })

        $('#persentase').keyup(function() {
            var inputValue = $(this).val().replace(/[^\d]/g, "");
            const nominal = parseFloat($("#persentases").val().replace(/[^\d]/g, ""))

            const jml = inputValue / nominal * 100

            const formattedPercentage = jml.toFixed(2);

            const bs = parseFloat(formattedPercentage).toLocaleString("id-ID", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            $('#adm').val(formattedPercentage)
        })
        //--> KALKULASI BIAYA RSC <--//
    </script>
@endpush
