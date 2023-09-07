@extends('templates.app')
@section('title', 'Analisa Usaha Pertanian')

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
                                                        <th class="text-center" colspan="1" rowspan="2">Sektor Ekonomi
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
                                                                name="" id="" placeholder="Masukan Angka">
                                                        </td>
                                                        <td width="25%"><input class="form-control" type="text"
                                                                name="" id="" placeholder="Masukan Angka">
                                                        </td>
                                                        <td width="25%"><input class="form-control" type="text"
                                                                name="" id="" placeholder="Masukan Angka">
                                                        </td>
                                                        <td width="25%">
                                                            <select class="form-control" name="" id="">
                                                                <option value="" class="text-center">--Pilih--
                                                                </option>
                                                                <option value="">Pertanian</option>
                                                                <option value="">Perkebunan</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" colspan="3">Total Luas Tanah (M2)</th>
                                                        <td><input class="form-control text-center fw-bold" type="text"
                                                                name="" id="" value="0M2" disabled="">
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
                                                            <select class="form-control" name="" id="">
                                                                <option value="" class="text-center">--Pilih--
                                                                </option>
                                                            </select>
                                                        </td>
                                                        <td><input class="form-control" type="text" name=""
                                                                id="" placeholder="Masukan Angka"></td>
                                                        <td><input class="form-control" type="text" name=""
                                                                id="" placeholder="Masukan Nominal"></td>
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
                                                                placeholder="Masukan Nominal"></td>
                                                        <td><input type="text" class="form-control"
                                                                placeholder="Masukan Nominal"></td>
                                                        <td><input type="text" class="form-control"
                                                                placeholder="Masukan Nominal"></td>
                                                    </tr>
                                                </tbody>
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">Biaya Pestisida</th>
                                                        <th class="text-center">Biaya Pupuk</th>
                                                        <th class="text-center">Biaya Pestisida</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td><input type="text" class="form-control"
                                                                placeholder="Masukan Nominal"></td>
                                                        <td><input type="text" class="form-control"
                                                                placeholder="Masukan Nominal"></td>
                                                        <td><input type="text" class="form-control"
                                                                placeholder="Masukan Nominal"></td>
                                                    </tr>
                                                </tbody>
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">Tenaga Kerja</th>
                                                        <th class="text-center">Biaya Pengairan</th>
                                                        <th class="text-center">Biaya Panen</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td><input type="text" class="form-control"
                                                                placeholder="Masukan Nominal"></td>
                                                        <td><input type="text" class="form-control"
                                                                placeholder="Masukan Nominal"></td>
                                                        <td><input type="text" class="form-control"
                                                                placeholder="Masukan Nominal"></td>
                                                    </tr>
                                                </tbody>
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">Biaya Penggarap</th>
                                                        <th class="text-center">Biaya Pajak</th>
                                                        <th class="text-center">Iuaran Desa</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td><input type="text" class="form-control"
                                                                placeholder="Masukan Nominal"></td>
                                                        <td><input type="text" class="form-control"
                                                                placeholder="Masukan Nominal"></td>
                                                        <td><input type="text" class="form-control"
                                                                placeholder="Masukan Nominal"></td>
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
                                                            <select class="form-control" name="" id="">
                                                                <option value="" class="text-center">--Pilih Jumlah
                                                                    Musim--</option>
                                                            </select>
                                                        </td>
                                                        <td><input type="text" class="form-control"
                                                                placeholder="Masukan Nominal"></td>
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
                                                                placeholder="Masukan Angka"></td>
                                                        <td><input type="text" class="form-control"
                                                                placeholder="Masukan Nominal"></td>
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
                                                                value="Rp. "></td>
                                                    </tr>
                                                    <tr>
                                                        <th><input class="form-control" disabled=""
                                                                value="Pengeluaran Biaya Usaha"></th>
                                                        <td><input type="text" class="form-control" readonly=""
                                                                value="Rp. "></td>
                                                    </tr>
                                                    <tr>
                                                        <th><input class="form-control" disabled=""
                                                                value="Penambahan Hasil Usaha"></th>
                                                        <td><input type="text" class="form-control" readonly=""
                                                                value="Rp. "></td>
                                                    </tr>
                                                    <tr>
                                                        <th><input class="form-control" disabled=""
                                                                value="Pinjaman Bank Lain"></th>
                                                        <td><input type="text" class="form-control" readonly=""
                                                                value="Rp. "></td>
                                                    </tr>
                                                    <tr>
                                                        <th><input class="form-control fw-bold" disabled=""
                                                                value="Hasil Bersih Usaha"></th>
                                                        <td><input type="text"
                                                                class="form-control bg-primary fw-bold text-white"
                                                                disabled="" value="Rp. "></td>
                                                    </tr>
                                                </thead>
                                            </table>

                                        </div>
                                        <div class="card-footer bg-transparent mt-auto">
                                            <div class="btn-list justify-content-end">
                                                <a href="#" class="btn btn-primary">
                                                    Simpan
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
