@extends('theme.app')
@section('title', 'Data Pengajuan')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-3">
                    @include('theme.menu-pengajuan', ['nasabah' => $data->kd_pengajuan])
                </div>

                <div class="col-xs-9">
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#pengajuan" data-toggle="tab">DATA PENGAJUAN</a>
                            </li>
                        </ul>

                        <form action="{{ route('pengajuan.storepengajuan', ['pengajuan' => $pengajuan->nasabah_kode]) }}"
                            method="POST">
                            @method('PUT')
                            @csrf
                            <div class="tab-content">

                                <div class="tab-pane active" id="pengajuan">
                                    <div class="box-body" style="margin-top: -10px;font-size:12px;">

                                        <div class="div-left">
                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">PLAFON</span>
                                                <input type="hidden" name="kode_pengajuan"
                                                    value="{{ $pengajuan->kode_pengajuan }}">
                                                <input type="hidden" value="{{ $pengajuan->auth }}" name="input_user">
                                                <input type="text" class="form-control" name="plafon" id="plafon"
                                                    placeholder="10.000.000"
                                                    value="{{ $pengajuan->plafon = 'Rp. ' . number_format($pengajuan->plafon, 0, ',', '.') }}">
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">PRODUK</span>
                                                <select class="form-control produk" style="width:100%;" name="produk_kode"
                                                    id="select-produk" required>
                                                    @if (is_null($pengajuan->produk_kode))
                                                        <option value="">--PILIH--</option>
                                                    @else
                                                        <option value="{{ $pengajuan->produk_kode }}">
                                                            {{ $pengajuan->produk_kode }} - {{ $pengajuan->produk_nama }}
                                                        </option>
                                                    @endif
                                                    @foreach ($produk as $item)
                                                        <option value="{{ $item->kode_produk }}">
                                                            {{ $item->kode_produk }} - {{ $item->nama_produk }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">METODE RPS</span>
                                                <select class="form-control text-uppercase" name="metode_rps"
                                                    id="select-metodes" required>
                                                    @if (is_null($pengajuan->metode_rps))
                                                        <option value="">--PILIH--</option>
                                                    @else
                                                        <option value="{{ $pengajuan->metode_rps }}">
                                                            {{ $pengajuan->metode_rps }}
                                                        </option>
                                                    @endif
                                                    <option value="FLAT"
                                                        {{ old('metode_rps') == 'FLAT' ? 'selected' : '' }}>
                                                        Flat
                                                    </option>
                                                    <option value="PRK"
                                                        {{ old('metode_rps') == 'PRK' ? 'selected' : '' }}>
                                                        PRK
                                                    </option>
                                                    <option value="EFEKTIF"
                                                        {{ old('metode_rps') == 'EFEKTIF' ? 'selected' : '' }}>
                                                        EFEKTIF
                                                    </option>
                                                    <option value="EFEKTIF ANUITAS"
                                                        {{ old('metode_rps') == 'EFEKTIF ANUITAS' ? 'selected' : '' }}>
                                                        EFEKTIF ANUITAS
                                                    </option>
                                                    <option value="EFEKTIF MUSIMAN"
                                                        {{ old('metode_rps') == 'EFEKTIF MUSIMAN' ? 'selected' : '' }}>
                                                        EFEKTIF MUSIMAN
                                                    </option>
                                                </select>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">SUKU BUNGA</span>
                                                <input type="text" class="form-control" name="suku_bunga" id="suku_bunga"
                                                    placeholder="ENTRI"
                                                    value="{{ old('suku_bunga', $pengajuan->suku_bunga) }}" required>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">NAMA CGC</span>
                                                <select class="form-control cgc" name="tabungan_cgc">
                                                    @if (is_null($pengajuan->tabungan_cgc))
                                                        <option value="">--PILIH--</option>
                                                    @else
                                                        <option value="{{ $pengajuan->tabungan_cgc }}">
                                                            {{ $pengajuan->tabungan_cgc . ' ' . '-' . ' ' . $pengajuan->namacgc }}
                                                        </option>
                                                    @endif

                                                    @foreach ($cgc as $item)
                                                        <option value="{{ $item->noacc }}">{{ $item->noacc }} -
                                                            {{ $item->fnama }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">KHUSUS KBT</span>
                                                <select class="form-control khsus_kbt" name="khsus_kbt" id="khsus_kbt">
                                                    <option value="">-- PILIH --</option>
                                                    <option value="PERLELEAN"
                                                        {{ old('khsus_kbt') == 'PERLELEAN' || $data->kondisi_khusus == 'PERLELEAN' ? 'selected' : '' }}>
                                                        PERLELEAN</option>
                                                    <option value="PERPADIAN"
                                                        {{ old('khsus_kbt') == 'PERPADIAN' || $data->kondisi_khusus == 'PERPADIAN' ? 'selected' : '' }}>
                                                        PERPADIAN</option>
                                                </select>
                                            </div>
                                        </div>


                                        <div class="div-right">
                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">JK KREDIT (BULAN)</span>
                                                <input type="number" class="form-control" name="jangka_waktu"
                                                    id="jangka_waktu" placeholder="ENTRI"
                                                    value="{{ $pengajuan->jangka_waktu }}" required>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">JK POKOK (BULAN)</span>
                                                <input type="number" class="form-control" name="jangka_pokok"
                                                    id="jangka_pokok" placeholder="ENTRI"
                                                    value="{{ old('jangka_pokok', $pengajuan->jangka_pokok) }}" required>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">JW BUNGA (BULAN)</span>
                                                <input type="number" class="form-control" name="jangka_bunga"
                                                    id="jangka_bunga" placeholder="ENTRI"
                                                    value="{{ old('jangka_bunga', $pengajuan->jangka_bunga) }}" required>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">PENGGUNAAN</span>
                                                <select class="form-control" name="penggunaan" id="select-penggunaan"
                                                    required>
                                                    @if (is_null($pengajuan->penggunaan))
                                                        <option value="">--PILIH--</option>
                                                    @else
                                                        <option value="{{ $pengajuan->penggunaan }}">
                                                            {{ $pengajuan->penggunaan }}
                                                        </option>
                                                    @endif
                                                    <option value="MODAL USAHA"
                                                        {{ old('penggunaan') == 'MODAL USAHA' ? 'selected' : '' }}>
                                                        MODAL USAHA
                                                    </option>
                                                    <option value="INVESTASI"
                                                        {{ old('penggunaan') == 'INVESTASI' ? 'selected' : '' }}>
                                                        INVESTASI
                                                    </option>
                                                    <option value="KONSUMTIF"
                                                        {{ old('penggunaan') == 'KONSUMTIF' ? 'selected' : '' }}>
                                                        KONSUMTIF
                                                    </option>
                                                    <option value="LAINNYA"
                                                        {{ old('penggunaan') == 'LAINNYA' ? 'selected' : '' }}>
                                                        LAINNYA
                                                    </option>
                                                </select>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:left;">
                                                <span class="fw-bold">RESORT</span>
                                                <select class="form-control resort" style="width:100%;"
                                                    name="resort_kode">
                                                    @if (is_null($pengajuan->resort_kode))
                                                        <option value="">--PILIH--</option>
                                                    @else
                                                        <option value="{{ $pengajuan->resort_kode }}">
                                                            {{ $pengajuan->nama_resort }}</option>
                                                    @endif

                                                    @foreach ($resort as $item)
                                                        <option value="{{ $item->kode_resort }}">{{ $item->nama_resort }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div style="margin-top:5px;width: 49.5%;float:right;">
                                                <span class="fw-bold">KETERANGAN</span>
                                                <input type="text" class="form-control" name="keterangan"
                                                    name="keterangan" placeholder="ENTRI"
                                                    value="{{ old('keterangan', $pengajuan->keterangan) }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @can('edit pengajuan kredit')
                                    <div class="box-body" style="margin-top:-20px;">
                                        <button type="submit" class="btn btn-sm btn-primary"
                                            style="margin-top:10px;width:100%">SIMPAN</button>
                                    </div>
                                @endcan
                            </div>
                        </form>

                        @can('otorisasi pengajuan kredit')
                            <form action="{{ route('otorpengajuan', ['otorisasi' => $pengajuan->kode_pengajuan]) }}"
                                method="POST">
                                @csrf
                                <div class="box-body" style="margin-top:-20px;">
                                    <input type="text" name='kode_pengajuan' value="{{ $data->kd_pengajuan }}" hidden>
                                    <button type="submit" class="btn btn-sm btn-primary"
                                        style="margin-top:10px;width:100%">OTORISASI</button>
                                </div>
                            </form>
                        @endcan

                    </div>
                </div>
        </section>
    </div>
@endsection

@push('myscript')
    <script>
        $('.produk').select2()
        $('.cgc').select2()
        $('.resort').select2()
        $('.khsus_kbt').select2()
    </script>
    <script>
        // Ketika pilihan sistem berubah
        $("#select-metodes").change(function() {
            var selectedValue = $(this).val();

            if (selectedValue === "FLAT") {

                // Jika dipilih "Sistem Flat Terelect"
                $("#jangka_pokok").val("1").prop("readonly", true);
                $("#jangka_bunga").val("1").prop("readonly", true);
            } else if (selectedValue === "EFEKTIF ANUITAS") {

                // Jika dipilih "Sistem Flat Terelect"
                $("#jangka_pokok").val("1").prop("readonly", true);
                $("#jangka_bunga").val("1").prop("readonly", true);
            } else {
                // Jika dipilih "Sistem Lainnya"
                $("#jangka_pokok").val("").prop("readonly", false);
                $("#jangka_bunga").val("").prop("readonly", false);
            }
        });

        //Validasi jangka poko dan jangka bunga tidak boleh lebih besar dari jangka kredit
        $("#jangka_pokok, #jangka_bunga").on("input", function() {
            var jangkaPoko = parseInt($("#jangka_pokok").val()) || 0;
            var jangkabunga = parseInt($("#jangka_bunga").val()) || 0;
            var jangkaKredit = parseInt($("#jangka_waktu").val()) || 0;

            if (jangkaPoko > jangkaKredit) {
                // Jika jangkaPoko lebih besar dari jangkaKredit
                $("#jangka_pokok").val(jangkaKredit);
            }

            if (jangkabunga > jangkaKredit) {
                // Jika jangkaPoko lebih besar dari jangkaKredit
                $("#jangka_bunga").val(jangkaKredit);
            }
        });

        $('#suku_bunga').on('input', function() {
            // Mengambil nilai dari input field
            var inputValue = $(this).val();

            // Mengganti koma (,) menjadi titik (.)
            var convertedValue = inputValue.replace(/,/g, '.');

            // Memasukkan nilai yang sudah diubah ke dalam input field
            $(this).val(convertedValue);
        });

        $("#select-produk").change(function() {
            var selectedValue = $(this).val();

            if (selectedValue !== "KBT") {
                $('.khsus_kbt').prop('disabled', true)
            } else {
                $('.khsus_kbt').prop('disabled', false)
            }
        });
    </script>
@endpush
