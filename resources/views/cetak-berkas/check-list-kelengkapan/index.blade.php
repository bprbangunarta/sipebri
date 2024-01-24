@extends('theme.app')
@section('title', 'Data Check List Kendaraan')

@section('content')
    <div class="content-wrapper">

        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-edit"></i>
                            <h3 class="box-title">DATA CHECK LIST KELENGKAPAN</h3>
                        </div>

                        <div class="box-body" style="overflow: auto;white-space: nowrap;width: 100%;">
                            <div class="box-body" data-select2-id="13" style="margin-left: 1%; margin-right: 1%;">
                                <div class="row">
                                    <table class="table table-bordered text-uppercase" style="">
                                        <thead>
                                            <tr class="bg-blue">
                                                <th class="text-center" width="5%">NO</th>
                                                <th class="text-center" width="90">NAMA LAPORAN PERPRODUK</th>
                                                <th class="text-center" width="5%">AKSI</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="text-uppercase">
                                                <td class="text-center">1.</td>
                                                <td>&nbsp;LAPORAN KREDIT IBADAH HAJI (KIH)</td>
                                                <td class="text-center">
                                                    <a href="{{ route('report.kih') }}" target="__blank"
                                                        class="btn-circle btn-sm bg-blue" title="Cetak">
                                                        <i class="fa fa-print"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr class="text-uppercase">
                                                <td class="text-center">2.</td>
                                                <td>&nbsp;LAPORAN KREDIT KENDARAAN OPERASIONAL (KKO)</td>
                                                <td class="text-center">
                                                    <a href="{{ route('report.kko') }}" target="__blank"
                                                        class="btn-circle btn-sm bg-blue" title="Cetak">
                                                        <i class="fa fa-print"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr class="text-uppercase">
                                                <td class="text-center">3.</td>
                                                <td>&nbsp;LAPORAN KREDIT PEGAWAI SWASTA NON MOU (KPJ)</td>
                                                <td class="text-center">
                                                    <a href="{{ route('report.kpj') }}" target="__blank"
                                                        class="btn-circle btn-sm bg-blue" title="Cetak">
                                                        <i class="fa fa-print"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr class="text-uppercase">
                                                <td class="text-center">4.</td>
                                                <td>&nbsp;LAPORAN KREDIT PEGAWAI NEGERI (KPN)</td>
                                                <td class="text-center">
                                                    <a href="{{ route('report.kpn') }}" target="__blank"
                                                        class="btn-circle btn-sm bg-blue" title="Cetak">
                                                        <i class="fa fa-print"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr class="text-uppercase">
                                                <td class="text-center">5.</td>
                                                <td>&nbsp;LAPORAN KREDIT PEGAWAI SWASTA (KPS)</td>
                                                <td class="text-center">
                                                    <a href="{{ route('report.kps') }}" class="btn-circle btn-sm bg-blue"
                                                        title="Cetak" target="__blank">
                                                        <i class="fa fa-print"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr class="text-uppercase">
                                                <td class="text-center">6.</td>
                                                <td>&nbsp;LAPORAN KREDIT RESEPSI (KRS) BPKB</td>
                                                <td class="text-center">
                                                    <a href="{{ route('report.krs_bpkb') }}"
                                                        class="btn-circle btn-sm bg-blue" title="Cetak" target="__blank">
                                                        <i class="fa fa-print"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr class="text-uppercase">
                                                <td class="text-center">7.</td>
                                                <td>&nbsp;LAPORAN KREDIT RESEPSI (KRS) BPKB SHM</td>
                                                <td class="text-center">
                                                    <a href="{{ route('report.krs_bpkb_shm') }}"
                                                        class="btn-circle btn-sm bg-blue" title="Cetak" target="__blank">
                                                        <i class="fa fa-print"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr class="text-uppercase">
                                                <td class="text-center">8.</td>
                                                <td>&nbsp;LAPORAN KREDIT RESEPSI (KRS) SHM</td>
                                                <td class="text-center">
                                                    <a href="{{ route('report.krs_shm') }}"
                                                        class="btn-circle btn-sm bg-blue" title="Cetak" target="__blank">
                                                        <i class="fa fa-print"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr class="text-uppercase">
                                                <td class="text-center">9.</td>
                                                <td>&nbsp;LAPORAN KREDIT UMUM (KRU) BPKB</td>
                                                <td class="text-center">
                                                    <a href="{{ route('report.kru_bpkb') }}"
                                                        class="btn-circle btn-sm bg-blue" title="Cetak" target="__blank">
                                                        <i class="fa fa-print"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr class="text-uppercase">
                                                <td class="text-center">10.</td>
                                                <td>&nbsp;LAPORAN KREDIT UMUM (KRU) BPKB SHM</td>
                                                <td class="text-center">
                                                    <a href="{{ route('report.kru_bpkb_shm') }}"
                                                        class="btn-circle btn-sm bg-blue" title="Cetak" target="__blank">
                                                        <i class="fa fa-print"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr class="text-uppercase">
                                                <td class="text-center">11.</td>
                                                <td>&nbsp;LAPORAN KREDIT UMUM (KRU) SHM</td>
                                                <td class="text-center">
                                                    <a href="{{ route('report.kru_shm') }}"
                                                        class="btn-circle btn-sm bg-blue" title="Cetak"
                                                        target="__blank">
                                                        <i class="fa fa-print"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr class="text-uppercase">
                                                <td class="text-center">12.</td>
                                                <td>&nbsp;LAPORAN KREDIT TANPA AGUNAN (KTA)</td>
                                                <td class="text-center">
                                                    <a href="{{ route('report.kta') }}" class="btn-circle btn-sm bg-blue"
                                                        title="Cetak" target="__blank">
                                                        <i class="fa fa-print"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr class="text-uppercase">
                                                <td class="text-center">13.</td>
                                                <td>&nbsp;LAPORAN KREDIT PEGAWAI (KUP)</td>
                                                <td class="text-center">
                                                    <a href="{{ route('report.kup') }}" class="btn-circle btn-sm bg-blue"
                                                        title="Cetak" target="__blank">
                                                        <i class="fa fa-print"></i>
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
            </div>
        </section>
    </div>

@endsection
