@extends('theme.app')
@section('title', 'Cetak Berkas Pengajuan')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-print"></i>
                            <h3 class="box-title">CETAK BERKAS PENGAJUAN</h3>
                        </div>

                        <div class="box-body">
                            <div class="col-md-12">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr class="bg-blue">
                                            <th class="text-center">NAMA BERKAS</th>
                                            <th class="text-center">AKSI</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-uppercase" style="vertical-align: middle;">
                                                Formulir Pengajuan Kredit
                                            </td>
                                            <td class="text-center" width="10%">
                                                <a href="#" target="_blank">
                                                    <span class="btn bg-blue" style="width: 120px;hight:100%;">
                                                        Cetak Berkas
                                                    </span>
                                                </a>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="text-uppercase" style="vertical-align: middle;">
                                                Surat Persetujuan Pendamping
                                            </td>
                                            <td class="text-center" width="10%">
                                                <a href="{{ route('cetak.pendamping', ['cetak' => $data->kd_pengajuan]) }}"
                                                    target="_blank">
                                                    <span class="btn bg-blue" style="width: 120px;hight:100%;">
                                                        Cetak Berkas
                                                    </span>
                                                </a>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="text-uppercase" style="vertical-align: middle;">
                                                Surat pernyataan pengecekan NIK
                                            </td>
                                            <td class="text-center" width="10%">
                                                <a href="{{ route('cetak.nik', ['cetak' => $data->kd_pengajuan]) }}"
                                                    target="_blank">
                                                    <span class="btn bg-blue" style="width: 120px;hight:100%;">
                                                        Cetak Berkas
                                                    </span>
                                                </a>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="text-uppercase" style="vertical-align: middle;">
                                                Surat pernyataan pengecekan IDEB
                                            </td>
                                            <td class="text-center" width="10%">
                                                <a href="{{ route('data.slik', ['cetak' => $data->kd_pengajuan]) }}"
                                                    target="_blank">
                                                    <span class="btn bg-blue" style="width: 120px;hight:100%;">
                                                        Cetak Berkas
                                                    </span>
                                                </a>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="text-uppercase" style="vertical-align: middle;">
                                                BA Pemerikasaan Sertifikat Tanah
                                            </td>
                                            <td class="text-center" width="10%">
                                                <a href="{{ route('cetak.tanah', ['cetak' => $data->kd_pengajuan]) }}"
                                                    target="_blank">
                                                    <span class="btn bg-blue" style="width: 120px;hight:100%;">
                                                        Cetak Berkas
                                                    </span>
                                                </a>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="text-uppercase" style="vertical-align: middle;">
                                                BA Pemerikasaan Agunan Kendaraan Motor
                                            </td>
                                            <td class="text-center" width="10%">
                                                <a href="{{ route('cetak.motor', ['cetak' => $data->kd_pengajuan]) }}"
                                                    target="_blank">
                                                    <span class="btn bg-blue" style="width: 120px;hight:100%;">
                                                        Cetak Berkas
                                                    </span>
                                                </a>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="text-uppercase" style="vertical-align: middle;">
                                                BA Pemerikasaan Agunan Kendaraan Mobil
                                            </td>
                                            <td class="text-center" width="10%">
                                                <a href="{{ route('cetak.mobil', ['cetak' => $data->kd_pengajuan]) }}"
                                                    target="_blank">
                                                    <span class="btn bg-blue" style="width: 120px;hight:100%;">
                                                        Cetak Berkas
                                                    </span>
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
