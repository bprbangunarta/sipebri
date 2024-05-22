@extends('rsc.usaha_pertanian.menu', [$data])
@section('title', 'Informasi Usaha Pertanian RSC')

@section('content')
    <div class="tab-content">
        <div class="tab-pane active">

            <form action="{{ route('rsc.usaha.pertanian.informasi.update', ['rsc' => $data->rsc]) }}" method="POST">
                @method('post')
                @csrf
                <div class="box-body" style="margin-top: -10px;font-size:12px;">

                    <div class="div-left">
                        <div>
                            <span class="fw-bold">KODE USAHA</span>
                            <input class="form-control input-sm form-border" type="text" name="kode_usaha"
                                value="{{ $pertanian->kode_usaha }}" readonly>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">NAMA USAHA</span>
                            <input class="form-control input-sm form-border" type="text" name="nama_usaha"
                                value="{{ $pertanian->nama_usaha }}">
                        </div>
                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">ALAMAT USAHA</span>
                            <input class="form-control input-sm form-border" type="text" name="lokasi_usaha"
                                value="{{ old('lokasi_usaha') ?? $data->alamat_ktp }}">
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">SEKTOR EKONOMI</span>
                            <select class="form-control input-sm form-border" name="jenis_usaha" id="" required>
                                <option value="">--PILIH--</option>
                                <option value="PERTANIAN"
                                    {{ old('jenis_usaha') == 'PERTANIAN' || $pertanian->jenis_usaha == 'PERTANIAN' ? 'selected' : '' }}>
                                    PERTANIAN
                                </option>
                                <option value="PERKEBUNAN"
                                    {{ old('jenis_usaha') == 'PERKEBUNAN' || $pertanian->jenis_usaha == 'PERKEBUNAN' ? 'selected' : '' }}>
                                    PERKEBUNAN</option>
                            </select>
                        </div>
                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">JENIS TANAMAN</span>
                            <select class="form-control input-sm form-border" name="jenis_tanaman" id="" required>
                                <option value="">--PILIH--</option>
                                <option value="PADI KETAN"
                                    {{ old('jenis_tanaman') == 'PADI KETAN' || $detail->jenis_tanaman == 'PADI KETAN' ? 'selected' : '' }}>
                                    PADI
                                    KETAN</option>
                                <option value="PADI INPARI"
                                    {{ old('jenis_tanaman') == 'PADI INPARI' || $detail->jenis_tanaman == 'PADI INPARI' ? 'selected' : '' }}>
                                    PADI INPARI</option>
                                <option value="CIHERANG"
                                    {{ old('jenis_tanaman') == 'CIHERANG' || $detail->jenis_tanaman == 'CIHERANG' ? 'selected' : '' }}>
                                    PADI CIHERANG</option>
                                <option value="PADI 42"
                                    {{ old('jenis_tanaman') == 'PADI 42' || $detail->jenis_tanaman == 'PADI 42' ? 'selected' : '' }}>
                                    PADI 42</option>
                                <option value="PADI IR64"
                                    {{ old('jenis_tanaman') == 'PADI IR64' || $detail->jenis_tanaman == 'PADI IR64' ? 'selected' : '' }}>
                                    PADI IR64</option>
                                <option value="PADI MUNCUL"
                                    {{ old('jenis_tanaman') == 'PADI MUNCUL' || $detail->jenis_tanaman == 'PADI MUNCUL' ? 'selected' : '' }}>
                                    PADI MUNCUL</option>
                                <option value="PADI PANDAN WANGI"
                                    {{ old('jenis_tanaman') == 'PADI PANDAN WANGI' || $detail->jenis_tanaman == 'PADI PANDAN WANGI' ? 'selected' : '' }}>
                                    PADI PANDAN WANGI</option>
                                <option value="LAINNYA"
                                    {{ old('jenis_tanaman') == 'LAINNYA' || $detail->jenis_tanaman == 'PADI PANDAN WANGI' ? 'selected' : '' }}>
                                    LAINNYA</option>
                            </select>
                        </div>
                    </div>

                    <div class="div-right">
                        <div style="width: 49.5%;float:left;">
                            <span class="fw-bold">LUAS MILIK SENDIRI</span>
                            <input class="form-control input-sm form-border" type="text" name="luas_sendiri"
                                id="lsendiri" placeholder="0"
                                value="{{ old('luas_sendiri', number_format($detail->luas_sendiri, '0', ',', '.')) }}">
                        </div>
                        <div style="width: 49.5%;float:right;">
                            <span class="fw-bold">LUAS HASIL SEWA</span>
                            <input class="form-control input-sm form-border" type="text" name="luas_sewa" id="lsewa"
                                placeholder="0"
                                value="{{ old('luas_sewa', number_format($detail->luas_sewa, '0', ',', '.')) }}">
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">LUAS HASIL GADAI</span>
                            <input class="form-control input-sm form-border" type="text" name="luas_gadai" id="lgadai"
                                placeholder="0"
                                value="{{ old('luas_gadai', number_format($detail->luas_gadai, '0', ',', '.')) }}">
                        </div>
                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">TOTAL LUAS TANAH</span>
                            <input class="form-control input-sm form-border" type="text" name="total_tanah"
                                id="total_tanah"
                                value="{{ old('lokasi_usaha', number_format($detail->total_luas, '0', ',', '.')) ?? 0 }}">
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">HASIL PANEN PER KW</span>
                            <input class="form-control input-sm form-border" type="text" name="hasil_panen"
                                id="hpanen" placeholder="0"
                                value="{{ old('hasil_panen', number_format($detail->hasil_panen, '0', ',', '.')) }}">
                        </div>
                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">HARGA PER KWINTAN</span>
                            <input class="form-control input-sm form-border" type="text" name="harga" id="hrg"
                                placeholder="Rp." value="{{ old('harga', number_format($detail->harga, '0', ',', '.')) }}">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-sm btn-primary" style="margin-top:10px;width:100%">SIMPAN</button>
                </div>
            </form>


        </div>
    </div>
@endsection

@push('myscript')
    <script src="{{ asset('assets/js/myscript/pertanian.js') }}"></script>
@endpush
