@extends('perhitungan.spreadsheet.menu_ajk')
@section('title', 'Simulasi AJK')

<link href='{{ asset('assets/css/ajk.css') }}' rel='stylesheet'>

@section('content')
    <div class="tab-content">
        <div class="tab-pane active">
            <div class="box-body table-responsive" style="overflow: auto; width: 100%; height:425px;">
                <center>
                    <table border="2" style="border-bottom: double;">
                        <thead>
                            <tr>
                                <th>
                                    <p></p>
                                </th>
                            </tr>
                            <tr>
                                <th colspan="10" class="up">PT BPR BANGUNARTA</th>
                            </tr>
                            <tr>
                                <th colspan="10" class="down">SIMULASI PERHITUNGAN PREMI ASURANSI JIWA
                                    KREDIT</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody class="tbup">
                            <tr>
                                <td class="one"></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="one"></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="one"></td>
                                <td style="width: 15%;"><b>Nama Debitur</b></td>
                                <td style="text-align:center; width: 2%;"><b>:</b></td>
                                <td style="width: 18%;"><b>{{ $data[5][4] }}</b></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="8" class="img border"><img src="{{ asset('assets/img/pba.png') }}"
                                        alt="">
                                </td>
                            </tr>
                            <tr>
                                <td class="one"></td>
                                <td style="width: 15%;">Plafond Kredit</td>
                                <td style="text-align:center;">:</td>
                                <td style="width: 18%;">
                                    {{-- {{ 'Rp ' . number_format($data[8][4], 0, ',', '.') }}</td> --}}
                                    {{ 'Rp ' . $data[8][4] }}</td>
                                <td style="width: 11%;">Tgl. Lahir</td>
                                <td style="text-align:left;">:</td>
                                <td style="width: 18%;">{{ $data[8][8] }}</td>
                                <td style="width: 15%;"><b>Maks. Pertanggungan</b></td>
                                <td style="width: 1%; text-align:left;"><b>:</b></td>
                                <td style="width: 25%;"><b>{{ 'Rp. ' . ' ' . $data[8][12] }}</b>
                                </td>
                            </tr>
                            <tr>
                                <td class="one"></td>
                                <td style="width: 15%;">J.W (Bulan)</td>
                                <td style="text-align:center;">:</td>
                                <td style="width: 18%;">{{ $data[9][4] }}</td>
                                <td style="width: 11%;">Tgl. Realisasi</td>
                                <td style="text-align:left;">:</td>
                                <td style="width: 18%;">{{ $data[9][8] }}</td>
                                <td style="width: 15%;"><b>Rate</b></td>
                                <td style="width: 1%; text-align:left;"><b>:</b></td>
                                <td style="width: 25%;"><b>{{ $data[9][12] }}</b></td>
                            </tr>
                            <tr>
                                <td class="one"></td>
                                <td style="width: 15%; font-size: 12px;">Sistem Angsuran (RPS)
                                </td>
                                <td style="text-align:center;">:</td>
                                <td style="width: 18%;">{{ $data[10][4] }}</td>
                                <td style="width: 11%;">Usia Realisasi</td>
                                <td style="text-align:left;">:</td>
                                <td style="width: 18%;">{{ $data[10][8] }}</td>
                                <td style="width: 15%;"><b>Keterangan Medis</b></td>
                                <td style="width: 1%; text-align:left;"><b>:</b></td>
                                <td style="width: 25%;"><b>{{ $data[10][12] }}</b>
                                </td>
                            </tr>
                            <tr>
                                <td class="one"></td>
                                <td style="width: 15%;">Produk Kredit</td>
                                <td style="text-align:center;">:</td>
                                <td style="width: 18%;">{{ $data[11][4] }}</td>
                                <td style="width: 11%;">Usia Jt Tempo</td>
                                <td style="text-align:left;">:</td>
                                <td style="width: 18%;">{{ $data[11][8] }}</td>
                            </tr>
                            <tr>
                                <td class="one"></td>
                                <td style="width: 13%;"></td>
                                <td style="text-align:center;"></td>
                                <td style="width: 12%;"></td>
                                <td style="width: 11%;">Jasa Asuransi</td>
                                <td style="text-align:left;">:</td>
                                <td style="width: 18%;"></td>
                            </tr>
                            <tr>
                                <td class="one"></td>
                                @if (is_null($data[14][2]))
                                    <td rowspan="2" colspan="3" class="one" style="color: red;"><b></b></td>
                                @else
                                    <td rowspan="2" colspan="3" class="one" style="color: red;">
                                        <b>{{ $data[14][2] }}</b>
                                    </td>
                                @endif
                                <td rowspan="2" colspan="2" style="width: 9%;">
                                    <b>BUMIDA 1967 <br> MENURUN UP 100</b>
                                </td>
                                <td rowspan="2" style="width: 18%; color: red;"><b>
                                        @if ($data[12][8] == 'Ditolak usia <20')
                                            {{ $data[12][8] }}
                                        @endif
                                    </b></td>
                                <td rowspan="2" style="width: 15%; font-size: 16px;">
                                    <b>Premi</b>
                                </td>
                                <td rowspan="2" style="text-align:left; font-size: 20px;">
                                    <b>:</b>
                                </td>
                                <td rowspan="2" style="width: 25%; font-size: 16px;"><b>
                                        @if ($data[14][12] === ' Err ')
                                            {{ $data[14][12] }}
                                        @else
                                            {{ 'Rp. ' . $data[14][12] }}
                                        @endif
                                    </b></td>
                            </tr>
                            <tr>
                                <td class="one"></td>
                                <td style="width: 12%;"></td>
                                <td style="text-align:center;"></td>
                                <td style="width: 12%;"></td>
                                <td style="width: %;"></td>
                                <td style="text-align:left;"></td>
                                <td style="width: 13%;"></td>
                            </tr>
                        </tbody>
                    </table>
                    <table border="2" style="margin-top: 1px; border-top: none;">
                        <tr style="font-size: 12px">
                            <td style="width: 11%; display: flexbox; text-align: center;">
                                <i><b>Note * :</b></i>
                            </td>
                            <td style="width: 2%;"><i>*</i></td>
                            <td>
                                <i>Asuransi Jiwa Kredit hanya berlaku dari usia <b>Minimal
                                        20</b> s/d <b>Maksimal 65 Tahun</b> pada
                                    saat
                                    jatuh
                                    tempo kredit. Harap sesuaikan sisa masa pemberian kredit
                                    apabila usia debitur sudah memasuki
                                    <b>62 Tahun</b></i>
                            </td>
                        </tr>
                        <tr style="font-size: 12px">
                            <td><i><b></b></i>
                            </td>
                            <td><i>*</i></td>
                            <td><i>Maksimal pertanggungan masing-masing fasilitas asuransi :
                                </i></td>
                        </tr>
                        <tr style="font-size: 12px">
                            <td><i><b></b></i>
                            </td>
                            <td><i></i></td>
                            <td><i>** BUMIDA 1967 Menurun UP 100
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:
                                    1 Milyar dengan usia maksimal <b>65 Tahun</b> pada saat
                                    jatuh tempo kredit </i></td>
                        </tr>
                        <tr style="font-size: 12px">
                            <td><i><b></b></i>
                            </td>
                            <td><i>*</i></td>
                            <td>
                                <i>
                                    Apabila <b>Plafond Kredit > Maks. Pertanggungan</b>, maka
                                    klaim dihitung berdasarkan <b>sisa
                                        hutang pokok dari nilai Maks. Pertanggungan</b> pada
                                    saat meninggal dunia dengan sistem
                                    angsuran <b>Menurun</b>, sesuai dengan suku bunga kredit
                                    yang berlaku</i>
                            </td>
                        </tr>
                    </table>
                </center>
            </div>
        </div>
    </div>
@endsection
