@extends('templates.app')
@section('title', 'Analisa Usaha Pergagangan')
@yield('jquery')
@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="container-xl">
                                <div class="row g-2 align-items-center">

                                    @include('templates.header-analisa', [
                                        'pengajuan' => $data->kd_pengajuan,
                                    ])

                                    <div class="col-auto ms-auto d-print-none">
                                        <div class="btn-list">
                                            <a href="{{ route('analisa.usaha.perdagangan') }}" class="btn btn-primary">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="icon icon-tabler icon-tabler-arrow-left" width="24"
                                                    height="24" viewBox="0 0 24 24" stroke-width="2"
                                                    stroke="currentColor" fill="none" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <path d="M5 12l14 0"></path>
                                                    <path d="M5 12l6 6"></path>
                                                    <path d="M5 12l6 -6"></path>
                                                </svg>
                                                Kembali
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body border-bottom py-3">
                            <div class="card">
                                <div class="row g-0">

                                    @include('templates.menu-analisa', [
                                        'pengajuan' => $data->kd_pengajuan,
                                    ])

                                    <div class="col d-flex flex-column">
                                        <form action="{{ route('tambah.detail_store', ['usaha' => $data->kd_nasabah]) }}"
                                            method="POST">
                                            @csrf
                                            <div class="card-body">

                                                <table class="table table-bordered table-vcenter fs-5">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" colspan="6">Informasi Barang Dagang
                                                            </th>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-center">Nama Barang</th>
                                                            <th class="text-center">Harga Beli</th>
                                                            <th class="text-center">Harga Jual</th>
                                                            <th class="text-center">Laba</th>
                                                            <th class="text-center" width="15%">Stok</th>
                                                            <th class="text-center" width="15%">%</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><input class="form-control" type="text"
                                                                    name="nama_barang1" id="nama_barang1"
                                                                    placeholder="Nama Item"
                                                                    value="{{ old('nama_barang1') }}" required></td>
                                                            <td><input class="form-control input-harga" type="text"
                                                                    name="hrg1" id="hrg1" placeholder="Nominal"
                                                                    value="{{ old('hrg1') }}" required>
                                                            </td>
                                                            <td><input class="form-control input-jual" type="text"
                                                                    name="jual1" id="jual1" placeholder="Nominal"
                                                                    value="{{ old('jual1') }}" required>
                                                            </td>
                                                            <td><input class="form-control" type="text" name="laba1"
                                                                    id="laba1" placeholder="Nominal" readonly></td>
                                                            <td><input class="form-control text-center" type="number"
                                                                    name="stock1" id="stock1"
                                                                    value="{{ old('stock1') }}" required></td>
                                                            <td><input class="form-control text-center input-persen"
                                                                    type="text" name="persen1" id="persen1" readonly>
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td><input class="form-control" type="text"
                                                                    name="nama_barang2" id="nama_barang2"
                                                                    placeholder="Nama Item"
                                                                    value="{{ old('nama_barang2') }}" required></td>
                                                            <td><input class="form-control" type="text" name="hrg2"
                                                                    id="hrg2" placeholder="Nominal"
                                                                    value="{{ old('hrg2') }}" required></td>
                                                            <td><input class="form-control" type="text" name="jual2"
                                                                    id="jual2" placeholder="Nominal"
                                                                    value="{{ old('jual2') }}" required></td>
                                                            <td><input class="form-control" type="text" name="laba2"
                                                                    id="laba2" placeholder="Nominal" readonly></td>
                                                            <td><input class="form-control text-center" type="number"
                                                                    name="stock2" id="stock2"
                                                                    value="{{ old('stock2') }}" required></td>
                                                            <td><input class="form-control text-center" type="text"
                                                                    name="persen2" id="persen2" readonly></td>
                                                        </tr>
                                                        <tr>
                                                            <td><input class="form-control" type="text"
                                                                    name="nama_barang3" id="nama_barang3"
                                                                    placeholder="Nama Item"
                                                                    value="{{ old('nama_barang3') }}" required></td>
                                                            <td><input class="form-control" type="text" name="hrg3"
                                                                    id="hrg3" placeholder="Nominal"
                                                                    value="{{ old('hrg3') }}" required></td>
                                                            <td><input class="form-control" type="text" name="jual3"
                                                                    id="jual3" placeholder="Nominal"
                                                                    value="{{ old('jual3') }}" required></td>
                                                            <td><input class="form-control" type="text" name="laba3"
                                                                    id="laba3" placeholder="Nominal" readonly></td>
                                                            <td><input class="form-control text-center" type="number"
                                                                    name="stock3" id="stock3"
                                                                    value="{{ old('stock3') }}" required></td>
                                                            <td><input class="form-control text-center" type="text"
                                                                    name="persen3" id="persen3" readonly></td>
                                                        </tr>
                                                        <tr>
                                                            <td><input class="form-control" type="text"
                                                                    name="nama_barang4" id="nama_barang4"
                                                                    placeholder="Nama Item"
                                                                    value="{{ old('nama_barang4') }}" required></td>
                                                            <td><input class="form-control" type="text" name="hrg4"
                                                                    id="hrg4" placeholder="Nominal"
                                                                    value="{{ old('hrg4') }}" required></td>
                                                            <td><input class="form-control" type="text" name="jual4"
                                                                    id="jual4" placeholder="Nominal"
                                                                    value="{{ old('jual4') }}" required></td>
                                                            <td><input class="form-control" type="text" name="laba4"
                                                                    id="laba4" placeholder="Nominal" readonly></td>
                                                            <td><input class="form-control text-center" type="number"
                                                                    name="stock4" id="stock4"
                                                                    value="{{ old('stock4') }}" required></td>
                                                            <td><input class="form-control text-center" type="text"
                                                                    name="persen4" id="persen4" readonly></td>
                                                        </tr>
                                                        <tr>
                                                            <td><input class="form-control" type="text"
                                                                    name="nama_barang5" id="nama_barang5"
                                                                    placeholder="Nama Item"
                                                                    value="{{ old('nama_barang5') }}" required></td>
                                                            <td><input class="form-control" type="text" name="hrg5"
                                                                    id="hrg5" placeholder="Nominal"
                                                                    value="{{ old('hrg5') }}" required></td>
                                                            <td><input class="form-control" type="text" name="jual5"
                                                                    id="jual5" placeholder="Nominal"
                                                                    value="{{ old('jual5') }}" required></td>
                                                            <td><input class="form-control" type="text" name="laba5"
                                                                    id="laba5" placeholder="Nominal" readonly></td>
                                                            <td><input class="form-control text-center" type="number"
                                                                    name="stock5" id="stock5"
                                                                    value="{{ old('stock5') }}" required></td>
                                                            <td><input class="form-control text-center" type="text"
                                                                    name="persen5" id="persen5" readonly></td>
                                                        </tr>
                                                        <tr>
                                                            <td><input class="form-control" type="text"
                                                                    name="nama_barang6" id="nama_barang6"
                                                                    placeholder="Nama Item"
                                                                    value="{{ old('nama_barang6') }}" required></td>
                                                            <td><input class="form-control" type="text" name="hrg6"
                                                                    id="hrg6" placeholder="Nominal"
                                                                    value="{{ old('hrg6') }}" required></td>
                                                            <td><input class="form-control" type="text" name="jual6"
                                                                    id="jual6" placeholder="Nominal"
                                                                    value="{{ old('jual6') }}" required></td>
                                                            <td><input class="form-control" type="text" name="laba6"
                                                                    id="laba6" placeholder="Nominal" readonly></td>
                                                            <td><input class="form-control text-center" type="number"
                                                                    name="stock6" id="stock6"
                                                                    value="{{ old('stock6') }}" required></td>
                                                            <td><input class="form-control text-center" type="text"
                                                                    name="persen6" id="persen6" readonly></td>
                                                        </tr>
                                                        <tr>
                                                            <td><input class="form-control" type="text"
                                                                    name="nama_barang7" id="nama_barang7"
                                                                    placeholder="Nama Item"
                                                                    value="{{ old('nama_barang7') }}" required></td>
                                                            <td><input class="form-control" type="text" name="hrg7"
                                                                    id="hrg7" placeholder="Nominal"
                                                                    value="{{ old('hrg7') }}" required></td>
                                                            <td><input class="form-control" type="text" name="jual7"
                                                                    id="jual7" placeholder="Nominal"
                                                                    value="{{ old('jual7') }}" required></td>
                                                            <td><input class="form-control" type="text" name="laba7"
                                                                    id="laba7" placeholder="Nominal" readonly></td>
                                                            <td><input class="form-control text-center" type="number"
                                                                    name="stock7" id="stock7"
                                                                    value="{{ old('stock7') }}" required></td>
                                                            <td><input class="form-control text-center" type="text"
                                                                    name="persen7" id="persen7" readonly></td>
                                                        </tr>
                                                        <tr>
                                                            <td><input class="form-control" type="text"
                                                                    name="nama_barang8" id="nama_barang8"
                                                                    placeholder="Nama Item"
                                                                    value="{{ old('nama_barang8') }}" required></td>
                                                            <td><input class="form-control" type="text" name="hrg8"
                                                                    id="hrg8" placeholder="Nominal"
                                                                    value="{{ old('hrg8') }}" required></td>
                                                            <td><input class="form-control" type="text" name="jual8"
                                                                    id="jual8" placeholder="Nominal"
                                                                    value="{{ old('jual8') }}" required></td>
                                                            <td><input class="form-control" type="text" name="laba8"
                                                                    id="laba8" placeholder="Nominal" readonly></td>
                                                            <td><input class="form-control text-center" type="number"
                                                                    name="stock8" id="stock8"
                                                                    value="{{ old('stock8') }}" required></td>
                                                            <td><input class="form-control text-center" type="text"
                                                                    name="persen8" id="persen8" readonly></td>
                                                        </tr>
                                                        <tr>
                                                            <td><input class="form-control" type="text"
                                                                    name="nama_barang9" id="nama_barang9"
                                                                    placeholder="Nama Item"
                                                                    value="{{ old('nama_barang9') }}" required></td>
                                                            <td><input class="form-control" type="text" name="hrg9"
                                                                    id="hrg9" placeholder="Nominal"
                                                                    value="{{ old('hrg9') }}" required></td>
                                                            <td><input class="form-control" type="text" name="jual9"
                                                                    id="jual9" placeholder="Nominal"
                                                                    value="{{ old('jual9') }}" required></td>
                                                            <td><input class="form-control" type="text" name="laba9"
                                                                    id="laba9" placeholder="Nominal" readonly></td>
                                                            <td><input class="form-control text-center" type="number"
                                                                    name="stock9" id="stock9"
                                                                    value="{{ old('stock9') }}" required></td>
                                                            <td><input class="form-control text-center" type="text"
                                                                    name="persen9" id="persen9" readonly></td>
                                                        </tr>
                                                        <tr>
                                                            <td><input class="form-control" type="text"
                                                                    name="nama_barang10" id="nama_barang10"
                                                                    placeholder="Nama Item"
                                                                    value="{{ old('nama_barang10') }}" required></td>
                                                            <td><input class="form-control" type="text" name="hrg10"
                                                                    id="hrg10" placeholder="Nominal"
                                                                    value="{{ old('hrg10') }}" required></td>
                                                            <td><input class="form-control" type="text" name="jual10"
                                                                    id="jual10" placeholder="Nominal"
                                                                    value="{{ old('jual10') }}" required></td>
                                                            <td><input class="form-control" type="text" name="laba10"
                                                                    id="laba10" placeholder="Nominal" readonly></td>
                                                            <td><input class="form-control text-center" type="number"
                                                                    name="stock10" id="stock10"
                                                                    value="{{ old('stock10') }}" required></td>
                                                            <td><input class="form-control text-center" type="text"
                                                                    name="persen10" id="persen10" readonly></td>
                                                        </tr>

                                                    </tbody>
                                                </table>
                                                <table class="table table-bordered table-vcenter fs-5">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">Total Beli</th>
                                                            <th class="text-center">Total Jual</th>
                                                            <th class="text-center">Total Laba</th>
                                                            <th class="text-center" width="11%">Stok</th>
                                                            <th class="text-center" width="11%">%</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><input class="form-control" type="text" name="tbeli"
                                                                    id="tbeli" disabled="" value="Rp."></td>
                                                            <td><input class="form-control" type="text" name="tjual"
                                                                    id="tjual" disabled="" value="Rp."></td>
                                                            <td><input class="form-control" type="text" name="tlaba"
                                                                    id="tlaba" disabled="" value="Rp."></td>
                                                            <td><input class="form-control text-center" type="text"
                                                                    name="tstock" id="tstock" disabled=""></td>
                                                            <td><input class="form-control text-center" type="text"
                                                                    name="tpersen" id="tpersen" disabled=""></td>
                                                        </tr>
                                                    </tbody>
                                                </table>

                                                <table class="table table-bordered table-vcenter fs-5">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" colspan="2">Informasi Perdagangan
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">Barang Dagang</th>
                                                            <th class="text-center">Pendapatan Harian</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><input type="text" class="form-control"
                                                                    placeholder="Masukan Nominal" id="brdg"></td>
                                                            <td><input type="text" class="form-control"
                                                                    placeholder="Masukan Nominal" id="penhar"></td>
                                                        </tr>
                                                    </tbody>
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">Pokok Penjualan</th>
                                                            <th class="text-center">Laba Harian</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><input type="text" class="form-control"
                                                                    placeholder="Masukan Nominal" id="popen"></td>
                                                            <td><input type="text" class="form-control"
                                                                    placeholder="Masukan Nominal" id="lahar"></td>
                                                        </tr>
                                                    </tbody>
                                                </table>

                                                <table class="table table-bordered table-vcenter fs-5">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" colspan="2">Biaya Perdagangan</th>
                                                        </tr>
                                                    </thead>
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">Transportasi</th>
                                                            <th class="text-center">Bongkar Muat</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><input type="text" class="form-control"
                                                                    placeholder="Masukan Nominal" id="transport"></td>
                                                            <td><input type="text" class="form-control"
                                                                    placeholder="Masukan Nominal" id="bongkar"></td>
                                                        </tr>
                                                    </tbody>
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">Pegawai</th>
                                                            <th class="text-center">Gatel</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><input type="text" class="form-control"
                                                                    placeholder="Masukan Nominal" id="pegawai"></td>
                                                            <td><input type="text" class="form-control"
                                                                    placeholder="Masukan Nominal" id="gatel"></td>
                                                        </tr>
                                                    </tbody>
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">Retribusi</th>
                                                            <th class="text-center">Sewa Tempat</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><input type="text" class="form-control"
                                                                    placeholder="Masukan Nominal" id="retri"></td>
                                                            <td><input type="text" class="form-control"
                                                                    placeholder="Masukan Nominal" id="sewa"></td>
                                                        </tr>
                                                    </tbody>
                                                </table>

                                                <table class="table table-bordered table-vcenter fs-5">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" colspan="2">Analisa Usaha</th>
                                                        </tr>
                                                    </thead>
                                                    <thead>
                                                        <tr>
                                                            <th><input class="form-control" disabled=""
                                                                    value="Laba Bulanan"></th>
                                                            <td><input type="text" class="form-control" readonly=""
                                                                    value="Rp. " id="lbulan"></td>
                                                        </tr>
                                                        <tr>
                                                            <th><input class="form-control" disabled=""
                                                                    value="Biaya Perdagangan"></th>
                                                            <td><input type="text" class="form-control" readonly=""
                                                                    value="Rp. " id="bdagang"></td>
                                                        </tr>
                                                        <tr>
                                                            <th><input class="form-control" disabled=""
                                                                    value="Proyeksi Penambahan"></th>
                                                            <td><input type="text" class="form-control"
                                                                    placeholder="Masukan Nominal" id="penambahan"></td>
                                                        </tr>
                                                        <tr>
                                                            <th><input class="form-control fw-bold" disabled=""
                                                                    value="Hasil Bersih Usaha"></th>
                                                            <td><input type="text"
                                                                    class="form-control bg-primary fw-bold text-white"
                                                                    disabled="" value="Rp. " id="hasilbersih"></td>
                                                        </tr>
                                                    </thead>
                                                </table>

                                            </div>
                                            <div class="card-footer bg-transparent mt-auto">
                                                <div class="btn-list justify-content-end">
                                                    <button type="submit" class="btn btn-primary">
                                                        Simpan
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
    <script src="{{ asset('assets/js/myscript/perdagangan.js') }}"></script>
@endsection
