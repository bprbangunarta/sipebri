@extends('theme.app')
@section('title', 'Fasilitas Kredit')

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
                            <h3 class="box-title">FASILITAS KREDIT</h3>

                            <div class="box-tools">
                                <form action="#" method="GET">
                                    <div class="input-group input-group-sm hidden-xs" style="width: 170px;">
                                        <input type="text" class="form-control pull-right" name="name" id="name" value="" placeholder="Search">

                                        <div class="input-group-btn">
                                            <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
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
                                        <th class="text-center" width="7%">STATUS</th>
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
                                        <td class="text-center">Disetujui</td>
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
