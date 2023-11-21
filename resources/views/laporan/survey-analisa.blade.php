@extends('theme.app')
@section('title', 'Laporan Survey dan Analisa')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>LAPORAN

                <a href="#" class="btn btn-sm btn-success pull-right">
                    <i class="fa fa-download"></i>&nbsp; Export Data
                </a>
            </h1>
        </section>

        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border" style="border-bottom: 1px solid #3C8DBC;">
                            <h3 class="box-title">SURVEY DAN ANALISA</h3>

                            <div class="box-tools">
                                <form action="#" method="GET">
                                    <div class="input-group input-group-sm hidden-xs pull-right" style="width: 335px;">
                                        <input type="date" class="form-control pull-left" style="width: 150px;" name="tgl1" id="tgl1" value="">

                                        <input type="date" class="form-control pull-right" style="width: 150px;" name="tgl2" id="tgl2" value="">

                                        <div class="input-group-btn">
                                            <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i></button>
                                        </div>
                                    </div>
                                </form>

                            </div>
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered text-uppercase" style="font-size: 12px;">
                                <thead>
                                    <tr class="bg-blue">
                                        <th class="text-center" width="3%">#</th>
                                        <th class="text-center" width="7%">TANGGAL</th>
                                        <th class="text-center" width="7%">KODE</th>
                                        <th class="text-center">NAMA LENGKAP</th>
                                        <th class="text-center" width="45%">ALAMAT</th>
                                        <th class="text-center" width="7%">PLAFON</th>
                                        <th class="text-center" width="10%">STATUS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="text-uppercase">
                                        <td class="text-center">1</td>
                                        <td class="text-center">17/11/2023</td>
                                        <td class="text-center">00339950</td>
                                        <td>Zulfadli Rizal Zulfadli Rizal</td>
                                        <td>KAMPUNG SUKAGALIH RT/RW 030/008 SUKAMULYA PAGADEN SUBANG PROVINSI JAWA BARAT</td>
                                        <td class="text-right">10.000.000.000</td>
                                        <td class="text-center">Penjadwalan</td>
                                    </tr>
                                    <tr class="text-uppercase">
                                        <td class="text-center">1</td>
                                        <td class="text-center">17/11/2023</td>
                                        <td class="text-center">00339950</td>
                                        <td>Zulfadli Rizal Zulfadli Rizal</td>
                                        <td>KAMPUNG SUKAGALIH RT/RW 030/008 SUKAMULYA PAGADEN SUBANG PROVINSI JAWA BARAT</td>
                                        <td class="text-right">10.000.000.000</td>
                                        <td class="text-center">Proses Survey</td>
                                    </tr>
                                    <tr class="text-uppercase">
                                        <td class="text-center">1</td>
                                        <td class="text-center">17/11/2023</td>
                                        <td class="text-center">00339950</td>
                                        <td>Zulfadli Rizal Zulfadli Rizal</td>
                                        <td>KAMPUNG SUKAGALIH RT/RW 030/008 SUKAMULYA PAGADEN SUBANG PROVINSI JAWA BARAT</td>
                                        <td class="text-right">10.000.000.000</td>
                                        <td class="text-center">Proses Analisa</td>
                                    </tr>
                                    <tr class="text-uppercase">
                                        <td class="text-center">1</td>
                                        <td class="text-center">17/11/2023</td>
                                        <td class="text-center">00339950</td>
                                        <td>Zulfadli Rizal Zulfadli Rizal</td>
                                        <td>KAMPUNG SUKAGALIH RT/RW 030/008 SUKAMULYA PAGADEN SUBANG PROVINSI JAWA BARAT</td>
                                        <td class="text-right">10.000.000.000</td>
                                        <td class="text-center">Naik Kasi</td>
                                    </tr>
                                    <tr class="text-uppercase">
                                        <td class="text-center">1</td>
                                        <td class="text-center">17/11/2023</td>
                                        <td class="text-center">00339950</td>
                                        <td>Zulfadli Rizal Zulfadli Rizal</td>
                                        <td>KAMPUNG SUKAGALIH RT/RW 030/008 SUKAMULYA PAGADEN SUBANG PROVINSI JAWA BARAT</td>
                                        <td class="text-right">10.000.000.000</td>
                                        <td class="text-center">Naik Komite I</td>
                                    </tr>
                                    <tr class="text-uppercase">
                                        <td class="text-center">1</td>
                                        <td class="text-center">17/11/2023</td>
                                        <td class="text-center">00339950</td>
                                        <td>Zulfadli Rizal Zulfadli Rizal</td>
                                        <td>KAMPUNG SUKAGALIH RT/RW 030/008 SUKAMULYA PAGADEN SUBANG PROVINSI JAWA BARAT</td>
                                        <td class="text-right">10.000.000.000</td>
                                        <td class="text-center">Naik Komite II</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>
@endsection
