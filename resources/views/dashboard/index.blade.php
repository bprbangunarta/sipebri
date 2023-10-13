@extends('theme.app')
@section('title', 'Dashboard')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Dashboard
            <small>Monitoring</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-laptop"></i> Dashboard</a></li>
            <li class="active">Index</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box bg-green">
                    <span class="info-box-icon"><i class="fa fa-thumbs-o-up"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Baki Debit</span>
                        <span class="info-box-number">174.835.045.319</span>

                        <div class="progress">
                            <div class="progress-bar" style="width: 100%"></div>
                        </div>
                        <span class="progress-description">
                            15 Agustus 2023
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box bg-aqua">
                    <span class="info-box-icon"><i class="fa fa-bookmark-o"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Tgk Pokok</span>
                        <span class="info-box-number">18.978.321.997</span>

                        <div class="progress">
                            <div class="progress-bar" style="width: 100%"></div>
                        </div>
                        <span class="progress-description">
                            15 Agustus 2023
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box bg-yellow">
                    <span class="info-box-icon"><i class="fa fa-calendar"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Tgk Denda</span>
                        <span class="info-box-number">28.403.286.391</span>

                        <div class="progress">
                            <div class="progress-bar" style="width: 100%"></div>
                        </div>
                        <span class="progress-description">
                            15 Agustus 2023
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box bg-red">
                    <span class="info-box-icon"><i class="fa fa-warning"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Tgk Bunga</span>
                        <span class="info-box-number">13.841.090.899</span>

                        <div class="progress">
                            <div class="progress-bar" style="width: 100%"></div>
                        </div>
                        <span class="progress-description">
                            15 Agustus 2023
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="box box-danger">
                    <div class="box-header with-border">
                        <i class="fa fa-calendar"></i>
                        <h3 class="box-title">Janji Bayar</h3>
                        <span class="pull-right-container">
                            <small class="label pull-right bg-red">2</small>
                        </span>
                    </div>
                    <div class="box-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="bg-red">
                                    <th class="text-center" style="width: 10px">#</th>
                                    <th class="text-center">Nama Debitur</th>
                                    <th class="text-center" style="width: 40px">Petugas</th>
                                    <th class="text-center" style="width: 130px">Tunggakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="bg-danger">
                                    <td class="text-center">1</td>
                                    <td>Zulfadli Rizal</td>
                                    <td class="text-center">ZFR</td>
                                    <td class="text-right">Rp. 18.189.987</td>
                                </tr>
                                <tr class="bg-danger">
                                    <td class="text-center">2</td>
                                    <td>Zulfadli Rizal</td>
                                    <td class="text-center">ZFR</td>
                                    <td class="text-right">Rp. 18.189.987</td>
                                </tr>
                                <tr class="bg-red">
                                    <td class="text-center" colspan="3"><b>Total</b></td>
                                    <td class="text-right"><b>Rp. 85.168.648</b></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <i class="fa fa-user"></i>
                        <h3 class="box-title">Debitur NPL</h3>
                        <span class="pull-right-container">
                            <small class="label pull-right bg-yellow">58</small>
                        </span>
                    </div>
                    <div class="box-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="bg-yellow">
                                    <th class="text-center" style="width: 10px">#</th>
                                    <th class="text-center">Nama Debitur</th>
                                    <th class="text-center">Tunggakan</th>
                                    <th class="text-center" style="width: 40px">Petugas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="bg-warning">
                                    <td class="text-center">1</td>
                                    <td>Zulfadli Rizal</td>
                                    <td>Rp. 18.189.987</td>
                                    <td class="text-center">ZFR</td>
                                </tr>
                                <tr class="bg-warning">
                                    <td class="text-center">2</td>
                                    <td>Zulfadli Rizal</td>
                                    <td>Rp. 18.189.987</td>
                                    <td class="text-center">ZFR</td>
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