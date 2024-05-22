@extends('rsc.usaha_pertanian.menu', [$data])
@section('title', 'Biaya Usaha Pertanian RSC')

@section('content')
    <div class="tab-content">
        <div class="tab-pane active">

            <form action="{{ route('rsc.usaha.pertanian.biaya.update', ['kode_usaha' => $data->kode_usaha]) }}"
                method="POST">
                @method('post')
                @csrf
                <div class="box-body" style="margin-top: -10px;font-size:12px;">

                    <div class="div-left">
                        <div style="width: 49.5%;float:left;">
                            <span class="fw-bold">PENGOLAHAN TANAH</span>
                            <input class="form-control input-sm form-border" type="text" placeholder="Rp."
                                name="pengolahan_tanah" id="pengolahan"
                                value="{{ old('pengolahan_tanah', number_format($detail->pengolahan_tanah, 0, ',', '.')) }}">
                        </div>
                        <div style="width: 49.5%;float:right;">
                            <span class="fw-bold">BIAYA BIBIT</span>
                            <input class="form-control input-sm form-border" type="text" name="bibit" placeholder="Rp."
                                id="bibit" value="{{ old('bibit', number_format($detail->bibit, 0, ',', '.')) }}">
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">BIAYA PUPUK</span>
                            <input class="form-control input-sm form-border" type="text" name="pupuk" placeholder="Rp."
                                id="pupuk" value="{{ old('pupuk', number_format($detail->pupuk, 0, ',', '.')) }}">
                        </div>
                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">BIAYA PESTISIDA</span>
                            <input class="form-control input-sm form-border" type="text" placeholder="Rp."
                                name="pestisida" id="pestisida"
                                value="{{ old('pestisida', number_format($detail->pestisida, 0, ',', '.')) }}">
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">TENAGA KERJA</span>
                            <input class="form-control input-sm form-border" type="text" placeholder="Rp."
                                name="tenaga_kerja" id="tenaga_kerja"
                                value="{{ old('tenaga_kerja', number_format($detail->tenaga_kerja, 0, ',', '.')) }}">
                        </div>
                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">BIAYA PENGAIRAN</span>
                            <input class="form-control input-sm form-border" type="text" placeholder="Rp."
                                name="pengairan" id="pengairan"
                                value="{{ old('pengairan', number_format($detail->pengairan, 0, ',', '.')) }}">
                        </div>
                    </div>

                    <div class="div-right">
                        <div style="width: 49.5%;float:left;">
                            <span class="fw-bold">BIAYA PANEN</span>
                            <input class="form-control input-sm form-border" type="text" placeholder="Rp." name="panen"
                                id="panen" value="{{ old('panen', number_format($detail->panen, 0, ',', '.')) }}">
                        </div>
                        <div style="width: 49.5%;float:right;">
                            <span class="fw-bold">BIAYA PENGGARAP</span>
                            <input class="form-control input-sm form-border" type="text" placeholder="Rp."
                                name="penggarap" id="penggarap"
                                value="{{ old('penggarap', number_format($detail->penggarap, 0, ',', '.')) }}">
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">BIAYA PAJAK</span>
                            <input class="form-control input-sm form-border" type="text" placeholder="Rp." name="pajak"
                                id="pajak" value="{{ old('pajak', number_format($detail->pajak, 0, ',', '.')) }}">
                        </div>
                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">IUARAN DESA</span>
                            <input class="form-control input-sm form-border" type="text" placeholder="Rp."
                                name="iuran_desa" id="iuran_desa"
                                value="{{ old('iuran_desa', number_format($detail->iuran_desa, 0, ',', '.')) }}">
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">BIAYA AMORTISASI</span>
                            <input class="form-control input-sm form-border" type="text" placeholder="Rp."
                                name="amortisasi" id="amortisasi"
                                value="{{ old('amortisasi', number_format($detail->amortisasi, 0, ',', '.')) }}"
                                @if ($detail->luas_sewa === 0) @readonly(true) @endif>
                        </div>
                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">PINJAMAN BANK LAIN</span>
                            <input class="form-control input-sm form-border" type="text" placeholder="Rp."
                                name="pinjaman_bank" id="pinjaman_bank"
                                value="{{ old('pinjaman_bank', number_format($detail->pinjaman_bank, 0, ',', '.')) }}">
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
    <script src="{{ asset('assets/js/myscript/pertanian.js') }}"></script>

    <script>
        $("#lsewa").on('input', function() {
            var lsewaValue = $(this).val();
            var amortisasiInput = $("#amortisasi");

            if (lsewaValue === "") {
                // Jika input "lsewa" kosong, nonaktifkan dan kosongkan input "amortisasi"
                amortisasiInput.prop("readonly", true);
                amortisasiInput.val("");
            } else {
                // Jika input "lsewa" tidak kosong, aktifkan input "amortisasi"
                amortisasiInput.prop("readonly", false);
            }
        });
    </script>
@endpush
