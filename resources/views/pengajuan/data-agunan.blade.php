@extends('theme.app')
@section('title', 'Data Jaminan')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-3">
                    @include('theme.menu-pengajuan', ['nasabah' => $data->kd_pengajuan,])
                </div>

                <div class="col-xs-9">
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#kendaraan" data-toggle="tab">KENDARAAN</a>
                            </li>

                            <li>
                                <a href="#tanah" data-toggle="tab">TANAH</a>
                            </li>

                            <li>
                                <a href="#lainnya" data-toggle="tab">LAINNYA</a>
                            </li>
                        </ul>

                        <div class="tab-content">

                            <div class="tab-pane active" id="kendaraan">
                                <div class="box-body" style="margin-top: -10px;font-size:12px;">
                                    <table class="table table-striped table-hover table-bordered table-condensed">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width: 200px">Agunan</th>
                                                <th class="text-center" style="width: 200px">Informasi</th>
                                                <th class="text-center">Detail</th>
                                                <th class="text-center" style="width: 100px">Taksasi</th>
                                                <th class="text-center" style="width: 100px">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                                <tr>
                                                    <td style="vertical-align: middle;">
                                                        <b>Jenis: </b><br>
                                                        Kendaraan Bermotor Roda 2
                                                        <p></p>
                                                        <b>Dokumen: </b><br>
                                                        Kendaraan Bermotor Roda 2
                                                    </td>
                                                    <td style="vertical-align: middle;">
                                                        <b>Atas Nama: </b><br>
                                                        NINIS NURANISA <br>
                                                        <p></p>
                                                        <b>No Doukumen: </b><br>
                                                        P007772168
                                                    </td>
                                                    <td style="vertical-align: middle;">
                                                        <b>Merek: </b> Konten <br>
                                                        <b>Tahun: </b> Konten <br>
                                                        <b>No. Rangka: </b> Konten <br>
                                                        <b>No. Mesin: </b> Konten <br>
                                                        <b>No. Polisi: </b> Konten
                                                    </td>
                                                    <td style="vertical-align: middle;">
                                                        Rp.  21.000.000</td>
                                                    <td class="text-center" style="vertical-align: middle;text-transform:uppercase;">
                                                        <button data-toggle="modal" data-target="#modal-edit" data-id="15" class="btn btn-sm btn-warning">
                                                            <i class="fa fa-file-text-o"></i>
                                                        </button>
                        
                                                        <button id="15" data-toggle="modal" data-target="#modal-foto" class="btn btn-sm btn-primary" data-id="15">
                                                            <i class="fa fa-image"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                    </table>
                                    <a data-toggle="modal" data-target="#tambah-kendaraan" class="btn btn-sm btn-primary" style="margin-top:10px;">TAMBAH</a>
                                </div>
                            </div>

                            <div class="tab-pane" id="tanah">
                                <div class="box-body" style="margin-top: -10px;font-size:12px;">
                                    <table class="table table-striped table-hover table-bordered table-condensed">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width: 200px">Agunan</th>
                                                <th class="text-center" style="width: 150px">Informasi</th>
                                                <th class="text-center">Detail</th>
                                                <th class="text-center" style="width: 120px">Taksasi</th>
                                                <th class="text-center" style="width: 100px">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td style="vertical-align: middle;">
                                                    <b>Jenis: </b><br>
                                                    {{-- {{ $item->jenis_agunan }} --}}
                                                    <p></p>
                                                    <b>Dokumen: </b><br>
                                                    {{-- {{ $item->jenis_dokumen }} --}}
                                                </td>
                                                <td style="vertical-align: middle;">
                                                    <b>Atas Nama: </b><br>
                                                    {{-- {{ $item->atas_nama }}  --}}
                                                    <br>
                                                    <p></p>
                                                    <b>No Dokumen: </b><br>
                                                    {{-- {{ $item->no_dokumen }} --}}
                                                </td>
                                                <td style="vertical-align: middle;">
                                                    <b>Luas: </b> 
                                                    {{-- {{ $item->luas }} M2  --}}
                                                    <br>
                                                    <b>Lokasi: </b> <br>
                                                    {{-- {{ $item->lokasi }} --}}
                                                </td>
                                                <td style="vertical-align: middle;">
                                                    {{-- {{ 'RP. ' . ' ' . number_format($item->nilai_taksasi, 0, ',', '.') }} --}}
                                                </td>
                                                <td class="text-center" style="vertical-align: middle;text-transform:uppercase;">
                                                    <button data-toggle="modal" data-target="#modal-edit" data-id="#"
                                                        class="btn btn-sm btn-warning">
                                                        <i class="fa fa-file-text-o"></i>
                                                    </button>
                    
                                                    <button data-toggle="modal" data-target="#modal-foto" class="btn btn-sm btn-primary">
                                                        <i class="fa fa-image"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <a data-toggle="modal" data-target="#tambah-tanah" class="btn btn-sm btn-primary" style="margin-top:10px;">TAMBAH</a>
                                </div>
                            </div>

                            <div class="tab-pane" id="lainnya">
                                <div class="box-body" style="margin-top: -10px;font-size:12px;">
                                    <table class="table table-striped table-hover table-bordered table-condensed">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width: 200px">Agunan</th>
                                                <th class="text-center" style="width: 150px">Informasi</th>
                                                <th class="text-center">Detail</th>
                                                <th class="text-center" style="width: 100px">Taksasi</th>
                                                <th class="text-center" style="width: 100px">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td style="vertical-align: middle;">
                                                    <b>Jenis: </b><br>
                                                    Kendaraan Bermotor Roda 2
                                                    <p></p>
                                                    <b>Dokumen: </b><br>
                                                    BPKB Motor Non Fiducia
                                                </td>
                                                <td style="vertical-align: middle;">
                                                    <b>Atas Nama: </b><br>
                                                    NINIS NURANISA <br>
                                                    <p></p>
                                                    <b>No Doukumen: </b><br>
                                                    P007772168
                                                </td>
                                                <td style="vertical-align: middle;">
                                                    <b>Lokasi: </b> <br>
                                                    Jl. H. Iksan No.89, Pamanukan, Kec. Pamanukan
                                                    <p></p>
                                                    <b>Catatan: </b><br>
                                                    BPJS mudah dicairkan
                                                </td>
                                                <td style="vertical-align: middle;">Rp8.000.000</td>
                                                <td class="text-center" style="vertical-align: middle;text-transform:uppercase;">
                                                    <button data-toggle="modal" data-target="#modal-edit" class="btn btn-sm btn-warning">
                                                        <i class="fa fa-file-text-o"></i>
                                                    </button>
    
                                                    <button data-toggle="modal" data-target="#modal-foto" class="btn btn-sm btn-primary">
                                                        <i class="fa fa-image"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <a data-toggle="modal" data-target="#tambah-lainnya" class="btn btn-sm btn-primary" style="margin-top:10px;">TAMBAH</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </section>
    </div>

    <div class="modal fade" id="tambah-kendaraan">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-blue">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">AGUNANA KENDARAAN</h4>
                </div>

                <form action="#" method="POST">
                    <div class="modal-body">
                        <div class="box-body">
                            <div class="row">

                                <input type="text" value="{{ $data->auth }}" name="input_user" hidden>
                                <input type="text" value="{{ $pengajuan[0]->kode_pengajuan }}" name="pengajuan_kode" hidden>
                                <input type="text" name="jenis_jaminan" value="Kendaraan" hidden>

                                <div class="div-left">
                                    <div style="margin-top: -15px;">
                                        <span class="fw-bold">JENIS AGUNAN</span>
                                        <select type="text" class="form-control jenis_agunan" style="width: 100%;" name="jenis_agunan_kode" required>
                                            <option value="" selected>--PILIH--</option>
                                            {{ $agunan }}
                                            @foreach ($agunan as $item)
                                            <option value="{{ $item->kode }}">{{ $item->jenis_agunan }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">JENIS DOKUMEN</span>
                                        <select type="text" class="form-control jenis_dokumen" style="width: 100%;" name="jenis_dokumen_kode" required>
                                            <option value="" selected>--PILIH--</option>
                                            @foreach ($dok as $item)
                                                <option value="{{ $item->kode }}">{{ $item->jenis_dokumen }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">NOMOR BPKB</span>
                                        <input class="form-control text-uppercase" type="text" name="no_dokumen" placeholder="ENTRI" value="{{ old('no_dokumen') }}" required>
                                    </div>
                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">PEMILIK KENDARAAN</span>
                                        <input class="form-control text-uppercase" type="text" name="atas_nama" placeholder="ENTRI" value="{{ old('atas_nama') }}" required>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">NOMOR MESIN</span>
                                        <input class="form-control text-uppercase" type="text" name="no_mesin" value="{{ old('no_mesin') }}" placeholder="ENTRI" required>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">NOMOR POLISI</span>
                                        <input class="form-control text-uppercase" type="text" name="no_polisa" value="{{ old('no_polisi') }}" placeholder="ENTRI" required>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">NOMOR RANGKA</span>
                                        <input class="form-control text-uppercase" type="text" name="no_rangka" value="{{ old('no_rangka') }}" placeholder="ENTRI" required>
                                    </div>
                                </div>

                                <div class="div-right">
                                    <div style="margin-top: -15px;">
                                        <span class="fw-bold">TIPE KENDARAAN</span>
                                        <input class="form-control text-uppercase" type="text" name="tipe_kendaraan" value="{{ old('tipe_kendaraan') }}" placeholder="ENTRI" required>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">MEREK KENDARAAN</span>
                                        <input class="form-control text-uppercase" type="text" name="merek" value="{{ old('merek') }}" placeholder="ENTRI" required>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">TAHUN KENDARAAN</span>
                                        <input class="form-control text-uppercase" type="text" name="tahun" value="{{ old('tahun') }}" placeholder="ENTRI" required>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">WARNA KENDARAAN</span>
                                        <input class="form-control text-uppercase" type="text" name="warna" value="{{ old('warna') }}" placeholder="ENTRI" required>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">LOKASI KENDARAAN</span>
                                        <input class="form-control text-uppercase" type="text" name="lokasi" {{ old('lokasi') }} placeholder="ENTRI">
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">LOKASI DATI2</span>
                                        <select type="text" class="form-control dati2" style="width:100%;" name="kode_dati">
                                            <option value="" selected>--PILIH--</option>
                                            @foreach ($dati as $item)
                                                <option value="{{ $item->kode_dati }}">{{ $item->nama_dati }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">CATATAN AGUNAN</span>
                                        <input class="form-control text-uppercase" type="text"
                                            name="catatan" {{ old('catatan') }} placeholder="ENTRI">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="margin-top: -10px;">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">BATAL</button>
                        <button type="submit" class="btn btn-primary">SIMPAN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="tambah-tanah">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-blue">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">AGUNANA TANAH</h4>
                </div>

                <form action="#" method="POST">
                    <div class="modal-body">
                        <div class="box-body">
                            <div class="row">

                                <input type="text" name="jenis_jaminan" value="Tanah" hidden>

                                <div class="div-left">
                                    <div style="margin-top: -15px;">
                                        <span class="fw-bold">JENIS AGUNAN</span>
                                        <input type="text" name="id" id="id" hidden>
                                        <input class="form-control text-uppercase" type="text"
                                            value="" name="jenis_agunan" id="jenis_agunan">
                                    </div>
                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">JENIS DOKUMEN</span>
                                        <input class="form-control text-uppercase" type="text"
                                            value="" name="jenis_dokumen" id="jenis_dokumen">
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">NOMOR SERTIFIKAT</span>
                                        <input class="form-control text-uppercase" type="text"
                                            value="" name="no_dok" id="no_dok">
                                    </div>
                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">PEMILIK SERTIFIKAT</span>
                                        <input class="form-control text-uppercase" type="text"
                                            value="" name="atas_nama" id="atas_nama">
                                    </div>
                                </div>

                                <div class="div-right">
                                    <div style="margin-top: -15px;">
                                        <span class="fw-bold">LUAS TANAH (M2)</span>
                                        <input class="form-control text-uppercase" type="text"
                                            value="" name="luas" id="luas">
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">LOKASI TANAH</span>
                                        <input class="form-control text-uppercase" type="text"
                                            name="lokasi" id="lokasi" value="">
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">NILAI PASAR</span>
                                        <input class="form-control text-uppercase" type="text"
                                            name="nilai_pasar" id="nilai_pasar" placeholder="Rp." value="">
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">NILAI TAKSASI</span>
                                        <input class="form-control text-uppercase" type="text"
                                            name="nilai_taksasi" id="nilai_taksasi" placeholder="Rp." value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="margin-top: -10px;">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">BATAL</button>
                        <button type="submit" class="btn btn-primary">SIMPAN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="tambah-lainnya">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-blue">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">AGUNANA LAINNYA</h4>
                </div>

                <form action="#" method="POST">
                    <div class="modal-body">
                        <div class="box-body">
                            <div class="row">

                                <input type="text" name="jenis_jaminan" value="Lainnya" hidden>

                                <div class="div-left">
                                    <div style="margin-top: -15px;">
                                        <span class="fw-bold">JENIS AGUNAN</span>
                                        <input class="form-control text-uppercase" type="text"
                                            value="Kendaraan Bermotor Roda 2">
                                    </div>
                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">JENIS DOKUMEN</span>
                                        <input class="form-control text-uppercase" type="text"
                                            value="BPKB Motor Non Fiducia">
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">NOMOR DOKUMEN</span>
                                        <input class="form-control text-uppercase" type="text"
                                            value="P007772168">
                                    </div>
                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">NAMA PEMILIK</span>
                                        <input class="form-control text-uppercase" type="text"
                                            value="ZULFADLI RIZAL">
                                    </div>
                                </div>

                                <div class="div-right">
                                    <div style="margin-top: -15px;">
                                        <span class="fw-bold">LOKASI AGUNAN</span>
                                        <input class="form-control text-uppercase" type="text"
                                            value="Motor Metik">
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">NILAI PASAR</span>
                                        <input class="form-control text-uppercase" type="text"
                                            placeholder="Rp.">
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">NILAI TAKSASI</span>
                                        <input class="form-control text-uppercase" type="text"
                                            name="nilai_pasar" id="" placeholder="Rp.">
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">CATATAN</span>
                                        <input class="form-control text-uppercase" type="text"
                                            name="nilai_taksasi" id="" placeholder="ENTRI">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="margin-top: -10px;">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">BATAL</button>
                        <button type="submit" class="btn btn-primary">SIMPAN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('myscript')
    <script>
        //Initialize Select2 Elements
        $('.jenis_agunan').select2()
        $('.jenis_dokumen').select2()
        $('.dati2').select2()
    </script>
@endpush