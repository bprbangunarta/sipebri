@extends('rsc.usaha_perdagangan.menu', [$data])
@section('title', 'Analisa Usaha Perdagangan RSC')

@section('content')
    <div class="tab-content">
        <div class="tab-pane active" id="identitas-usaha">
            <div class="box-body" style="margin-top: -10px;">

                <form action="{{ route('rsc.usaha.perdagangan.barang.simpan', ['kode_usaha' => $data->kode_usaha]) }}"
                    method="POST">
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
                                            <input class="form-control input-sm form-border" type="text"
                                                name="nama_barang1" id="nama_barang1" placeholder="Nama Item"
                                                value="{{ old('nama_barang1') }}" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border input-harga" type="text"
                                                name="hrg1" id="hrg1" placeholder="Rp. " value="{{ old('hrg1') }}"
                                                required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border" type="text" name="jual1"
                                                id="jual1" placeholder="Rp. " value="{{ old('jual1') }}" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border input-jual" type="text"
                                                name="laba1" id="laba1" placeholder="Rp. " readonly>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border text-center"
                                                style="width: 50px;" type="text" name="stock1" id="stock1"
                                                value="{{ old('stock1') }}" placeholder="0" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border text-center input-persen"
                                                style="width: 70px;" type="text" name="persen1" id="persen1" readonly
                                                placeholder="0.00%">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <input class="form-control input-sm form-border" type="text"
                                                name="nama_barang2" id="nama_barang2" placeholder="Nama Item"
                                                value="{{ old('nama_barang2') }}" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border input-harga" type="text"
                                                name="hrg2" id="hrg2" placeholder="Rp. " value="{{ old('hrg2') }}"
                                                required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border" type="text" name="jual2"
                                                id="jual2" placeholder="Rp. " value="{{ old('jual2') }}" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border input-jual" type="text"
                                                name="laba2" id="laba2" placeholder="Rp. " readonly>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border text-center"
                                                style="width: 50px;" type="text" name="stock2" id="stock2"
                                                value="{{ old('stock2') }}" placeholder="0" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border text-center input-persen"
                                                style="width: 70px;" type="text" name="persen2" id="persen2" readonly
                                                placeholder="0.00%">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <input class="form-control input-sm form-border" type="text"
                                                name="nama_barang3" id="nama_barang3" placeholder="Nama Item"
                                                value="{{ old('nama_barang3') }}" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border input-harga" type="text"
                                                name="hrg3" id="hrg3" placeholder="Rp. "
                                                value="{{ old('hrg3') }}" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border" type="text"
                                                name="jual3" id="jual3" placeholder="Rp. "
                                                value="{{ old('jual3') }}" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border input-jual" type="text"
                                                name="laba3" id="laba3" placeholder="Rp. " readonly>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border text-center"
                                                style="width: 50px;" type="text" name="stock3" id="stock3"
                                                value="{{ old('stock3') }}" placeholder="0" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border text-center input-persen"
                                                style="width: 70px;" type="text" name="persen3" id="persen3"
                                                readonly placeholder="0.00%">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <input class="form-control input-sm form-border" type="text"
                                                name="nama_barang4" id="nama_barang4" placeholder="Nama Item"
                                                value="{{ old('nama_barang4') }}" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border input-harga" type="text"
                                                name="hrg4" id="hrg4" placeholder="Rp. "
                                                value="{{ old('hrg4') }}" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border" type="text"
                                                name="jual4" id="jual4" placeholder="Rp. "
                                                value="{{ old('jual4') }}" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border input-jual" type="text"
                                                name="laba4" id="laba4" placeholder="Rp. " readonly>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border text-center"
                                                style="width: 50px;" type="text" name="stock4" id="stock4"
                                                value="{{ old('stock4') }}" placeholder="0" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border text-center input-persen"
                                                style="width: 70px;" type="text" name="persen4" id="persen4"
                                                readonly placeholder="0.00%">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <input class="form-control input-sm form-border" type="text"
                                                name="nama_barang5" id="nama_barang5" placeholder="Nama Item"
                                                value="{{ old('nama_barang5') }}" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border input-harga" type="text"
                                                name="hrg5" id="hrg5" placeholder="Rp. "
                                                value="{{ old('hrg5') }}" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border" type="text"
                                                name="jual5" id="jual5" placeholder="Rp. "
                                                value="{{ old('jual5') }}" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border input-jual" type="text"
                                                name="laba5" id="laba5" placeholder="Rp. " readonly>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border text-center"
                                                style="width: 50px;" type="text" name="stock5" id="stock5"
                                                value="{{ old('stock5') }}" placeholder="0" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border text-center input-persen"
                                                style="width: 70px;" type="text" name="persen5" id="persen5"
                                                readonly placeholder="0.00%">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <input class="form-control input-sm form-border" type="text"
                                                name="nama_barang6" id="nama_barang6" placeholder="Nama Item"
                                                value="{{ old('nama_barang6') }}" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border input-harga" type="text"
                                                name="hrg6" id="hrg6" placeholder="Rp. "
                                                value="{{ old('hrg6') }}" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border" type="text"
                                                name="jual6" id="jual6" placeholder="Rp. "
                                                value="{{ old('jual6') }}" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border input-jual" type="text"
                                                name="laba6" id="laba6" placeholder="Rp. " readonly>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border text-center"
                                                style="width: 50px;" type="text" name="stock6" id="stock6"
                                                value="{{ old('stock6') }}" placeholder="0" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border text-center input-persen"
                                                style="width: 70px;" type="text" name="persen6" id="persen6"
                                                readonly placeholder="0.00%">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <input class="form-control input-sm form-border" type="text"
                                                name="nama_barang7" id="nama_barang7" placeholder="Nama Item"
                                                value="{{ old('nama_barang7') }}" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border input-harga" type="text"
                                                name="hrg7" id="hrg7" placeholder="Rp. "
                                                value="{{ old('hrg7') }}" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border" type="text"
                                                name="jual7" id="jual7" placeholder="Rp. "
                                                value="{{ old('jual7') }}" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border input-jual" type="text"
                                                name="laba7" id="laba7" placeholder="Rp. " readonly>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border text-center"
                                                style="width: 50px;" type="text" name="stock7" id="stock7"
                                                value="{{ old('stock7') }}" placeholder="0" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border text-center input-persen"
                                                style="width: 70px;" type="text" name="persen7" id="persen7"
                                                readonly placeholder="0.00%">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <input class="form-control input-sm form-border" type="text"
                                                name="nama_barang8" id="nama_barang8" placeholder="Nama Item"
                                                value="{{ old('nama_barang8') }}" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border input-harga" type="text"
                                                name="hrg8" id="hrg8" placeholder="Rp. "
                                                value="{{ old('hrg8') }}" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border" type="text"
                                                name="jual8" id="jual8" placeholder="Rp. "
                                                value="{{ old('jual8') }}" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border input-jual" type="text"
                                                name="laba8" id="laba8" placeholder="Rp. " readonly>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border text-center"
                                                style="width: 50px;" type="text" name="stock8" id="stock8"
                                                value="{{ old('stock8') }}" placeholder="0" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border text-center input-persen"
                                                style="width: 70px;" type="text" name="persen8" id="persen8"
                                                readonly placeholder="0.00%">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <input class="form-control input-sm form-border" type="text"
                                                name="nama_barang9" id="nama_barang9" placeholder="Nama Item"
                                                value="{{ old('nama_barang9') }}" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border input-harga" type="text"
                                                name="hrg9" id="hrg9" placeholder="Rp. "
                                                value="{{ old('hrg9') }}" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border" type="text"
                                                name="jual9" id="jual9" placeholder="Rp. "
                                                value="{{ old('jual9') }}" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border input-jual" type="text"
                                                name="laba9" id="laba9" placeholder="Rp. " readonly>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border text-center"
                                                style="width: 50px;" type="text" name="stock9" id="stock9"
                                                value="{{ old('stock9') }}" placeholder="0" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border text-center input-persen"
                                                style="width: 70px;" type="text" name="persen9" id="persen9"
                                                readonly placeholder="0.00%">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <input class="form-control input-sm form-border" type="text"
                                                name="nama_barang10" id="nama_barang10" placeholder="Nama Item"
                                                value="{{ old('nama_barang10') }}" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border input-harga" type="text"
                                                name="hrg10" id="hrg10" placeholder="Rp. "
                                                value="{{ old('hrg10') }}" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border" type="text"
                                                name="jual10" id="jual10" placeholder="Rp. "
                                                value="{{ old('jual10') }}" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border input-jual" type="text"
                                                name="laba10" id="laba10" placeholder="Rp. " readonly>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border text-center"
                                                style="width: 50px;" type="text" name="stock10" id="stock10"
                                                value="{{ old('stock10') }}" placeholder="0" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm form-border text-center input-persen"
                                                style="width: 70px;" type="text" name="persen10" id="persen10"
                                                readonly placeholder="0.00%">
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
                                            id="tbeli" value="Rp." readonly>
                                    </td>
                                    <td>
                                        <input class="form-control input-sm" type="text" name="tjual"
                                            id="tjual" value="Rp." readonly>
                                    </td>
                                    <td>
                                        <input class="form-control input-sm" type="text" name="tlaba"
                                            id="tlaba" value="Rp." readonly>
                                    </td>
                                    <td>
                                        <input class="form-control input-sm text-center" style="width: 50px;"
                                            type="text" name="tstock" id="tstock" placeholder="0" readonly>
                                    </td>
                                    <td>
                                        <input class="form-control input-sm text-center" style="width: 70px;"
                                            type="text" name="tpersen" id="tpersen" placeholder="0.00%" readonly>
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
        <script>
            $('.lama_usaha').select2()
        </script>
    @endpush
