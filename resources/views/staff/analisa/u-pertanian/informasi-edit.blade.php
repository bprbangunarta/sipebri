@extends('staff.analisa.u-pertanian.menu', [$data, 'kode_usaha' => $pertanian->kd_usaha, 'pengajuan' => $data->kd_pengajuan])
@section('title', 'Analisa Usaha Pertanian')

@section('content')
    <div class="tab-content">
        <div class="tab-pane active">

            <form action="{{ route('pertanian.updateinformasi', ['kode_usaha' => $pertanian->kd_usaha]) }}" method="POST">
                @method('put')
                @csrf
                <div class="box-body" style="margin-top: -10px;font-size:12px;">

                    {{-- <input type="text" id="plafon" value="{{ $data->plafon }}" hidden>
                <input type="text" id="jangka_waktu" value="{{ $data->jangka_waktu }}" hidden> --}}

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
                                value="{{ old('lokasi_usaha') ?? $pertanian->lokasi_usaha }}">
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">SEKTOR EKONOMI</span>
                            <select class="form-control input-sm form-border" name="jenis_usaha" id="">
                                @if (is_null($pertanian->jenis_usaha))
                                    <option value="">--PILIH--</option>
                                @else
                                    <option value="{{ $pertanian->jenis_usaha }}">{{ $pertanian->jenis_usaha }}</option>
                                @endif
                                <option value="PERTANIAN" {{ old('jenis_usaha') == 'PERTANIAN' ? 'selected' : '' }}>
                                    PERTANIAN
                                </option>
                                <option value="PERKEBUNAN" {{ old('jenis_usaha') == 'PERKEBUNAN' ? 'selected' : '' }}>
                                    PERKEBUNAN</option>
                            </select>
                        </div>
                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">JENIS TANAMAN</span>
                            <select class="form-control input-sm form-border" name="jenis_tanaman" id="">
                                @if (is_null($detail->jenis_tanaman))
                                    <option value="">--PILIH--</option>
                                @else
                                    <option value="{{ $detail->jenis_tanaman }}">{{ $detail->jenis_tanaman }}</option>
                                @endif
                                <option value="PADI KETAN" {{ old('jenis_tanaman') == 'PADI KETAN' ? 'selected' : '' }}>
                                    PADI
                                    KETAN</option>
                                <option value="PADI INPARI" {{ old('jenis_tanaman') == 'PADI INPARI' ? 'selected' : '' }}>
                                    PADI INPARI</option>
                            </select>
                        </div>
                    </div>

                    <div class="div-right">
                        <div style="width: 49.5%;float:left;">
                            <span class="fw-bold">LUAS MILIK SENDIRI</span>
                            <input class="form-control input-sm form-border" type="text" name="luas_sendiri"
                                id="lsendiri" placeholder="0"
                                value="{{ old('luas_sendiri') ?? number_format($detail->luas_sendiri, 0, ',', '.') }}">
                        </div>
                        <div style="width: 49.5%;float:right;">
                            <span class="fw-bold">LUAS HASIL SEWA</span>
                            <input class="form-control input-sm form-border" type="text" name="luas_sewa" id="lsewa"
                                placeholder="0"
                                value="{{ old('luas_sewa') ?? number_format($detail->luas_sewa, 0, ',', '.') }}">
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">LUAS HASIL GADAI</span>
                            <input class="form-control input-sm form-border" type="text" name="luas_gadai" id="lgadai"
                                placeholder="0"
                                value="{{ old('luas_gadai') ?? number_format($detail->luas_gadai, 0, ',', '.') }}">
                        </div>
                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">TOTAL LUAS TANAH</span>
                            <input class="form-control input-sm form-border" type="text" name="total_tanah"
                                id="total_tanah"
                                value="{{ old('lokasi_usaha') ?? number_format($detail->total_luas, 0, ',', '.') }}">
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">HASIL PANEN PER KW</span>
                            <input class="form-control input-sm form-border" type="text" name="hasil_panen"
                                id="hpanen" placeholder="0" value="{{ old('hasil_panen') ?? $detail->hasil_panen }}">
                        </div>
                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">HARGA PER KWINTAN</span>
                            <input class="form-control input-sm form-border" type="text" name="harga" id="hrg"
                                placeholder="Rp."
                                value="{{ old('harga') ?? ($detail->harga = 'Rp. ' . number_format($detail->harga, 0, ',', '.')) }}">
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
