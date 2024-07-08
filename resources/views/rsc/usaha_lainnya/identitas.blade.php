@extends('rsc.usaha_lainnya.menu', [$data])
@section('title', 'Analisa Usaha Lainnya RSC')

@section('content')
    <div class="tab-content">
        <div class="tab-pane active">

            <form action="{{ route('rsc.usaha.lain.identitas.update') }}" method="POST">
                @method('put')
                @csrf
                <div class="box-body" style="margin-top: -10px;font-size:12px;">

                    <div class="div-left">
                        <div>
                            <span class="fw-bold">KODE USAHA</span>
                            <input class="form-control input-sm form-border" type="text" name="kode_usaha"
                                value="{{ $lain->kode_usaha }}" readonly>
                        </div>

                        <div style="margin-top:5px;width: 100%;float:left;">
                            <span class="fw-bold">NAMA USAHA</span>
                            <input class="form-control input-sm form-border" type="text" name="nama_usaha" id=""
                                value="{{ $lain->nama_usaha ?? null }}">
                        </div>
                    </div>

                    <div class="div-right">
                        <div style="width: 49.5%;float:left;">
                            <span class="fw-bold">JENIS USAHA</span>
                            <select class="form-control input-sm form-border" name="jenis_usaha" id="select-usaha">
                                <option value="">--Pilih--</option>
                                <option value="MAKANAN"
                                    {{ old('jenis_usaha') == 'MAKANAN' || $lain->jenis_usaha == 'MAKANAN' ? 'selected' : '' }}>
                                    MAKANAN
                                </option>
                                <option value="PERIKANAN"
                                    {{ old('jenis_usaha') == 'PERIKANAN' || $lain->jenis_usaha == 'PERIKANAN' ? 'selected' : '' }}>
                                    PERIKANAN
                                </option>
                                <option value="PETERNAKAN"
                                    {{ old('jenis_usaha') == 'PETERNAKAN' || $lain->jenis_usaha == 'PETERNAKAN' ? 'selected' : '' }}>
                                    PETERNAKAN</option>
                                <option value="PERKEBUNAN"
                                    {{ old('jenis_usaha') == 'PERKEBUNAN' || $lain->jenis_usaha == 'PERKEBUNAN' ? 'selected' : '' }}>
                                    PERKEBUNAN</option>
                                <option value="PENGOLAHAN"
                                    {{ old('jenis_usaha') == 'PENGOLAHAN' || $lain->jenis_usaha == 'PENGOLAHAN' ? 'selected' : '' }}>
                                    PENGOLAHAN</option>
                                <option value="HOME INDUSTRI"
                                    {{ old('jenis_usaha') == 'HOME INDUSTRI' || $lain->jenis_usaha == 'HOME INDUSTRI' ? 'selected' : '' }}>
                                    HOME INDUSTRI</option>
                            </select>
                        </div>
                        <div style="width: 49.5%;float:right;">
                            <span class="fw-bold">LAMA USAHA</span>
                            <select class="form-control input-sm form-border" name="lama_usaha" id="">
                                <option value="">-- PIlih --</option>
                                <option value="1 Tahun"
                                    {{ old('lama_usaha') == '1 Tahun' || $lain->lama_usaha == '1 Tahun' ? 'selected' : '' }}>
                                    1 Tahun</option>
                                <option value="2 Tahun"
                                    {{ old('lama_usaha') == '2 Tahun' || $lain->lama_usaha == '2 Tahun' ? 'selected' : '' }}>
                                    2 Tahun</option>
                                <option value="3 Tahun"
                                    {{ old('lama_usaha') == '3 Tahun' || $lain->lama_usaha == '3 Tahun' ? 'selected' : '' }}>
                                    3 Tahun</option>
                                <option value="4 Tahun"
                                    {{ old('lama_usaha') == '4 Tahun' || $lain->lama_usaha == '4 Tahun' ? 'selected' : '' }}>
                                    4 Tahun</option>
                                <option value=">5 Tahun"
                                    {{ old('lama_usaha') == '5 Tahun' || $lain->lama_usaha == '>5 Tahun' ? 'selected' : '' }}>
                                    >5 Tahun</option>
                            </select>
                        </div>

                        <div style="margin-top:5px;width: 100%;float:right;">
                            <span class="fw-bold">ALAMAT USAHA</span>
                            <input class="form-control input-sm form-border" type="text" name="lokasi_usaha"
                                value="{{ $lain->lokasi_usaha ?? $data->alamat_ktp }}">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-sm btn-primary" style="margin-top:10px;width:100%">SIMPAN</button>
                </div>
            </form>


        </div>
    </div>
@endsection

@push('myscript')
    <script src="{{ asset('assets/js/myscript/lainnya.js') }}"></script>
@endpush
