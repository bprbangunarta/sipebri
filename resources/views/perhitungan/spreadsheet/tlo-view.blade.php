@extends('perhitungan.spreadsheet.menu_ajk')
@section('title', 'Simulasi TLO')

<link href='{{ asset('assets/css/tlo.css') }}' rel='stylesheet'>

@section('content')
    <div class="tab-content">
        <div class="tab-pane active">
            <div class="box-body table-responsive" style="overflow: auto; width: 100%; height:425px;">
                <div class="content">
                    <center>
                        <b>
                            <p>PT BPR BANGUNARTA <br> SIMULASI PERHITUNGAN PREMI ASURANSI TLO <i>(TOTAL LOST ONLY)</i>
                            </p>
                        </b>
                    </center>

                    <table class="table1">
                        <thead>
                            <tr>
                                <td>
                                    <b>Nama Debitur</b>
                                </td>
                                <td>
                                    <b>:</b>
                                </td>
                                <td>
                                    &nbsp;&nbsp;&nbsp;<b>WAWA WIBAWA</b>
                                </td>
                                <td style="border-top: 1px solid rgb(255, 255, 255); border-bottom: 1px solid rgb(255, 255, 255);"
                                    width="4%"></td>
                                <td rowspan="2" style="float: right;"><img src="{{ asset('assets/img/pba.png') }}"
                                        alt="" width="250px">
                                </td>
                            </tr>
                        </thead>
                    </table>

                    <table class="bd">
                        <thead>
                            <tr>
                                <th rowspan="2">No</th>
                                <th rowspan="2">Jenis Kendaran</th>
                                <th rowspan="2">Jenis Kendaran</th>
                                <th rowspan="2">No Polisi</th>
                                <th rowspan="2">JW (Bulan)</th>
                                <th colspan="2">Realisasi</th>
                                <th rowspan="2">Nilai Pertanggungan</th>
                                <th colspan="5">Premi TLO</th>
                            </tr>
                            <tr>

                                <th>Realisasi</th>
                                <th>Jt Tempo</th>
                                <th>Rate</th>
                                <th>Premi</th>
                                <th>Polis</th>
                                <th>Total Premi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center">1</td>
                                <td>&nbsp;</td>
                                <td></td>
                                <td class="text-center"></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>

                            </tr>
                            <tr>
                                <td class="text-center">2</td>
                                <td>&nbsp;</td>
                                <td></td>
                                <td class="text-center"></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>

                            </tr>
                            <tr>
                                <td class="text-center">3</td>
                                <td>&nbsp;</td>
                                <td></td>
                                <td class="text-center"></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>

                            </tr>
                            <tr>
                                <td class="text-center">4</td>
                                <td>&nbsp;</td>
                                <td></td>
                                <td class="text-center"></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>

                            </tr>
                            <tr>
                                <td class="text-center">5</td>
                                <td>&nbsp;</td>
                                <td></td>
                                <td class="text-center"></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>

                            </tr>
                            <tr style="background-color: rgb(241, 241, 241);">
                                <td colspan="6" style="font-size: 14px; text-align:center; height:35px;">
                                    <b>Total Nilai
                                        Pertanggungan &
                                        Premi TLO</b>
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>

                            </tr>
                        </tbody>
                    </table>
                    <table class="ftr">
                        <tr>
                            <td width='25%' class="text-center fs-2"><b>Note * :</b></td>
                            <td>
                                <p><i>* Masa Asuransi TLO maksimal selama <b>36 bulan (3 Tahun)</b> jangka waktu kredit</i>
                                </p>
                                <p><i>* Proses klaim tidak berlaku untuk tindak penipuan/penggelapan kendaraan yang menjadi
                                        agunan</i></p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

    </div>
    </div>

    </div>
    </div>
@endsection
