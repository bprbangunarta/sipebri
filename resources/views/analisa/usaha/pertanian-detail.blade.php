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
                                                                    placeholder="Masukan Angka"
                                                                    value="{{ old('luas_sendiri') }}">
                                                            </td>
                                                            <td width="25%"><input class="form-control" type="text"
                                                                    name="luas_sewa" id="lsewa"
                                                                    placeholder="Masukan Angka"
                                                                    value="{{ old('luas_sewa') }}">
                                                            </td>
                                                            <td width="25%"><input class="form-control" type="text"
                                                                    name="luas_gadai" id="lgadai"
                                                                    placeholder="Masukan Angka"
                                                                    value="{{ old('luas_gadai') }}">
                                                            </td>
                                                            <td width="25%">
                                                                <select class="form-control" name="jenis_usaha"
                                                                    id="">
                                                                    <option value="" class="text-center">--Pilih--
                                                                    </option>
                                                                    <option value="pertanian"
                                                                        {{ old('jenis_usaha') == 'pertanian' ? 'selected' : '' }}>
                                                                        Pertanian</option>
                                                                    <option value="perkebunan"
                                                                        {{ old('jenis_usaha') == 'perkebunan' ? 'selected' : '' }}>
                                                                        Perkebunan</option>
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
                                                                    type="text" name="lokasi_usaha"
                                                                    value="{{ old('lokasi_usaha') }}"></td>
                                                            <td><input class="form-control text-center fw-bold"
                                                                    type="text" name="total_tanah" id="total_tanah"
                                                                    value="{{ old('lokasi_usaha') ?? 0 }}">
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
                                                            <th class="text-center" width="33%">Hasil Panen / Kw
                                                            </th>
                                                            <th class="text-center" width="33%">Harga Per Kwintan</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <input type="text" id="plafon"
                                                                    value="{{ $data->plafon }}" hidden>
                                                                <input type="text" id="jangka_waktu"
                                                                    value="{{ $data->jangka_waktu }}" hidden>
                                                                <select class="form-control" name="jenis_tanaman"
                                                                    id="">
                                                                    <option value="" class="text-center">--Pilih--
                                                                    </option>
                                                                    <option value="Padi Inpari"
                                                                        {{ old('jenis_tanaman') == 'Padi Ketan' ? 'selected' : '' }}>
                                                                        Padi Ketan</option>
                                                                    <option value="Padi Ketan"
                                                                        {{ old('jenis_tanaman') == 'Padi 42' ? 'selected' : '' }}>
                                                                        Padi 42</option>
                                                                    <option value="Padi Ketan"
                                                                        {{ old('jenis_tanaman') == 'Padi IR64' ? 'selected' : '' }}>
                                                                        Padi IR64</option>
                                                                    <option value="Padi Ketan"
                                                                        {{ old('jenis_tanaman') == 'Padi Muncul' ? 'selected' : '' }}>
                                                                        Padi Muncul</option>
                                                                    <option value="Padi Ketan"
                                                                        {{ old('jenis_tanaman') == 'Padi Pandan Wangi' ? 'selected' : '' }}>
                                                                        Padi Pandan Wangi</option>
                                                                    <option value="Padi Ketan"
                                                                        {{ old('jenis_tanaman') == 'Lainnya' ? 'selected' : '' }}>
                                                                        Lainnya</option>
                                                                </select>
                                                            </td>
                                                            <td><input class="form-control" type="text"
                                                                    name="hasil_panen" id="hpanen"
                                                                    placeholder="Masukan Angka"
                                                                    value="{{ old('hasil_panen') }}"></td>
                                                            <td><input class="form-control" type="text" name="harga"
                                                                    id="hrg" placeholder="Masukan Nominal"
                                                                    value="{{ old('harga') }}"></td>
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
                                                                    id="pengolahan"
                                                                    value="{{ old('pengolahan_tanah') }}">
                                                            </td>
                                                            <td><input type="text" class="form-control" name="bibit"
                                                                    placeholder="Masukan Nominal" id="bibit"
                                                                    value="{{ old('bibit') }}"></td>
                                                            <td><input type="text" class="form-control" name="pupuk"
                                                                    placeholder="Masukan Nominal" id="pupuk"
                                                                    value="{{ old('pupuk') }}"></td>
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
                                                                    placeholder="Masukan Nominal" name="pestisida"
                                                                    id="pestisida" value="{{ old('pestisida') }}"></td>
                                                            <td><input type="text" class="form-control"
                                                                    placeholder="Masukan Nominal" name="tenaga_kerja"
                                                                    id="tenaga_kerja" value="{{ old('tenaga_kerja') }}">
                                                            </td>
                                                            <td><input type="text" class="form-control"
                                                                    placeholder="Masukan Nominal" name="pengairan"
                                                                    id="pengairan" value="{{ old('pengairan') }}"></td>
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
                                                                    id="panen" value="{{ old('panen') }}"></td>
                                                            <td><input type="text" class="form-control"
                                                                    placeholder="Masukan Nominal" name="penggarap"
                                                                    id="penggarap" value="{{ old('penggarap') }}"></td>
                                                            <td><input type="text" class="form-control"
                                                                    placeholder="Masukan Nominal" name="pajak"
                                                                    id="pajak" value="{{ old('pajak') }}"></td>
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
                                                                    id="iuran_desa" value="{{ old('iuran_desa') }}"></td>
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
                                                            <th class="text-center">Jumlah Waktu Panen</th>
                                                            <th class="text-center">Biaya Amortisasi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <input type="text" class="form-control"
                                                                    placeholder="Masukan Jangka Waktu Panen"
                                                                    name="jangka_waktu_panen" id="jwp"
                                                                    value="{{ old('jangka_waktu_panen') ?? 6 }}">
                                                            </td>
                                                            </td>
                                                            <td><input type="text" class="form-control"
                                                                    placeholder="Masukan Nominal" name="amortisasi"
                                                                    id="amortisasi" value="{{ old('amortisasi') }}"
                                                                    readonly></td>
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
                                                                    id="luas_tanah"
                                                                    value="{{ old('tambah_luas_tanah') }}"></td>
                                                            <td><input type="text" class="form-control"
                                                                    placeholder="Masukan Nominal" name="pinjaman_bank"
                                                                    id="pinjaman_bank"
                                                                    value="{{ old('pinjaman_bank') }}"></td>
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
                                                                    value="Rp. {{ old('pendapatan') }}" name="pendapatan"
                                                                    id="pendapatan">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th><input class="form-control" disabled=""
                                                                    value="Pengeluaran Biaya Usaha"></th>
                                                            <td><input type="text" class="form-control" readonly=""
                                                                    value="Rp. {{ old('pengeluaran') }}"
                                                                    name="pengeluaran" id="pengeluaran">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th><input class="form-control" disabled=""
                                                                    value="Penambahan Hasil Usaha"></th>
                                                            <td><input type="text" class="form-control"
                                                                    value="Rp. {{ old('penambahan') }}" name="penambahan"
                                                                    id="penambahan"></td>
                                                        </tr>
                                                        <tr>
                                                            <th><input class="form-control" disabled=""
                                                                    value="Pinjaman Bank Lain"></th>
                                                            <td><input type="text" class="form-control" readonly=""
                                                                    value="Rp. {{ old('pinjaman') }}" name="pinjaman"
                                                                    id="pinjaman"></td>
                                                        </tr>
                                                        <tr>
                                                            <th><input class="form-control fw-bold" disabled=""
                                                                    value="Hasil Bersih Usaha"></th>
                                                            <td><input type="text"
                                                                    class="form-control bg-primary text-white"
                                                                    value="Rp. {{ old('laba_bersih') }}"
                                                                    name="laba_bersih" id="laba_bersih" readonly>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th><input class="form-control fw-bold" disabled=""
                                                                    value="Hasil Pendapatan Perbulan"></th>
                                                            <td><input type="text"
                                                                    class="form-control bg-primary text-white"
                                                                    value="Rp. {{ old('laba_perbulan') }}"
                                                                    name="laba_perbulan" id="lb_perbulan" readonly>
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
@endsection
