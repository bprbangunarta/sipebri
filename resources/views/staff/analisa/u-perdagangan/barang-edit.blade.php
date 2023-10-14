@extends('staff.analisa.u-perdagangan.menu', [$data, 'kode_usaha' => $perdagangan->kd_usaha, 'pengajuan' => $data->kd_pengajuan])
@section('title', 'Analisa Usaha Perdagangan')

@section('content')
    <div class="tab-content">
        <div class="tab-pane active" id="identitas-usaha">
            <div class="box-body" style="margin-top: -10px;">

                <form action="{{ route('perdagangan.updatebarang', ['kode_usaha' => $perdagangan->kd_usaha]) }}"
                    method="POST">
                    @method('put')
                    @csrf
                    <div class="box-body table-responsive no-padding">
                        <div style="overflow: auto; width: 100%; height: 355px;">
                            <table class="table table-hover" style="font-size: 12px;">
                                <thead>
                                    <tr>
                                        <th class="text-center">NAMA BARANG</th>
                                        <th class="text-center">HARGA BELI</th>
                                        <th class="text-center">HARGA JUAL</th>
                                        <th class="text-center">LABA</th>
                                        <th class="text-center">STOK</th>
                                        <th class="text-center">%</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <input type="text" value="{{ $barang[0]->kode_barang }}" name="kode_barang1"
                                                hidden>
                                            <input class="form-control input-sm form-border" type="text"
                                                name="nama_barang1" id="nama_barang1" placeholder="Nama Item"
                                                value="{{ old('nama_barang1') ?? $barang[0]->nama_barang }}" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border input-harga" type="text"
                                                name="hrg1" id="hrg1" placeholder="Rp. "
                                                value="{{ old('hrg1') ?? ($barang[0]->harga_beli = 'Rp. ' . number_format($barang[0]->harga_beli, 0, ',', '.')) }}"
                                                required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border" type="text" name="jual1"
                                                id="jual1" placeholder="Rp. "
                                                value="{{ old('jual1') ?? ($barang[0]->harga_jual = 'Rp. ' . number_format($barang[0]->harga_jual, 0, ',', '.')) }}"
                                                required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border input-jual" type="text"
                                                name="laba1" id="laba1" placeholder="Rp. "
                                                value="{{ old('laba1') ?? ($barang[0]->laba = 'Rp. ' . number_format($barang[0]->laba, 0, ',', '.')) }}"
                                                readonly>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border text-center"
                                                style="width: 50px;" type="text" name="stock1" id="stock1"
                                                value="{{ $barang[0]->stok }}" placeholder="0" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border text-center input-persen"
                                                style="width: 70px;" type="text" name="persen1" id="persen1"
                                                value="{{ $barang[0]->presentase_laba . '%' }}" readonly
                                                placeholder="0.00%">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <input type="text" value="{{ $barang[1]->kode_barang }}" name="kode_barang2"
                                                hidden>
                                            <input class="form-control input-sm form-border" type="text"
                                                name="nama_barang2" id="nama_barang2" placeholder="Nama Item"
                                                value="{{ old('nama_barang2') ?? $barang[1]->nama_barang }}" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border input-harga" type="text"
                                                name="hrg2" id="hrg2" placeholder="Rp. "
                                                value="{{ old('hrg2') ?? ($barang[1]->harga_beli = 'Rp. ' . number_format($barang[1]->harga_beli, 0, ',', '.')) }}"
                                                required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border" type="text" name="jual2"
                                                id="jual2" placeholder="Rp. "
                                                value="{{ old('jual2') ?? ($barang[1]->harga_jual = 'Rp. ' . number_format($barang[1]->harga_jual, 0, ',', '.')) }}"
                                                required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border input-jual" type="text"
                                                name="laba2" id="laba2" placeholder="Rp. "
                                                value="{{ old('jual2') ?? ($barang[1]->laba = 'Rp. ' . number_format($barang[1]->laba, 0, ',', '.')) }}"
                                                readonly>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border text-center"
                                                style="width: 50px;" type="text" name="stock2" id="stock2"
                                                value="{{ $barang[1]->stok }}" placeholder="0" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border text-center input-persen"
                                                style="width: 70px;" type="text" name="persen2" id="persen2"
                                                value="{{ $barang[1]->presentase_laba . '%' }}" readonly
                                                placeholder="0.00%">
                                        </td>
                                    </tr>

                                    <tr>
                                        <input type="text" value="{{ $barang[2]->kode_barang }}" name="kode_barang3"
                                            hidden>
                                        <td>
                                            <input class="form-control input-sm form-border" type="text"
                                                name="nama_barang3" id="nama_barang3" placeholder="Nama Item"
                                                value="{{ old('nama_barang3') ?? $barang[2]->nama_barang }}" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border input-harga" type="text"
                                                name="hrg3" id="hrg3" placeholder="Rp. "
                                                value="{{ old('hrg3') ?? ($barang[2]->harga_beli = 'Rp. ' . number_format($barang[2]->harga_beli, 0, ',', '.')) }}"
                                                required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border" type="text"
                                                name="jual3" id="jual3" placeholder="Rp. "
                                                value="{{ old('jual3') ?? ($barang[2]->harga_jual = 'Rp. ' . number_format($barang[2]->harga_jual, 0, ',', '.')) }}"
                                                required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border input-jual" type="text"
                                                name="laba3" id="laba3" placeholder="Rp. "
                                                value="{{ old('laba3') ?? ($barang[2]->laba = 'Rp. ' . number_format($barang[2]->laba, 0, ',', '.')) }}"
                                                readonly>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border text-center"
                                                style="width: 50px;" type="text" name="stock3" id="stock3"
                                                value="{{ $barang[2]->stok }}" placeholder="0" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border text-center input-persen"
                                                style="width: 70px;" type="text" name="persen3" id="persen3"
                                                value="{{ $barang[2]->presentase_laba . '%' }}" readonly
                                                placeholder="0.00%">
                                        </td>
                                    </tr>

                                    <tr>
                                        <input type="text" value="{{ $barang[3]->kode_barang }}" name="kode_barang4"
                                            hidden>
                                        <td>
                                            <input class="form-control input-sm form-border" type="text"
                                                name="nama_barang4" id="nama_barang4" placeholder="Nama Item"
                                                value="{{ old('nama_barang4') ?? $barang[3]->nama_barang }}" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border input-harga" type="text"
                                                name="hrg4" id="hrg4" placeholder="Rp. "
                                                value="{{ old('hrg4') ?? ($barang[3]->harga_beli = 'Rp. ' . number_format($barang[3]->harga_beli, 0, ',', '.')) }}"
                                                required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border" type="text"
                                                name="jual4" id="jual4" placeholder="Rp. "
                                                value="{{ old('jual4') ?? ($barang[3]->harga_jual = 'Rp. ' . number_format($barang[3]->harga_jual, 0, ',', '.')) }}"
                                                required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border input-jual" type="text"
                                                name="laba4" id="laba4"
                                                value="{{ old('laba3') ?? ($barang[3]->laba = 'Rp. ' . number_format($barang[3]->laba, 0, ',', '.')) }}"
                                                placeholder="Rp. " readonly>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border text-center"
                                                style="width: 50px;" type="text" name="stock4" id="stock4"
                                                value="{{ $barang[3]->stok }}" placeholder="0" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border text-center input-persen"
                                                style="width: 70px;" type="text" name="persen4" id="persen4"
                                                value="{{ $barang[3]->presentase_laba . '%' }}" readonly
                                                placeholder="0.00%">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <input type="text" value="{{ $barang[4]->kode_barang }}"
                                                name="kode_barang5" hidden>
                                            <input class="form-control input-sm form-border" type="text"
                                                name="nama_barang5" id="nama_barang5" placeholder="Nama Item"
                                                value="{{ old('nama_barang5') ?? $barang[4]->nama_barang }}" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border input-harga" type="text"
                                                name="hrg5" id="hrg5" placeholder="Rp. "
                                                value="{{ old('hrg5') ?? ($barang[4]->harga_beli = 'Rp. ' . number_format($barang[4]->harga_beli, 0, ',', '.')) }}"
                                                required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border" type="text"
                                                name="jual5" id="jual5" placeholder="Rp. "
                                                value="{{ old('jual5') ?? ($barang[4]->harga_jual = 'Rp. ' . number_format($barang[4]->harga_jual, 0, ',', '.')) }}"
                                                required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border input-jual" type="text"
                                                name="laba5" id="laba5"
                                                value="{{ old('laba5') ?? ($barang[4]->laba = 'Rp. ' . number_format($barang[4]->laba, 0, ',', '.')) }}"
                                                placeholder="Rp. " readonly>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border text-center"
                                                style="width: 50px;" type="text" name="stock5" id="stock5"
                                                value="{{ $barang[4]->stok }}" placeholder="0" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border text-center input-persen"
                                                style="width: 70px;" type="text" name="persen5" id="persen5"
                                                value="{{ $barang[4]->presentase_laba . '%' }}" readonly
                                                placeholder="0.00%">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <input type="text" value="{{ $barang[5]->kode_barang }}"
                                                name="kode_barang6" hidden>
                                            <input class="form-control input-sm form-border" type="text"
                                                name="nama_barang6" id="nama_barang6" placeholder="Nama Item"
                                                value="{{ old('nama_barang6') ?? $barang[5]->nama_barang }}" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border input-harga" type="text"
                                                name="hrg6" id="hrg6" placeholder="Rp. "
                                                value="{{ old('hrg6') ?? ($barang[5]->harga_beli = 'Rp. ' . number_format($barang[5]->harga_beli, 0, ',', '.')) }}"
                                                required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border" type="text"
                                                name="jual6" id="jual6" placeholder="Rp. "
                                                value="{{ old('jual6') ?? ($barang[5]->harga_jual = 'Rp. ' . number_format($barang[5]->harga_jual, 0, ',', '.')) }}"
                                                required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border input-jual" type="text"
                                                name="laba6" id="laba6"
                                                value="{{ old('laba6') ?? ($barang[5]->laba = 'Rp. ' . number_format($barang[5]->laba, 0, ',', '.')) }}"
                                                placeholder="Rp. " readonly>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border text-center"
                                                style="width: 50px;" type="text" name="stock6" id="stock6"
                                                value="{{ $barang[5]->stok }}" placeholder="0" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border text-center input-persen"
                                                style="width: 70px;" type="text" name="persen6" id="persen6"
                                                value="{{ $barang[5]->presentase_laba . '%' }}" readonly
                                                placeholder="0.00%">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <input type="text" value="{{ $barang[6]->kode_barang }}"
                                                name="kode_barang7" hidden>
                                            <input class="form-control input-sm form-border" type="text"
                                                name="nama_barang7" id="nama_barang7" placeholder="Nama Item"
                                                value="{{ old('nama_barang7') ?? $barang[6]->nama_barang }}" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border input-harga" type="text"
                                                name="hrg7" id="hrg7" placeholder="Rp. "
                                                value="{{ old('hrg7') ?? ($barang[6]->harga_beli = 'Rp. ' . number_format($barang[6]->harga_beli, 0, ',', '.')) }}"
                                                required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border" type="text"
                                                name="jual7" id="jual7" placeholder="Rp. "
                                                value="{{ old('jual7') ?? ($barang[6]->harga_jual = 'Rp. ' . number_format($barang[6]->harga_jual, 0, ',', '.')) }}"
                                                required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border input-jual" type="text"
                                                name="laba7" id="laba7"
                                                value="{{ old('laba7') ?? ($barang[6]->laba = 'Rp. ' . number_format($barang[6]->laba, 0, ',', '.')) }}"
                                                placeholder="Rp. " readonly>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border text-center"
                                                style="width: 50px;" type="text" name="stock7" id="stock7"
                                                value="{{ $barang[6]->stok }}" placeholder="0" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border text-center input-persen"
                                                style="width: 70px;" type="text" name="persen7" id="persen7"
                                                value="{{ $barang[6]->presentase_laba . '%' }}" readonly
                                                placeholder="0.00%">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <input type="text" value="{{ $barang[7]->kode_barang }}"
                                                name="kode_barang8" hidden>
                                            <input class="form-control input-sm form-border" type="text"
                                                name="nama_barang8" id="nama_barang8" placeholder="Nama Item"
                                                value="{{ old('nama_barang8') ?? $barang[7]->nama_barang }}" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border input-harga" type="text"
                                                name="hrg8" id="hrg8" placeholder="Rp. "
                                                value="{{ old('hrg8') ?? ($barang[7]->harga_beli = 'Rp. ' . number_format($barang[7]->harga_beli, 0, ',', '.')) }}"
                                                required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border" type="text"
                                                name="jual8" id="jual8" placeholder="Rp. "
                                                value="{{ old('jual8') ?? ($barang[7]->harga_jual = 'Rp. ' . number_format($barang[7]->harga_jual, 0, ',', '.')) }}"
                                                required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border input-jual" type="text"
                                                name="laba8" id="laba8"
                                                value="{{ old('laba8') ?? ($barang[7]->laba = 'Rp. ' . number_format($barang[7]->laba, 0, ',', '.')) }}"
                                                placeholder="Rp. " readonly>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border text-center"
                                                style="width: 50px;" type="text" name="stock8" id="stock8"
                                                value="{{ $barang[7]->stok }}" placeholder="0" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border text-center input-persen"
                                                style="width: 70px;" type="text" name="persen8" id="persen8"
                                                value="{{ $barang[7]->presentase_laba . '%' }}" readonly
                                                placeholder="0.00%">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <input type="text" value="{{ $barang[8]->kode_barang }}"
                                                name="kode_barang9" hidden>
                                            <input class="form-control input-sm form-border" type="text"
                                                name="nama_barang9" id="nama_barang9" placeholder="Nama Item"
                                                value="{{ old('nama_barang9') ?? $barang[8]->nama_barang }}" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border input-harga" type="text"
                                                name="hrg9" id="hrg9" placeholder="Rp. "
                                                value="{{ old('hrg9') ?? ($barang[8]->harga_beli = 'Rp. ' . number_format($barang[8]->harga_beli, 0, ',', '.')) }}"
                                                required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border" type="text"
                                                name="jual9" id="jual9" placeholder="Rp. "
                                                value="{{ old('jual9') ?? ($barang[8]->harga_jual = 'Rp. ' . number_format($barang[8]->harga_jual, 0, ',', '.')) }}"
                                                required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border input-jual" type="text"
                                                name="laba9" id="laba9"
                                                value="{{ old('laba9') ?? ($barang[8]->laba = 'Rp. ' . number_format($barang[8]->laba, 0, ',', '.')) }}"
                                                placeholder="Rp. " readonly>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border text-center"
                                                style="width: 50px;" type="text" name="stock9" id="stock9"
                                                value="{{ $barang[8]->stok }}" placeholder="0" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border text-center input-persen"
                                                style="width: 70px;" type="text" name="persen9" id="persen9"
                                                value="{{ $barang[8]->presentase_laba . '%' }}" readonly
                                                placeholder="0.00%">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <input type="text" value="{{ $barang[9]->kode_barang }}"
                                                name="kode_barang10" hidden>
                                            <input class="form-control input-sm form-border" type="text"
                                                name="nama_barang10" id="nama_barang10" placeholder="Nama Item"
                                                value="{{ old('nama_barang10') ?? $barang[9]->nama_barang }}" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border input-harga" type="text"
                                                name="hrg10" id="hrg10" placeholder="Rp. "
                                                value="{{ old('hrg10') ?? ($barang[9]->harga_beli = 'Rp. ' . number_format($barang[9]->harga_beli, 0, ',', '.')) }}"
                                                required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border" type="text"
                                                name="jual10" id="jual10" placeholder="Rp. "
                                                value="{{ old('jual10') ?? ($barang[9]->harga_jual = 'Rp. ' . number_format($barang[9]->harga_jual, 0, ',', '.')) }}"
                                                required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border input-jual" type="text"
                                                name="laba10" id="laba10"
                                                value="{{ old('laba10') ?? ($barang[9]->laba = 'Rp. ' . number_format($barang[9]->laba, 0, ',', '.')) }}"
                                                placeholder="Rp. " readonly>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border text-center"
                                                style="width: 50px;" type="text" name="stock10" id="stock10"
                                                value="{{ $barang[9]->stok }}" placeholder="0" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border text-center input-persen"
                                                style="width: 70px;" type="text" name="persen10" id="persen10"
                                                value="{{ $barang[9]->presentase_laba . '%' }}" readonly
                                                placeholder="0.00%">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <table class="table table-hover" style="font-size: 12px;">
                            <thead>
                                <tr>
                                    <th class="text-center">KALKULASI</th>
                                    <th class="text-center">TOTAL BELI</th>
                                    <th class="text-center">TOTAL JUAL</th>
                                    <th class="text-center">TOTAL LABA</th>
                                    <th class="text-center">STOK</th>
                                    <th class="text-center">%</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <input class="form-control input-sm" type="text" name=""
                                            id="" value="Semua Barang" readonly>
                                    </td>
                                    <td>
                                        <input class="form-control input-sm" type="text" name="tbeli"
                                            id="tbeli"
                                            value="{{ $perdagangan->total_beli = 'Rp. ' . number_format($perdagangan->total_beli, 0, ',', '.') }}"
                                            readonly>
                                    </td>
                                    <td>
                                        <input class="form-control input-sm" type="text" name="tjual"
                                            id="tjual"
                                            value="{{ $perdagangan->total_jual = 'Rp. ' . number_format($perdagangan->total_jual, 0, ',', '.') }}"
                                            readonly>
                                    </td>
                                    <td>
                                        <input class="form-control input-sm" type="text" name="tlaba"
                                            id="tlaba"
                                            value="{{ $perdagangan->total_laba = 'Rp. ' . number_format($perdagangan->total_laba, 0, ',', '.') }}"
                                            readonly>
                                    </td>
                                    <td>
                                        <input class="form-control input-sm text-center" style="width: 50px;"
                                            type="text" name="tstock" id="tstock"
                                            value="{{ $perdagangan->total_stok = number_format($perdagangan->total_stok, 0, ',', '.') }}"
                                            placeholder="0" readonly>
                                    </td>
                                    <td>
                                        <input class="form-control input-sm text-center" style="width: 70px;"
                                            type="text" name="tpersen" id="tpersen"
                                            value="{{ number_format($perdagangan->total_pl, 2) . '%' }}"
                                            placeholder="0.00%" readonly>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <button type="submit" class="btn btn-sm btn-primary"
                        style="margin-top:10px;width:100%;">SIMPAN</button>
                </form>

            </div>
        </div>
    @endsection

    @push('myscript')
        <script src="{{ asset('assets/js/myscript/perdagangan.js') }}"></script>
    @endpush
