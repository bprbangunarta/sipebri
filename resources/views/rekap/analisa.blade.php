@extends('theme.app')
@section('title', 'Rekap Analisa')

@section('content')
<div class="content-wrapper">
    <section class="content">
        <div class="row">

            <div class="col-xs-12">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="#usaha" data-toggle="tab">
                                USAHA
                            </a>
                        </li>

                        <li>
                            <a href="#keuangan" data-toggle="tab">
                                KEUANGAN
                            </a>
                        </li>

                        <li>
                            <a href="#" class="">
                                KEPEMILIKAN
                            </a>
                        </li>

                        <li>
                            <a href="#" class="">
                                ANALISA 5C
                            </a>
                        </li>

                        <li>
                            <a href="#" class="">
                                KUALITATIF
                            </a>
                        </li>

                        <li>
                            <a href="#" class="">
                                MEMORANDUM
                            </a>
                        </li>

                        <li>
                            <a href="#" class="">
                                ADMINISTRASI
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane active" id="usaha">
                            <div class="box-body" style="margin-top: -10px;">

                                {{-- ANALISA USAHA PERTANIAN --}}
                                <table class="table table-striped table-hover table-bordered table-condensed">
                                    <thead>
                                        <tr class="bg-blue">
                                            <th class="text-center" colspan="4">USAHA PERTANIAN</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        {{-- LOOPING USAHA --}}
                                        <tr>
                                            <td colspan="4">
                                                <font class="text-uppercase">
                                                    <b>NAMA USAHA : </b> KARYAWAN - 
                                                    <b>SEKTOR: </b> PERTANIAN - 
                                                    <b>JENIS TANAMAN : </b> PADI KETAN -
                                                    <b>ALAMAT : </b> KAMPUNG SUKAGALIH RT/RW 030/008 SUKAMULYA PAGADEN SUBANG <br>

                                                    <b>LUAS MILIK SENDIRI : </b> 1000 M2 -
                                                    <b>LUAS HASIL SEWA : </b> 1000 M2 -
                                                    <b>LUAS HASIL GADAI : </b> 1000 M2 -
                                                    <b>TOTAL LUAS TANAH : </b> 3000 M2 -
                                                    <b>HASIL PANEN PER KW : </b> 3000 -
                                                    <b>HARGA PER KWINTAN : </b> Rp. 350.000
                                                </font>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-center" width="5%">NO</th>
                                            <th class="text-center">KETERANGAN</th>
                                            <th class="text-center" width="15%">PENDAPATAN</th>
                                            <th class="text-center" width="15%">PENGELUARAN</th>
                                        </tr>

                                        <tr>
                                            <td class="text-center">1</td>
                                            <td class="text-uppercase">Pendapatan Usaha</td>
                                            <td class="text-uppercase">Rp.  5.000.000</td>
                                            <td class="text-uppercase"></td>
                                        </tr>

                                        <tr>
                                            <td class="text-center">2</td>
                                            <td class="text-uppercase">Pajak Kendaraan</td>
                                            <td class="text-uppercase"></td>
                                            <td class="text-uppercase">Rp.  120.000</td>
                                        </tr>

                                        <tr>
                                            <td class="text-center">3</td>
                                            <td class="text-uppercase">Pengeluaran Lainnya</td>
                                            <td class="text-uppercase"></td>
                                            <td class="text-uppercase">Rp.  150.000</td>
                                        </tr>

                                        <tr>
                                            <td class="text-center">#</td>
                                            <th class="text-uppercase">TOTAL</th>
                                            <th class="text-uppercase">RP. 5.000.000</th>
                                            <th class="text-uppercase">Rp.  270.000</th>
                                        </tr>

                                        <tr>
                                            <th class="text-center text-uppercase" colspan="2">HASIL USAHA BERSIH</th>
                                            <th class="text-center text-uppercase" colspan="2">Rp. 4.730.000</th>
                                        </tr>
                                        {{-- END LOOPING USAHA --}} 
                                    </tbody>
                                </table>
                                {{-- END ANALISA USAHA PERTANIAN --}}
                                
                                {{-- ANALISA USAHA JASA --}}
                                <table class="table table-striped table-hover table-bordered table-condensed" style="margin-top:5px;">
                                    <thead>
                                        <tr class="bg-blue">
                                            <th class="text-center" colspan="4">USAHA JASA</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        {{-- LOOPING USAHA --}}
                                        <tr>
                                            <td colspan="4">
                                                <font>
                                                    <b>NAMA USAHA : </b> KARYAWAN - <b>LAMA USAHA : </b> 3 TAHUN - <b>ALAMAT : </b> KAMPUNG SUKAGALIH RT/RW 030/008 SUKAMULYA PAGADEN SUBANG 
                                                </font>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-center" width="5%">NO</th>
                                            <th class="text-center">KETERANGAN</th>
                                            <th class="text-center" width="15%">PENDAPATAN</th>
                                            <th class="text-center" width="15%">PENGELUARAN</th>
                                        </tr>

                                        <tr>
                                            <td class="text-center">1</td>
                                            <td class="text-uppercase">Pendapatan Usaha</td>
                                            <td class="text-uppercase">Rp.  5.000.000</td>
                                            <td class="text-uppercase"></td>
                                        </tr>

                                        <tr>
                                            <td class="text-center">2</td>
                                            <td class="text-uppercase">Pajak Kendaraan</td>
                                            <td class="text-uppercase"></td>
                                            <td class="text-uppercase">Rp.  120.000</td>
                                        </tr>

                                        <tr>
                                            <td class="text-center">3</td>
                                            <td class="text-uppercase">Pengeluaran Lainnya</td>
                                            <td class="text-uppercase"></td>
                                            <td class="text-uppercase">Rp.  150.000</td>
                                        </tr>

                                        <tr>
                                            <td class="text-center">#</td>
                                            <th class="text-uppercase">TOTAL</th>
                                            <th class="text-uppercase">RP. 5.000.000</th>
                                            <th class="text-uppercase">Rp.  270.000</th>
                                        </tr>

                                        <tr>
                                            <th class="text-center text-uppercase" colspan="2">HASIL USAHA BERSIH</th>
                                            <th class="text-center text-uppercase" colspan="2">Rp. 4.730.000</th>
                                        </tr>
                                        {{-- END LOOPING USAHA --}} 
                                    </tbody>
                                </table>
                                {{-- END ANALISA USAHA JASA --}}

                                {{-- ANALISA USAHA LAINNYA --}}
                                <table class="table table-striped table-hover table-bordered table-condensed" style="margin-top:5px;">
                                    <thead>
                                        <tr class="bg-blue">
                                            <th class="text-center" colspan="4">USAHA LAINNYA</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- LOOPING USAHA --}}
                                        <tr>
                                            <td colspan="4">
                                                <font>
                                                    <b>NAMA USAHA : </b> KARYAWAN - <b>LAMA USAHA : </b> 3 TAHUN - <b>ALAMAT : </b> KAMPUNG SUKAGALIH RT/RW 030/008 SUKAMULYA PAGADEN SUBANG 
                                                </font>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-center" width="5%">NO</th>
                                            <th class="text-center">KETERANGAN</th>
                                            <th class="text-center" width="15%">PENDAPATAN</th>
                                            <th class="text-center" width="15%">PENGELUARAN</th>
                                        </tr>
                                        
                                        <tr>
                                            <td class="text-center">1</td>
                                            <td class="text-uppercase">Nama Pendapatan 1</td>
                                            <td class="text-uppercase">Rp.  5.000.000</td>
                                            <td class="text-uppercase"></td>
                                        </tr>

                                        <tr>
                                            <td class="text-center">2</td>
                                            <td class="text-uppercase">Nama Pendapatan 2</td>
                                            <td class="text-uppercase">Rp.  5.000.000</td>
                                            <td class="text-uppercase"></td>
                                        </tr>

                                        <tr>
                                            <td class="text-center">3</td>
                                            <td class="text-uppercase">Nama Pendapatan 3</td>
                                            <td class="text-uppercase">Rp.  5.000.000</td>
                                            <td class="text-uppercase"></td>
                                        </tr>

                                        <tr>
                                            <td class="text-center">4</td>
                                            <td class="text-uppercase">Nama Pendapatan 4</td>
                                            <td class="text-uppercase">Rp.  5.000.000</td>
                                            <td class="text-uppercase"></td>
                                        </tr>

                                        <tr>
                                            <td class="text-center">5</td>
                                            <td class="text-uppercase">Pengeluaran 1</td>
                                            <td class="text-uppercase"></td>
                                            <td class="text-uppercase">Rp.  120.000</td>
                                        </tr>

                                        <tr>
                                            <td class="text-center">6</td>
                                            <td class="text-uppercase">Pengeluaran 2</td>
                                            <td class="text-uppercase"></td>
                                            <td class="text-uppercase">Rp.  120.000</td>
                                        </tr>

                                        <tr>
                                            <td class="text-center">7</td>
                                            <td class="text-uppercase">Pengeluaran 3</td>
                                            <td class="text-uppercase"></td>
                                            <td class="text-uppercase">Rp.  120.000</td>
                                        </tr>

                                        <tr>
                                            <td class="text-center">8</td>
                                            <td class="text-uppercase">Pengeluaran 4</td>
                                            <td class="text-uppercase"></td>
                                            <td class="text-uppercase">Rp.  120.000</td>
                                        </tr>

                                        <tr>
                                            <td class="text-center">9</td>
                                            <td class="text-uppercase">Proyeksi Penambahan</td>
                                            <td class="text-uppercase">Rp.  1.000.000</td>
                                            <td class="text-uppercase"></td>
                                        </tr>

                                        <tr>
                                            <td class="text-center">#</td>
                                            <th class="text-uppercase">TOTAL</th>
                                            <th class="text-uppercase">RP. 21.000.000</th>
                                            <th class="text-uppercase">Rp.  480.000</th>
                                        </tr>

                                        <tr>
                                            <th class="text-center text-uppercase" colspan="2">HASIL USAHA BERSIH</th>
                                            <th class="text-center text-uppercase" colspan="2">Rp. 20.520.000</th>
                                        </tr>
                                        {{-- END LOOPING USAHA --}}
                                    </tbody>
                                </table>
                                {{-- END ANALISA USAHA LAINNYA --}}
                            </div>
                        </div>

                        <div class="tab-pane" id="keuangan">
                            <div class="box-body" style="margin-top: -10px;">
                                CONTEN KEUANGAN
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </section>
</div>
@endsection