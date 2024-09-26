@extends('skrining.menu_hasil_skrining')
@section('title', 'Hasil Screening')

@section('content')
    <div class="tab-content">
        <div class="tab-pane active">

            <div class="box-body table-responsive" style="width: 100%; height:425px;">
                <form action="{{ route('skrining.cetak') }}" method="get" target="_blank">
                    @csrf
                    <table class="table table-hover">
                        <thead style="font-weight: bold;">
                            <tr>
                                <td style="width: 10%;">NIK</td>
                                <td style="width: 3%;">:</td>
                                <td>
                                    <input type="hidden" value="{{ $nik }}" name="nik">
                                    {{ $nik }}
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 10%;">NAMA</td>
                                <td style="width: 3%;">:</td>
                                <td>
                                    <input type="hidden" value="{{ $nama }}" name="nama">
                                    {{ $nama }}
                                </td>
                            </tr>
                        </thead>
                    </table>

                    @if ($status == 'TERDAFTAR')
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 3%; text-align:center;">NO</th>
                                    <th style="width: 40%; text-align:center;">NAMA</th>
                                    <th style="text-align:center;">KETERANGAN</th>
                                </tr>
                            </thead>
                            <br>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td style="padding-left:45px;">DTTOT</td>
                                    <td style="display:flex; justify-content:center;">
                                        <select type="text" class="form-control" style="width: 50%; " name="dttot"
                                            id="">
                                            <option value="TERDAFTAR" {{ !empty($dttot) ? 'selected' : '' }}>
                                                TERDAFTAR</option>
                                            <option value="TIDAK TERDAFTAR" {{ empty($dttot) ? 'selected' : '' }}>TIDAK
                                                TERDAFTAR
                                            </option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td style="padding-left:45px;">DPPSPM</td>
                                    <td style="display:flex; justify-content:center;">
                                        <select type="text" class="form-control" style="width: 50%; " name="dppspm"
                                            id="">
                                            <option value="TERDAFTAR" {{ !empty($dppspm) ? 'selected' : '' }}>TERDAFTAR
                                            </option>
                                            <option value="TIDAK TERDAFTAR" {{ empty($dppspm) ? 'selected' : '' }}>TIDAK
                                                TERDAFTAR
                                            </option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td style="padding-left:45px;">JUDI ONLINE</td>
                                    <td style="display:flex; justify-content:center;">
                                        <select type="text" class="form-control" style="width: 50%; " name="judi_online"
                                            id="">
                                            <option value="TERDAFTAR" {{ !empty($judi) ? 'selected' : '' }}>TERDAFTAR
                                            </option>
                                            <option value="TIDAK TERDAFTAR" {{ empty($judi) ? 'selected' : '' }}>TIDAK
                                                TERDAFTAR
                                            </option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td style="padding-left:45px;">PEP</td>
                                    <td style="display:flex; justify-content:center;">
                                        <select type="text" class="form-control" style="width: 50%; " name="pep"
                                            id="">
                                            <option value="TERDAFTAR" {{ $pep == 'TERDAFTAR' ? 'selected' : '' }}>TERDAFTAR
                                            </option>
                                            <option value="TIDAK TERDAFTAR"
                                                {{ $pep == 'TIDAK TERDAFTAR' ? 'selected' : '' }}>
                                                TIDAK
                                                TERDAFTAR
                                            </option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>5</td>
                                    <td style="padding-left:45px;">NEGATIVE NEWS</td>
                                    <td style="display:flex; justify-content:center;">
                                        <select type="text" class="form-control" style="width: 50%; "
                                            name="negative_news" id="">
                                            <option value="TERDAFTAR" {{ !empty($negative_news) ? 'selected' : '' }}>
                                                TERDAFTAR
                                            </option>
                                            <option value="TIDAK TERDAFTAR" {{ empty($negative_news) ? 'selected' : '' }}>
                                                TIDAK
                                                TERDAFTAR
                                            </option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>6</td>
                                    <td style="padding-left:45px;">WATCH LIST</td>
                                    <td style="display:flex; justify-content:center;">
                                        <select type="text" class="form-control" style="width: 50%; " name="watch_list"
                                            id="">
                                            <option value="TERDAFTAR" {{ !empty($watch_list) ? 'selected' : '' }}>TERDAFTAR
                                            </option>
                                            <option value="TIDAK TERDAFTAR" {{ empty($watch_list) ? 'selected' : '' }}>
                                                TIDAK
                                                TERDAFTAR
                                            </option>
                                        </select>
                                    </td>
                                </tr>


                                <tr>
                                    <td>
                                        <button type="submit" class="btn btn-sm btn-primary"
                                            style="margin-top:20px;width:100%"><i class="fa fa-print"
                                                aria-hidden="true"></i>
                                            Print</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    @endif


                    <div class="box-body" style="overflow: auto;white-space: nowrap;width: 100%;">
                        @if (!empty($dttot))
                            <label for="">DTTOT</label>
                            <table class="table table-hover">
                                <thead style="background-color: rgb(235, 227, 227)">
                                    <tr>
                                        <th class="text-center">Nama</th>
                                        <th class="text-center">Deskripsi</th>
                                        <th class="text-center">Terduga</th>
                                        <th class="text-center">Kode Densus</th>
                                        <th class="text-center">Tempat Lahir</th>
                                        <th class="text-center">Tanggal Lahir </th>
                                        <th class="text-center">WN</th>
                                        <th class="text-center">Alamat</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($dttot as $row)
                                        <tr>
                                            @foreach ($row as $data)
                                                <td>{{ $data }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif

                        @if (!empty($dppspm))
                            <label for="">DPPSPM</label>
                            <table class="table table-hover">
                                <thead style="background-color: rgb(235, 227, 227)">
                                    <tr>
                                        <th class="text-center">Kode</th>
                                        <th class="text-center">Nama Bank</th>
                                        <th class="text-center">Alamat</th>
                                        <th class="text-center">Deskripsi</th>
                                        <th class="text-center">Deskripsi</th>
                                        <th class="text-center">Deskripsi</th>
                                        <th class="text-center">Deskripsi</th>
                                        <th class="text-center">Deskripsi</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($dppspm as $row)
                                        <tr>
                                            @foreach ($row as $data)
                                                <td>{{ $data }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif

                        @if (!empty($judi))
                            <label for="">JUDI ONLINE</label>
                            <table class="table table-hover">
                                <thead style="background-color: rgb(235, 227, 227)">
                                    <tr>
                                        <th class="text-center">Nama</th>
                                        <th class="text-center">No Rek</th>
                                        <th class="text-center">NIK Bank</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($judi as $row)
                                        <tr>
                                            @foreach ($row as $data)
                                                <td class="text-center">{{ $data }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif

                        @if (!empty($negative_news))
                            <label for="">NEGATIVE NEWS</label>
                            <table class="table table-hover">
                                <thead style="background-color: rgb(235, 227, 227)">
                                    <tr>
                                        <th class="text-center">Sumberh</th>
                                        <th class="text-center">Hari</th>
                                        <th class="text-center">Tanggal</th>
                                        <th class="text-center">Nama dalam Berita</th>
                                        <th class="text-center">Jabatan dalam Berita</th>
                                        <th class="text-center">Alamat dalam Berita</th>
                                        <th class="text-center">Dugaan TP Berdasarkan Data PPATK</th>
                                        <th class="text-center">Dugaan TP Berdasarkan UU 8 Tahun 2010</th>
                                        <th class="text-center">Keterangan Berita</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($negative_news as $row)
                                        <tr>
                                            @foreach ($row as $data)
                                                <td>{{ $data }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif

                        @if (!empty($watch_list))
                            <label for="">WATCH LIST</label>
                            <table class="table table-hover">
                                <thead style="background-color: rgb(235, 227, 227)">
                                    <tr>
                                        <th class="text-center">Periode</th>
                                        <th class="text-center">Id</th>
                                        <th class="text-center">Kode Watchlist</th>
                                        <th class="text-center">Jenis Pelaku</th>
                                        <th class="text-center">Nama Asli</th>
                                        <th class="text-center">Parameter Pencarian</th>
                                        <th class="text-center">Tempat Lahir</th>
                                        <th class="text-center">Tanggal Lahir (yyyy-mm-dd)</th>
                                        <th class="text-center">NPWP</th>
                                        <th class="text-center">KTP</th>
                                        <th class="text-center">PAS</th>
                                        <th class="text-center">KITAS</th>
                                        <th class="text-center">SUKET</th>
                                        <th class="text-center">SIM</th>
                                        <th class="text-center">KITAP</th>
                                        <th class="text-center">KIMS</th>
                                        <th class="text-center">Identitas Lain</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($watch_list as $row)
                                        <tr>
                                            @foreach ($row as $data)
                                                <td>{{ $data }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>

                </form>
            </div>
        </div>

    </div>
    </div>
@endsection
