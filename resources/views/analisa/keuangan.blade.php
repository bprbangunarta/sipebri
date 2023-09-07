@extends('templates.app')
@section('title', 'Analisa Kemampuan Keuangan')

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">

                            <div class="container-xl">
                                <div class="row g-2 align-items-center">

                                    @include('templates.header-analisa', [$data])

                                    <div class="col-auto ms-auto d-print-none">
                                        <div class="btn-list">
                                            <a href="{{ route('lain.index', ['pengajuan' => $data->kd_pengajuan]) }}"
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
                                                        <th class="text-center" colspan="5">Informasi Keuangan</th>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-center">Jenis Usaha</th>
                                                        <th class="text-center">Pendapatan Usaha</th>
                                                    </tr>
                                                </thead>
                                                <thead>
                                                    <tr>
                                                        <th><input class="form-control" disabled=""
                                                                value="Usaha Perdagangan"></th>
                                                        <td><input type="text" class="form-control" readonly=""
                                                                value="{{ old('perdagangan') ?? ($kemampuan->perdagangan = 'Rp. ' . number_format($kemampuan->perdagangan, 0, ',', '.')) }}">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th><input class="form-control" disabled=""
                                                                value="Usaha Pertanian"></th>
                                                        <td><input type="text" class="form-control" readonly=""
                                                                value="{{ old('pertanian') ?? ($kemampuan->pertanian = 'Rp. ' . number_format($kemampuan->pertanian, 0, ',', '.')) }}">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th><input class="form-control" disabled="" value="Usaha Jasa">
                                                        </th>
                                                        <td><input type="text" class="form-control" readonly=""
                                                                value="{{ old('jasa') ?? ($kemampuan->jasa = 'Rp. ' . number_format($kemampuan->jasa, 0, ',', '.')) }}">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th><input class="form-control" disabled=""
                                                                value="Usaha Lainnya"></th>
                                                        <td><input type="text" class="form-control" readonly=""
                                                                value="{{ old('lainnya') ?? ($kemampuan->lainnya = 'Rp. ' . number_format($kemampuan->lainnya, 0, ',', '.')) }}">
                                                        </td>
                                                    </tr>
                                                </thead>
                                            </table>

                                            <table class="table table-bordered table-vcenter fs-5">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" colspan="2">Biaya Rumah Tangga</th>
                                                    </tr>
                                                </thead>
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">Pengeluaran Untuk</th>
                                                        <th class="text-center">Nominal Pengeluaran</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <th><input class="form-control" disabled=""
                                                                value="Biaya Konsumsi Pokok"></th>
                                                        <td><input type="text" class="form-control"
                                                                placeholder="Masukan Nominal"></td>
                                                    </tr>
                                                    <tr>
                                                        <th><input class="form-control" disabled="" value="Kesehatan">
                                                        </th>
                                                        <td><input type="text" class="form-control"
                                                                placeholder="Masukan Nominal"></td>
                                                    </tr>
                                                    <tr>
                                                        <th><input class="form-control" disabled="" value="Pendidikan">
                                                        </th>
                                                        <td><input type="text" class="form-control"
                                                                placeholder="Masukan Nominal"></td>
                                                    </tr>
                                                    <tr>
                                                        <th><input class="form-control" disabled="" value="Gatel"></th>
                                                        <td><input type="text" class="form-control"
                                                                placeholder="Masukan Nominal"></td>
                                                    </tr>
                                                    <tr>
                                                        <th><input class="form-control" disabled="" value="Jajan Anak">
                                                        </th>
                                                        <td><input type="text" class="form-control"
                                                                placeholder="Masukan Nominal"></td>
                                                    </tr>
                                                    <tr>
                                                        <th><input class="form-control" disabled=""
                                                                value="Sumbangan Sosial"></th>
                                                        <td><input type="text" class="form-control"
                                                                placeholder="Masukan Nominal"></td>
                                                    </tr>
                                                    <tr>
                                                        <th><input class="form-control" disabled="" value="Rokok">
                                                        </th>
                                                        <td><input type="text" class="form-control"
                                                                placeholder="Masukan Nominal"></td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <table class="table table-bordered table-vcenter fs-5">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" colspan="2">Kawajiban Lainnya</th>
                                                    </tr>
                                                </thead>
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">Kewajiban Untuk</th>
                                                        <th class="text-center">Nominal Kewajiban</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td><input type="text" class="form-control"
                                                                placeholder="Nama Kewajiban"></td>
                                                        <td><input type="text" class="form-control"
                                                                placeholder="Masukan Nominal"></td>
                                                    </tr>
                                                    <tr>
                                                        <td><input type="text" class="form-control"
                                                                placeholder="Nama Kewajiban"></td>
                                                        <td><input type="text" class="form-control"
                                                                placeholder="Masukan Nominal"></td>
                                                    </tr>
                                                    <tr>
                                                        <td><input type="text" class="form-control"
                                                                placeholder="Nama Kewajiban"></td>
                                                        <td><input type="text" class="form-control"
                                                                placeholder="Masukan Nominal"></td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <table class="table table-bordered table-vcenter fs-5">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" colspan="2">Analisa Keuangan</th>
                                                    </tr>
                                                </thead>
                                                <thead>
                                                    <tr>
                                                        <th><input class="form-control" disabled=""
                                                                value="Pendapatan Usaha"></th>
                                                        <td><input type="text" class="form-control" readonly=""
                                                                value="{{ $kemampuan->total = 'Rp. ' . number_format($kemampuan->total, 0, ',', '.') }}">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th><input class="form-control" disabled=""
                                                                value="Biaya Rumah Tangga"></th>
                                                        <td><input type="text" class="form-control" readonly=""
                                                                value="Rp. "></td>
                                                    </tr>
                                                    <tr>
                                                        <th><input class="form-control" disabled=""
                                                                value="Kewajiban Lainnya"></th>
                                                        <td><input type="text" class="form-control" readonly=""
                                                                value="Rp. "></td>
                                                    </tr>
                                                    <tr>
                                                        <th><input class="form-control fw-bold" disabled=""
                                                                value="Keuangan Perbulan"></th>
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
