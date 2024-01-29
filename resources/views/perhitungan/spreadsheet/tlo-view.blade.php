<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asuransi Jiwa Kredit Simulation</title>
</head>

<body>
    <div class="content">
        <div class="header">
            <center>
                <h4>PT BPR BANGUNARTA <br> SIMULASI PERHITUNGAN PREMI ASURANSI TLO <i>(TOTAL LOST ONLY)</i>
                </h4>
            </center>

            <table class="table1">
                <thead>
                    <tr>
                        <td
                            style="background:#f2efef; border-top: 1px solid black; border-bottom: 1px solid black; border-left: 1px solid black; border-bottom: 1px solid black;">
                            <b>Nama Debitur</b>
                        </td>
                        <td style="background:#f2efef; border-top: 1px solid black; border-bottom: 1px solid black;">
                            <b>:</b>
                        </td>
                        <td
                            style="background:#f2efef; border-top: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black;">
                            <b>WAWA WIBAWA</b>
                        </td>
                        <td style="border-top: 1px solid rgb(255, 255, 255); border-bottom: 1px solid rgb(255, 255, 255);"
                            width="4%"></td>
                        <td rowspan="2" style="float: right;"><img src="{{ asset('assets/img/pba.png') }}"
                                alt="" width="250px">
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <p>* Asuransi Jiwa Kredit hanya berlaku dari usia Minimal 20 s/d Maksimal 65 Tahun pada saat jatuh tempo kredit.
        Harap sesuaikan sisa masa pemberian kredit apabila usia debitur sudah memasuki 62 Tahun</p>
    <p>* Maksimal pertanggungan masing-masing fasilitas asuransi : ** BUMIDA 1967 Menurun UP 100 : 1 Milyar dengan usia
        maksimal 65 Tahun pada saat jatuh tempo kredit</p>
    <p>* Apabila Plafond Kredit > Maks. Pertanggungan, maka klaim dihitung berdasarkan sisa hutang pokok dari nilai
        Maks. Pertanggungan pada saat meninggal dunia dengan sistem angsuran Menurun, sesuai dengan suku bunga kredit
        yang berlaku</p>
</body>

</html>

@extends('perhitungan.spreadsheet.menu_ajk')
@section('title', 'Simulasi AJK')

@section('content')
    <div class="tab-content">
        <div class="tab-pane active">
            <div class="box-body table-responsive" style="overflow: auto; width: 100%; height:425px;">
                <form action="{{ route('premi') }}" method="get">
                    @csrf
                    <div class="card-body">

                        <table class="table table-vcenter">
                            <thead>
                                <tr>
                                    <th class="text-center fs-4" colspan="5" style="border: none;">
                                        <b>SIMULASI PERHITUNGAN PREMI ASURANSI JIWA
                                            KREDIT</b>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="no-border">
                                        <span class="fw-bold fs-4">Masukan Nama</span>
                                        <input type="text" class="form-control" name="nama" id="nama" required>
                                    </td>
                                    <td class="no-border">
                                        <span class="fw-bold fs-4">Masukan Plafon</span>
                                        <input type="text" class="form-control" name="plafon" id="plafons" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="no-border">
                                        <span class="fw-bold fs-4">Pilih Metode RPS</span>
                                        <select class="form-control input-sm form-border"
                                            style="border: 1px solid rgb(196, 196, 196); font-size: 14px; color: black;"
                                            name="rps" id="">
                                            <option value="" selected>---Pilih---</option>
                                            <option value="FLAT">FLAT</option>
                                            <option value="ANUITAS">ANUITAS</option>
                                            <option value="EFEKTIF">EFEKTIF</option>
                                            <option value="EFEKTIF MUSIMAN">EFEKTIF MUSIMAN
                                            </option>
                                            <option value="REKENING KORAN">REKENING KORAN
                                            </option>
                                        </select>
                                    </td>
                                    <td class="no-border">
                                        <span class="fw-bold fs-4">Pilih Produk</span>
                                        <select class="form-control input-sm form-border"
                                            style="border: 1px solid rgb(196, 196, 196); font-size: 14px; color: black;"
                                            name="produk" id="">
                                            <option value="" selected>---Pilih---</option>
                                            <option value="KRU">KRU</option>
                                            <option value="KUP">KUP</option>
                                            <option value="KM">KM</option>
                                            <option value="PRK">PRK</option>
                                            <option value="KTO">KTO</option>
                                            <option value="KBT">KBT</option>
                                            <option value="KPS">KPS</option>
                                            <option value="KIH">KIH</option>
                                            <option value="KPJ">KPJ</option>
                                            <option value="KRS">KRS</option>
                                            <option value="KPN">KPN</option>
                                            <option value="UMROH">UMROH</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="no-border">
                                        <span class="fw-bold fs-4">Masukan Jangka Waktu</span>
                                        <input type="text" class="form-control" name="jw"
                                            placeholder="Masukan Jangka Waktu" id="jk" required>
                                    </td>
                                    <td class="no-border">
                                        <span class="fw-bold fs-4">Tanggal Lahir
                                            (12-27-1994)</span>
                                        <input type="text" class="form-control" name="tgl_lahir"
                                            placeholder="Masukan Tanggal Lahir" id="tgllahir" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="no-border">
                                        <span class="fw-bold fs-4">Tanggal Realisasi</span>
                                        <input type="text" class="form-control" name="tgl_realisasi"
                                            placeholder="Tanggal Sekarang" id="hari">
                                    </td>
                                    <td class="no-border">
                                        <button type="submit" class="btn btn-primary"
                                            style="margin-top: 20px; width:100%;">
                                            Hitung
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>

    </div>
    </div>
@endsection
