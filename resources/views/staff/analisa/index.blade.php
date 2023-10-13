@extends('theme.app')
@section('title', 'Permohonan Analisa')

@section('content')
<div class="content-wrapper">

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <i class="fa fa-file-text-o"></i>
                        <h3 class="box-title">Permohonan Analisa</h3>
                    </div>
                    <div class="box-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="bg-blue">
                                    <th class="text-center" style="width: 10px">#</th>
                                    <th class="text-center" style="width: 150px">PENGAJUAN</th>
                                    <th class="text-center" style="width: 200px">NASABAH</th>
                                    <th class="text-center">ALAMAT</th>
                                    <th class="text-center" style="width: 100px">WILAYAH</th>
                                    <th class="text-center">STATUS</th>
                                    <th class="text-center" style="width: 90px">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center" style="vertical-align: middle;">1</td>
                                    <td style="vertical-align: middle;">
                                        <b>KODE: </b>0823737830 <br>
                                        <b>TANGGAL</b> : 2023-08-25
                                    </td>
                                    <td style="text-transform: uppercase;vertical-align: middle;">
                                        ZULFADLI RIZAL <br>
                                        <b>Kaetegori:</b> RELOAN
                                        
                                    </td>
                                    <td style="text-transform: uppercase;">
                                        KAMPUNG SUKAGALIH RT/RW 030/008 SUKAMULYA PAGADEN SUBANG <br>
                                        <b>Desa: </b>SUKAMULYA | <b>Kecamatan: </b>PAGADEN
                                    </td>
                                    <td class="text-center" style="vertical-align: middle;">
                                        KANTOR KAS JALANCAGAK
                                    </td>
                                    <td class="text-center" style="vertical-align: middle;">
                                        <span class="label label-warning">PROSES ANALISA</span>
                                    </td>
                                    <td class="text-center" style="vertical-align: middle;">
                                        <a href="/theme/analisa/usaha/perdagangan" class="btn-circle btn-sm btn-warning" title="Input Analisa"><i class="fa fa-file-text-o"></i></a>
                                        <a href="#" class="btn-circle btn-sm btn-primary"><i class="fa fa-print"></i></a>
                                    </td>
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