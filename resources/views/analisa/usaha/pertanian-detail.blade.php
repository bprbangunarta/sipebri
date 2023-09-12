@extends('templates.app')
@section('title', 'Analisa Usaha Pertanian')
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
                                            <a href="{{ route('pertanian.index', ['pengajuan' => $data->kd_pengajuan]) }}"
                                                class="btn btn-primary">
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
                                        <form
                                            action="{{ route('pertanian.update', ['pertanian' => $pertanian->kd_usaha, 'usaha' => $pertanian->kd_usaha]) }}"
                                            method="post">
                                            @csrf
                                            @method('put')
                                            <div class="card-body">

                                                <table class="table table-bordered table-vcenter fs-5">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" colspan="4">Informasi Usaha</th>
                                                        </tr>
                                                    </thead>
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" colspan="3">Luas Tanah (M2)</th>
                                                            <th class="text-center" colspan="1" rowspan="2">Sektor
                                                                Ekonomi
                                                            </th>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-center">Milik Sendiri</th>
                                                            <th class="text-center">Hasil Sewa</th>
                                                            <th class="text-center">Hasil Gadai</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td width="25%"><input class="form-control" type="text"
                                                                    name="luas_sendiri" id="lsendiri"
                                                                    placeholder="Masukan Angka">
                                                            </td>
                                                            <td width="25%"><input class="form-control" type="text"
                                                                    name="luas_sewa" id="lsewa"
                                                                    placeholder="Masukan Angka">
                                                            </td>
                                                            <td width="25%"><input class="form-control" type="text"
                                                                    name="luas_gadai" id="lgadai"
                                                                    placeholder="Masukan Angka">
                                                            </td>
                                                            <td width="25%">
                                                                <select class="form-control" name="jenis_usaha"
                                                                    id="">
                                                                    <option value="" class="text-center">--Pilih--
                                                                    </option>
                                                                    <option value="pertanian">Pertanian</option>
                                                                    <option value="perkebunan">Perkebunan</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" colspan="3">Lokasi Pertanian
                                                            </th>
                                                            <th class="text-center" colspan="1">Total Luas Tanah (M2)
                                                            </th>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3"><input class="form-control text-center"
                                                                    type="text" name="lokasi_usaha"></td>
                                                            <td><input class="form-control text-center fw-bold"
                                                                    type="text" name="total_tanah" id="total_tanah"
                                                                    value="0">
                                                            </td>
                                                        </tr>
                                                    </thead>
                                                </table>

                                                <table class="table table-bordered table-vcenter fs-5">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" colspan="3">Jumlah Hasil Panen</th>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-center" width="34%">Jenis Tanaman</th>
                                                            <th class="text-center" width="33%">Hasil Panen</th>
                                                            <th class="text-center" width="33%">Harga Per Kwintan</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <select class="form-control" name="jenis_tanaman"
                                                                    id="">
                                                                    <option value="" class="text-center">--Pilih--
                                                                    </option>
                                                                    <option value="Padi Inpari">Padi Inpari</option>
                                                                    <option value="Padi Ketan">Padi Ketan</option>
                                                                </select>
                                                            </td>
                                                            <td><input class="form-control" type="text"
                                                                    name="hasil_panen" id="hpanen"
                                                                    placeholder="Masukan Angka"></td>
                                                            <td><input class="form-control" type="text" name="harga"
                                                                    id="hrg" placeholder="Masukan Nominal"></td>
                                                        </tr>

                                                    </tbody>
                                                </table>

                                                <table class="table table-bordered table-vcenter fs-5">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" colspan="3">Biaya Pertanian</th>
                                                        </tr>
                                                    </thead>
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">Pengolahan Tanah</th>
                                                            <th class="text-center">Biaya Bibit</th>
                                                            <th class="text-center">Biaya Pupuk</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><input type="text" class="form-control"
                                                                    placeholder="Masukan Nominal" name="pengolahan_tanah"
                                                                    id="pengolahan">
                                                            </td>
                                                            <td><input type="text" class="form-control" name="bibit"
                                                                    placeholder="Masukan Nominal" id="bibit"></td>
                                                            <td><input type="text" class="form-control" name="pupuk"
                                                                    placeholder="Masukan Nominal" id="pupuk"></td>
                                                        </tr>
                                                    </tbody>
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">Biaya Pestisida</th>
                                                            <th class="text-center">Tenaga Kerja</th>
                                                            <th class="text-center">Biaya Pengairan</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><input type="text" class="form-control"
                                                                    name="pestisida" placeholder="Masukan Nominal"
                                                                    name="pestisida" id="pestisida"></td>
                                                            <td><input type="text" class="form-control"
                                                                    placeholder="Masukan Nominal" name="tenaga_kerja"
                                                                    id="tenaga_kerja"></td>
                                                            <td><input type="text" class="form-control"
                                                                    placeholder="Masukan Nominal" name="pengairan"
                                                                    id="pengairan"></td>
                                                        </tr>
                                                    </tbody>
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">Biaya Panen</th>
                                                            <th class="text-center">Biaya Penggarap</th>
                                                            <th class="text-center">Biaya Pajak</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><input type="text" class="form-control"
                                                                    placeholder="Masukan Nominal" name="panen"
                                                                    id="panen"></td>
                                                            <td><input type="text" class="form-control"
                                                                    placeholder="Masukan Nominal" name="penggarap"
                                                                    id="penggarap"></td>
                                                            <td><input type="text" class="form-control"
                                                                    placeholder="Masukan Nominal" name="pajak"
                                                                    id="pajak"></td>
                                                        </tr>
                                                    </tbody>
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">Iuaran Desa</th>
                                                            <th class="text-center"></th>
                                                            <th class="text-center"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><input type="text" class="form-control"
                                                                    placeholder="Masukan Nominal" name="iuran_desa"
                                                                    id="iuran_desa"></td>
                                                            <td></td>
                                                            <td></td>
                                                        </tr>

                                                    </tbody>
                                                </table>

                                                <table class="table table-bordered table-vcenter fs-5">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" colspan="2">Informasi Tambahan</th>
                                                        </tr>
                                                    </thead>
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">Jumlah Musim</th>
                                                            <th class="text-center">Biaya Amortisasi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <select class="form-control" name="jumlah_musim"
                                                                    id="jml_musim">
                                                                    <option value="" class="text-center">--Pilih
                                                                        Jumlah
                                                                        Musim--</option>
                                                                    <option value="6">2 Musim</option>
                                                                    <option value="3">3 Musim</option>
                                                                </select>
                                                            </td>
                                                            <td><input type="text" class="form-control"
                                                                    placeholder="Masukan Nominal" name="amortisasi"
                                                                    id="amortisasi"></td>
                                                        </tr>
                                                    </tbody>
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">Penambahan Usaha (Luas Tanah M2)</th>
                                                            <th class="text-center">Pinjaman Bank Lain</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><input type="text" class="form-control"
                                                                    placeholder="Masukan Angka" name="tambah_luas_tanah"
                                                                    id="luas_tanah"></td>
                                                            <td><input type="text" class="form-control"
                                                                    placeholder="Masukan Nominal" name="pinjaman_bank"
                                                                    id="pinjaman_bank"></td>
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
                                                                    value="Pendapatan Hasil Panen"></th>
                                                            <td><input type="text" class="form-control" readonly=""
                                                                    value="Rp. " name="pendapatan" id="pendapatan">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th><input class="form-control" disabled=""
                                                                    value="Pengeluaran Biaya Usaha"></th>
                                                            <td><input type="text" class="form-control" readonly=""
                                                                    value="Rp. " name="pengeluaran" id="pengeluaran">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th><input class="form-control" disabled=""
                                                                    value="Penambahan Hasil Usaha"></th>
                                                            <td><input type="text" class="form-control" value="Rp. "
                                                                    name="penambahan" id="penambahan"></td>
                                                        </tr>
                                                        <tr>
                                                            <th><input class="form-control" disabled=""
                                                                    value="Pinjaman Bank Lain"></th>
                                                            <td><input type="text" class="form-control" readonly=""
                                                                    value="Rp. " name="pinjaman" id="pinjaman"></td>
                                                        </tr>
                                                        <tr>
                                                            <th><input class="form-control fw-bold" disabled=""
                                                                    value="Hasil Bersih Usaha"></th>
                                                            <td><input type="text"
                                                                    class="form-control bg-primary fw-bold text-white"
                                                                    value="Rp. " name="laba_bersih" id="laba_bersih">
                                                            </td>
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
    <script src="{{ asset('assets/js/myscript/pertanian.js') }}"></script>
@endsection
