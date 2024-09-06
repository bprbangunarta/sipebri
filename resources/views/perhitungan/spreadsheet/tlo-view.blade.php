<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href='{{ asset('assets/css/tlo.css') }}' rel='stylesheet'>

    <title>Simulasi TLO</title>
</head>

<body>
    <div class="content">
        <center>
            <b>
                <p>PT BPR BANGUNARTA <br> SIMULASI PERHITUNGAN PREMI ASURANSI TLO <i>(TOTAL LOST ONLY)</i>
                </p>
            </b>

            <div class="cont">
                <div class="left">
                    <div class="item"><b>Nama Debitur</b></div>
                    <div class="item"><b>:</b></div>
                    <div class="item"><b>{{ $data[5][4] }}</b></div>
                </div>
                <div class="img">
                    <img src="{{ asset('assets/img/pba.png') }}" alt="" width="400px" height="auto">
                </div>
            </div>

            <table class="bd">
                <thead>
                    <tr>
                        <th rowspan="2">No</th>
                        <th rowspan="2">Jenis Kendaran</th>
                        <th rowspan="2">No Polisi</th>
                        <th rowspan="2">JW (Bulan)</th>
                        <th colspan="2">Realisasi</th>
                        <th rowspan="2">Nilai Pertanggungan</th>
                        <th colspan="4">Premi TLO</th>
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
                        <td class="text-center" style="text-align: center">1</td>
                        <td>&nbsp;{{ $data[9][3] }}</td>
                        <td class="text-center">&nbsp;{{ $data[9][4] }}</td>
                        <td>&nbsp;{{ $data[9][5] }}</td>
                        <td>&nbsp;{{ $data[9][6] }}</td>
                        <td>&nbsp;{{ $data[9][7] }}</td>
                        <td>&nbsp;{{ number_format($data[9][8], '0', ',', '.') }}</td>
                        <td>&nbsp;{{ $data[9][9] }}</td>
                        <td>&nbsp;{{ $data[9][10] }}</td>
                        <td>&nbsp;{{ $data[9][11] }}</td>
                        <td>&nbsp;{{ $data[9][12] }}</td>

                    </tr>
                    <tr>
                        <td class="text-center" style="text-align: center">2</td>
                        <td>&nbsp;{{ $data[10][3] }}</td>
                        <td class="text-center">&nbsp;{{ $data[10][4] }}</td>
                        <td>&nbsp;{{ $data[10][5] }}</td>
                        <td>&nbsp;{{ $data[10][6] }}</td>
                        <td>&nbsp;{{ $data[10][7] }}</td>
                        <td>
                            @if (!empty($data[10][8]))
                                &nbsp;{{ number_format($data[10][8], '0', ',', '.') }}
                            @else
                            @endif
                        </td>
                        <td>&nbsp;{{ $data[10][9] }}</td>
                        <td>&nbsp;{{ $data[10][10] }}</td>
                        <td>&nbsp;{{ $data[10][11] }}</td>
                        <td>&nbsp;{{ $data[10][12] }}</td>

                    </tr>
                    <tr>
                        <td class="text-center" style="text-align: center">3</td>
                        <td>&nbsp;{{ $data[11][3] }}</td>
                        <td class="text-center">&nbsp;{{ $data[11][4] }}</td>
                        <td>&nbsp;{{ $data[11][5] }}</td>
                        <td>&nbsp;{{ $data[11][6] }}</td>
                        <td>&nbsp;{{ $data[11][7] }}</td>
                        <td>
                            @if (!empty($data[11][8]))
                                &nbsp;{{ number_format($data[11][8], '0', ',', '.') }}
                            @else
                            @endif
                        </td>
                        <td>&nbsp;{{ $data[11][9] }}</td>
                        <td>&nbsp;{{ $data[11][10] }}</td>
                        <td>&nbsp;{{ $data[11][11] }}</td>

                    </tr>
                    <tr>
                        <td class="text-center" style="text-align: center">4</td>
                        <td>&nbsp;{{ $data[12][3] }}</td>
                        <td class="text-center">&nbsp;{{ $data[12][4] }}</td>
                        <td>&nbsp;{{ $data[12][5] }}</td>
                        <td>&nbsp;{{ $data[12][6] }}</td>
                        <td>&nbsp;{{ $data[12][7] }}</td>
                        <td>
                            @if (!empty($data[12][8]))
                                &nbsp;{{ number_format($data[12][8], '0', ',', '.') }}
                            @else
                            @endif
                        </td>
                        <td>&nbsp;{{ $data[12][9] }}</td>
                        <td>&nbsp;{{ $data[12][10] }}</td>
                        <td>&nbsp;{{ $data[12][11] }}</td>

                    </tr>
                    <tr>
                        <td class="text-center" style="text-align: center">5</td>
                        <td>&nbsp;{{ $data[13][3] }}</td>
                        <td class="text-center">&nbsp;{{ $data[13][4] }}</td>
                        <td>&nbsp;{{ $data[13][5] }}</td>
                        <td>&nbsp;{{ $data[13][6] }}</td>
                        <td>&nbsp;{{ $data[13][7] }}</td>
                        <td>
                            @if (!empty($data[13][8]))
                                &nbsp;{{ number_format($data[13][8], '0', ',', '.') }}
                            @else
                            @endif
                        </td>
                        <td>&nbsp;{{ $data[13][9] }}</td>
                        <td>&nbsp;{{ $data[13][10] }}</td>
                        <td>&nbsp;{{ $data[13][11] }}</td>

                    </tr>
                    <tr style="background-color: rgb(241, 241, 241);">
                        <td colspan="6" style="font-size: 14px; text-align:center; height:35px;">
                            <b>Total Nilai
                                Pertanggungan &
                                Premi TLO</b>
                        </td>
                        <td style="font-size: 14px; text-align:center; height:35px;"><b>&nbsp;{{ $data[14][8] }}</b>
                        </td>
                        <td style="font-size: 14px; text-align:center; height:35px;"></td>
                        <td style="font-size: 14px; text-align:center; height:35px;"><b>&nbsp;{{ $data[14][10] }}</b>
                        </td>
                        <td style="font-size: 14px; text-align:center; height:35px;"><b>&nbsp;{{ $data[14][11] }}</b>
                        </td>
                        <td style="font-size: 14px; text-align:center; height:35px;"><b>&nbsp;{{ $data[14][12] }}</b>
                        </td>

                    </tr>
                </tbody>
            </table>
            <table class="ftr">
                <tr>
                    <td width='15%' class="text-center fs-2" style="padding-left:50px;"><b>Note * :</b></td>
                    <td>
                        <p><i>* Masa Asuransi TLO maksimal selama <b>36 bulan (3 Tahun)</b> jangka waktu kredit</i>
                        </p>
                        <p><i>* Proses klaim tidak berlaku untuk tindak penipuan/penggelapan kendaraan yang menjadi
                                agunan</i></p>
                    </td>
                </tr>
            </table>

        </center>
    </div>

    <script>
        window.print()
    </script>
</body>

</html>
