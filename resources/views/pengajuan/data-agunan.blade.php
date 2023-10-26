@extends('theme.app')
@section('title', 'Data Jaminan')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-3">
                    @include('theme.menu-pengajuan', ['nasabah' => $data->kd_pengajuan])
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
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr class="bg-blue">
                                                <th class="text-center" style="width: 200px">AGUNAN</th>
                                                <th class="text-center" style="width: 200px">INFORMASI</th>
                                                <th class="text-center">DETAIL</th>
                                                <th class="text-center" style="width: 100px">TAKSASI</th>

                                                @can('edit pengajuan kredit')
                                                    <th class="text-center" style="width: 100px">AKSI</th>
                                                @endcan

                                                @can('otorisasi pengajuan kredit')
                                                    <th class="text-center" style="width: 50px">AKSI</th>
                                                @endcan
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($jaminan as $item)
                                                @if ($item->jenis_jaminan == 'Kendaraan')
                                                    <tr>
                                                        <td style="vertical-align: middle;">
                                                            <b>Jenis: </b><br>
                                                            {{ $item->jenis_agunan }}
                                                            <p></p>
                                                            <b>Dokumen: </b><br>
                                                            {{ $item->jenis_dokumen }}
                                                        </td>
                                                        <td style="vertical-align: middle;">
                                                            <b>Atas Nama: </b><br>
                                                            {{ $item->atas_nama }} <br>
                                                            <p></p>
                                                            <b>No Doukumen: </b><br>
                                                            {{ $item->no_dokumen }}
                                                        </td>
                                                        <td style="vertical-align: middle;">
                                                            <b>Merek: </b> {{ $item->merek }} <br>
                                                            <b>Tahun: </b> {{ $item->tahun }} <br>
                                                            <b>No. Rangka: </b> {{ $item->no_rangka }} <br>
                                                            <b>No. Mesin: </b> {{ $item->no_mesin }} <br>
                                                            <b>No. Polisi: </b> {{ $item->no_polisi }}
                                                        </td>
                                                        <td style="vertical-align: middle;">
                                                            {{ 'Rp. ' . ' ' . number_format($item->nilai_taksasi, 0, ',' . ',') ?? 0 }}
                                                        </td>

                                                        @can('edit pengajuan kredit')
                                                            <td class="text-center"
                                                                style="vertical-align: middle;text-transform:uppercase;">
                                                                <button data-toggle="modal" data-target="#modal-edit-kendaraan"
                                                                    data-id="{{ $item->id }}"
                                                                    class="btn btn-sm btn-warning">
                                                                    <i class="fa fa-file-text-o"></i>
                                                                </button>

                                                                <button id="15" data-toggle="modal"
                                                                    data-target="#modal-foto-kendaraan"
                                                                    class="btn btn-sm btn-primary"
                                                                    data-id="{{ $item->id }}, {{ $item->atas_nama }}">
                                                                    <i class="fa fa-image"></i>
                                                                </button>
                                                            </td>
                                                        @endcan

                                                        @can('otorisasi pengajuan kredit')
                                                            <td class="text-center"
                                                                style="vertical-align: middle;text-transform:uppercase;">
                                                                <button data-toggle="modal" data-target="#otor-kendaraan"
                                                                    data-id="{{ $item->id }}"
                                                                    class="btn btn-sm btn-success">
                                                                    <i class="fa fa-check"></i>
                                                                </button>
                                                            </td>
                                                        @endcan
                                                    </tr>
                                                @endif
                                            @empty
                                                <tr>
                                                    <td class="text-center" colspan="7">TIDAK ADA DATA</td>
                                                </tr>
                                            @endforelse

                                        </tbody>
                                    </table>

                                    @can('edit pengajuan kredit')
                                        <a data-toggle="modal" data-target="#tambah-kendaraan" class="btn btn-sm btn-primary"
                                            style="margin-top:10px;">TAMBAH</a>
                                    @endcan

                                </div>
                            </div>

                            <div class="tab-pane" id="tanah">
                                <div class="box-body" style="margin-top: -10px;font-size:12px;">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr class="bg-blue">
                                                <th class="text-center" style="width: 200px">AGUNAN</th>
                                                <th class="text-center" style="width: 150px">INFORMASI</th>
                                                <th class="text-center">DETAIL</th>
                                                <th class="text-center" style="width: 120px">TAKSASI</th>

                                                @can('edit pengajuan kredit')
                                                    <th class="text-center" style="width: 100px">AKSI</th>
                                                @endcan

                                                @can('otorisasi pengajuan kredit')
                                                    <th class="text-center" style="width: 50px">AKSI</th>
                                                @endcan
                                            </tr>
                                        </thead>
                                        <tbody>

                                            @forelse ($jaminan as $item)
                                                @if ($item->jenis_jaminan == 'Tanah')
                                                    <tr>
                                                        <td style="vertical-align: middle;">
                                                            <b>Jenis: </b><br>
                                                            {{ $item->jenis_agunan }}
                                                            <p></p>
                                                            <b>Dokumen: </b><br>
                                                            {{ $item->jenis_dokumen }}
                                                        </td>
                                                        <td style="vertical-align: middle;">
                                                            <b>Atas Nama: </b><br>
                                                            {{ $item->atas_nama }}
                                                            <br>
                                                            <p></p>
                                                            <b>No Dokumen: </b><br>
                                                            {{ $item->no_dokumen }}
                                                        </td>
                                                        <td style="vertical-align: middle;">
                                                            <b>Luas: </b>
                                                            {{ $item->luas }} M2
                                                            <br>
                                                            <b>Lokasi: </b> <br>
                                                            {{ $item->lokasi }}
                                                        </td>
                                                        <td style="vertical-align: middle;">
                                                            {{ 'RP. ' . ' ' . number_format($item->nilai_taksasi, 0, ',', '.') }}
                                                        </td>

                                                        @can('edit pengajuan kredit')
                                                            <td class="text-center"
                                                                style="vertical-align: middle;text-transform:uppercase;">
                                                                <button data-toggle="modal" data-target="#modal-edit-tanah"
                                                                    data-id="{{ $item->id }}"
                                                                    class="btn btn-sm btn-warning">
                                                                    <i class="fa fa-file-text-o"></i>
                                                                </button>

                                                                <button data-toggle="modal" data-target="#modal-foto-tanah"
                                                                    class="btn btn-sm btn-primary"
                                                                    data-id="{{ $item->id }}, {{ $item->atas_nama }}">
                                                                    <i class="fa fa-image"></i>
                                                                </button>
                                                            </td>
                                                        @endcan

                                                        @can('otorisasi pengajuan kredit')
                                                            <td class="text-center"
                                                                style="vertical-align: middle;text-transform:uppercase;">
                                                                <button data-toggle="modal" data-target="#otor-tanah"
                                                                    data-id="{{ $item->id }}"
                                                                    class="btn btn-sm btn-success">
                                                                    <i class="fa fa-check"></i>
                                                                </button>
                                                            </td>
                                                        @endcan
                                                    </tr>
                                                @endif
                                            @empty
                                                <tr>
                                                    <td class="text-center" colspan="7">TIDAK ADA DATA
                                                    </td>
                                                </tr>
                                            @endforelse

                                        </tbody>
                                    </table>
                                    @can('edit pengajuan kredit')
                                        <a data-toggle="modal" data-target="#tambah-tanah" class="btn btn-sm btn-primary"
                                            style="margin-top:10px;">TAMBAH</a>
                                    @endcan
                                </div>
                            </div>

                            <div class="tab-pane" id="lainnya">
                                <div class="box-body" style="margin-top: -10px;font-size:12px;">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr class="bg-blue">
                                                <th class="text-center" style="width: 200px">AGUNAN</th>
                                                <th class="text-center" style="width: 150px">INFORMASI</th>
                                                <th class="text-center">DETAIL</th>
                                                <th class="text-center" style="width: 100px">TAKSASI</th>

                                                @can('edit pengajuan kredit')
                                                    <th class="text-center" style="width: 100px">AKSI</th>
                                                @endcan

                                                @can('otorisasi pengajuan kredit')
                                                    <th class="text-center" style="width: 50px">AKSI</th>
                                                @endcan
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($jaminan as $item)
                                                @if ($item->jenis_jaminan == 'Lainnya')
                                                    <tr>
                                                        <td style="vertical-align: middle;">
                                                            <b>Jenis: </b><br>
                                                            {{ $item->jenis_agunan }}
                                                            <p></p>
                                                            <b>Dokumen: </b><br>
                                                            {{ $item->jenis_dokumen }}
                                                        </td>
                                                        <td style="vertical-align: middle;">
                                                            <b>Atas Nama: </b><br>
                                                            {{ $item->atas_nama }}
                                                            <br>
                                                            <p></p>
                                                            <b>No Dokumen: </b><br>
                                                            {{ $item->no_dokumen }}
                                                        </td>
                                                        <td style="vertical-align: middle;">
                                                            <b>Lokasi: </b> <br>
                                                            {{ $item->lokasi }}
                                                            <p></p>
                                                            <b>Catatan: </b><br>
                                                            {{ $item->catatan }}
                                                        </td>
                                                        <td style="vertical-align: middle;">
                                                            {{ 'RP. ' . ' ' . number_format($item->nilai_taksasi, 0, ',', '.') }}
                                                        </td>

                                                        @can('edit pengajuan kredit')
                                                            <td class="text-center"
                                                                style="vertical-align: middle;text-transform:uppercase;">
                                                                <button data-toggle="modal" data-target="#modal-edit-lain"
                                                                    data-id="{{ $item->id }}"
                                                                    class="btn btn-sm btn-warning">
                                                                    <i class="fa fa-file-text-o"></i>
                                                                </button>

                                                                <button data-toggle="modal" data-target="#modal-foto-lain"
                                                                    class="btn btn-sm btn-primary"
                                                                    data-id="{{ $item->id }}, {{ $item->atas_nama }}">
                                                                    <i class="fa fa-image"></i>
                                                                </button>
                                                            </td>
                                                        @endcan

                                                        @can('otorisasi pengajuan kredit')
                                                            <td class="text-center"
                                                                style="vertical-align: middle;text-transform:uppercase;">
                                                                <button data-toggle="modal" data-target="#otor-lain"
                                                                    data-id="{{ $item->id }}"
                                                                    class="btn btn-sm btn-success">
                                                                    <i class="fa fa-check"></i>
                                                                </button>
                                                            </td>
                                                        @endcan
                                                    </tr>
                                                @endif
                                            @empty
                                                <tr>
                                                    <td class="text-center" colspan="7">TIDAK ADA DATA
                                                    </td>
                                                </tr>
                                            @endforelse

                                        </tbody>
                                    </table>
                                    @can('edit pengajuan kredit')
                                        <a data-toggle="modal" data-target="#tambah-lainnya" class="btn btn-sm btn-primary"
                                            style="margin-top:10px;">TAMBAH</a>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </section>
    </div>

    @include('pengajuan.include.edit-kendaraan')
    @include('pengajuan.include.otor-kendaraan')
    @include('pengajuan.include.foto-kendaraan')
    @include('pengajuan.include.edit-tanah')
    @include('pengajuan.include.otor-tanah')
    @include('pengajuan.include.foto-tanah')
    @include('pengajuan.include.edit-lain')
    @include('pengajuan.include.otor-lain')
    @include('pengajuan.include.foto-lain')

    <div class="modal fade" id="tambah-kendaraan">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-blue">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">AGUNAN KENDARAAN</h4>
                </div>

                <form action="{{ route('kendaraan.simpan') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="box-body">
                            <div class="row">

                                <input type="text" value="{{ $data->auth }}" name="input_user" hidden>
                                <input type="text" value="{{ $pengajuan->kode_pengajuan }}" name="pengajuan_kode"
                                    hidden>
                                <input type="text" name="jenis_jaminan" value="Kendaraan" hidden>

                                <div class="div-left">
                                    <div style="margin-top: -15px;">
                                        <span class="fw-bold">JENIS AGUNAN</span>
                                        <select type="text" class="form-control jenis_agunan" style="width: 100%;"
                                            name="jenis_agunan_kode" required>
                                            <option value="" selected>--PILIH--</option>
                                            {{ $agunan }}
                                            @foreach ($agunan as $item)
                                                <option value="{{ $item->kode }}">{{ $item->jenis_agunan }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">JENIS DOKUMEN</span>
                                        <select type="text" class="form-control jenis_dokumen" style="width: 100%;"
                                            name="jenis_dokumen_kode" required>
                                            <option value="" selected>--PILIH--</option>
                                            @foreach ($dok as $item)
                                                <option value="{{ $item->kode }}">{{ $item->jenis_dokumen }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">NOMOR BPKB</span>
                                        <input class="form-control text-uppercase" type="text" name="no_dokumen"
                                            placeholder="ENTRI" value="{{ old('no_dokumen') }}" required>
                                    </div>
                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">PEMILIK KENDARAAN</span>
                                        <input class="form-control text-uppercase" type="text" name="atas_nama"
                                            placeholder="ENTRI" value="{{ old('atas_nama') }}" required>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">NOMOR MESIN</span>
                                        <input class="form-control text-uppercase" type="text" name="no_mesin"
                                            value="{{ old('no_mesin') }}" placeholder="ENTRI" required>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">NOMOR POLISI</span>
                                        <input class="form-control text-uppercase" type="text" name="no_polisi"
                                            value="{{ old('no_polisi') }}" placeholder="ENTRI" required>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">NOMOR RANGKA</span>
                                        <input class="form-control text-uppercase" type="text" name="no_rangka"
                                            value="{{ old('no_rangka') }}" placeholder="ENTRI" required>
                                    </div>
                                </div>

                                <div class="div-right">
                                    <div style="margin-top: -15px;">
                                        <span class="fw-bold">TIPE KENDARAAN</span>
                                        <input class="form-control text-uppercase" type="text" name="tipe_kendaraan"
                                            value="{{ old('tipe_kendaraan') }}" placeholder="ENTRI" required>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">MEREK KENDARAAN</span>
                                        <input class="form-control text-uppercase" type="text" name="merek"
                                            value="{{ old('merek') }}" placeholder="ENTRI" required>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">TAHUN KENDARAAN</span>
                                        <input class="form-control text-uppercase" type="text" name="tahun"
                                            value="{{ old('tahun') }}" placeholder="ENTRI" required>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">WARNA KENDARAAN</span>
                                        <input class="form-control text-uppercase" type="text" name="warna"
                                            value="{{ old('warna') }}" placeholder="ENTRI" required>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">LOKASI KENDARAAN</span>
                                        <input class="form-control text-uppercase" type="text" name="lokasi"
                                            {{ old('lokasi') }} placeholder="ENTRI">
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">LOKASI DATI2</span>
                                        <select type="text" class="form-control dati2" style="width:100%;"
                                            name="kode_dati">
                                            <option value="" selected>--PILIH--</option>
                                            @foreach ($dati as $item)
                                                <option value="{{ $item->kode_dati }}">{{ $item->nama_dati }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">CATATAN AGUNAN</span>
                                        <input class="form-control text-uppercase" type="text" name="catatan"
                                            {{ old('catatan') }} placeholder="ENTRI">
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
                    <h4 class="modal-title">AGUNAN TANAH</h4>
                </div>

                <form action="{{ route('tanah.simpan') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="box-body">
                            <div class="row">

                                <input type="text" name="jenis_jaminan" value="Tanah" hidden>

                                <div class="div-left">
                                    <div style="margin-top: -15px;">
                                        <span class="fw-bold">JENIS AGUNAN</span>
                                        <input type="text" value="{{ $data->auth }}" name="input_user" hidden>
                                        <input type="text" value="{{ $pengajuan->kode_pengajuan }}"
                                            name="pengajuan_kode" hidden>
                                        <select type="text" class="form-control jenis_agunan" style="width: 100%;"
                                            name="jenis_agunan_kode" required>
                                            <option value="" selected>--PILIH--</option>
                                            {{ $agunan }}
                                            @foreach ($agunan as $item)
                                                <option value="{{ $item->kode }}">{{ $item->jenis_agunan }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">JENIS DOKUMEN</span>
                                        <select type="text" class="form-control jenis_dokumen" style="width: 100%;"
                                            name="jenis_dokumen_kode" required>
                                            <option value="" selected>--PILIH--</option>
                                            @foreach ($dok as $item)
                                                <option value="{{ $item->kode }}">{{ $item->jenis_dokumen }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">NOMOR SERTIFIKAT</span>
                                        <input class="form-control text-uppercase" type="text"
                                            value="{{ old('no_dok') }}" name="no_dokumen" id="no_dok" placeholder="ENTRI">
                                    </div>

                                </div>

                                <div class="div-right">

                                    <div style="margin-top: -15px;">
                                        <span class="fw-bold">PEMILIK SERTIFIKAT</span>
                                        <input class="form-control text-uppercase" type="text"
                                            value="{{ old('atas_nama') }}" name="atas_nama" id="atas_nama" placeholder="ENTRI">
                                    </div>
                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">LUAS TANAH (M2)</span>
                                        <input class="form-control text-uppercase" type="text"
                                            value="{{ old('luas') }}" name="luas" id="luas" placeholder="ENTRI">
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">LOKASI TANAH</span>
                                        <input class="form-control text-uppercase" type="text" name="lokasi"
                                            id="lokasi" value="{{ old('lokasi') }}" placeholder="ENTRI">
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
                    <h4 class="modal-title">AGUNAN LAINNYA</h4>
                </div>

                <form action="{{ route('lain.simpan') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="box-body">
                            <div class="row">

                                <input type="text" name="jenis_jaminan" value="Lainnya" hidden>

                                <div class="div-left">
                                    <div style="margin-top: -15px;">
                                        <span class="fw-bold">JENIS AGUNAN</span>
                                        <input type="text" value="{{ $data->auth }}" name="input_user" hidden>
                                        <input type="text" value="{{ $pengajuan->kode_pengajuan }}"
                                            name="pengajuan_kode" hidden>
                                        <select type="text" class="form-control jenis_agunan" style="width: 100%;"
                                            name="jenis_agunan_kode" required>
                                            <option value="" selected>--PILIH--</option>
                                            {{ $agunan }}
                                            @foreach ($agunan as $item)
                                                <option value="{{ $item->kode }}">{{ $item->jenis_agunan }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">JENIS DOKUMEN</span>
                                        <select type="text" class="form-control jenis_dokumen" style="width: 100%;"
                                            name="jenis_dokumen_kode" required>
                                            <option value="" selected>--PILIH--</option>
                                            @foreach ($dok as $item)
                                                <option value="{{ $item->kode }}">{{ $item->jenis_dokumen }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">NOMOR DOKUMEN</span>
                                        <input class="form-control text-uppercase" type="text" name="no_dokumen"
                                            id="no_dok" value="{{ old('no_dokumen') }}" placeholder="ENTRI" required>
                                    </div>

                                </div>

                                <div class="div-right">
                                    <div style="margin-top: -15px;">
                                        <span class="fw-bold">NAMA PEMILIK</span>
                                        <input class="form-control text-uppercase" type="text" name="atas_nama"
                                            id="atas_nama" value="{{ old('atas_nama') }}" placeholder="ENTRI" required>
                                    </div>
                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">LOKASI AGUNAN</span>
                                        <input class="form-control text-uppercase" type="text" name="lokasi"
                                            id="lokasi" value="{{ old('lokasi') }}" placeholder="ENTRI" required>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">CATATAN</span>
                                        <input class="form-control text-uppercase" type="text" name="catatan"
                                            id="catatan" value="{{ old('catatan') }}" placeholder="ENTRI">
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
        $("button[data-target='#modal-foto-kendaraan'], button[data-target='#modal-foto-tanah']").click(function() {
            // Mendapatkan nilai 'id' dari tombol yang diklik
            var dataId = $(this).data('id').split(',');

            var nilaiid = dataId[0];
            var atasNama = dataId[1];

            // Menyalin nilai 'id' ke elemen di dalam modal
            $('#nid').val(nilaiid);
            $('#ats_nama').val(atasNama);
        });

        $("button[data-target='#modal-foto-tanah']").click(function() {
            // Mendapatkan nilai 'id' dari tombol yang diklik
            var dataId = $(this).data('id').split(',');

            var nilaiid = dataId[0];
            var atasNama = dataId[1];

            // Menyalin nilai 'id' ke elemen di dalam modal
            $('#nidt').val(nilaiid);
            $('#ats_namat').val(atasNama);
        });

        $("button[data-target='#modal-foto-lain']").click(function() {
            // Mendapatkan nilai 'id' dari tombol yang diklik
            var dataId = $(this).data('id').split(',');

            var nilaiid = dataId[0];
            var atasNama = dataId[1];

            // Menyalin nilai 'id' ke elemen di dalam modal
            $('#nidl').val(nilaiid);
            $('#ats_namal').val(atasNama);
        });

        //Initialize Select2 Elements
        $('.jenis_agunan').select2()
        $('.jenis_dokumen').select2()
        $('.dati2').select2()


        var luas = document.getElementById("luas");
        if (luas) {
            luas.addEventListener("keyup", function(e) {
                luas.value = formatRupiah(this.value, "Rp. ");
            });
        }

        function formatRupiah(angka, prefix) {
            var number_string = angka.replace(/[^,\d]/g, "").toString(),
                split = number_string.split(","),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                separator = sisa ? "." : "";
                rupiah += separator + ribuan.join(".");
            }

            rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
            return prefix == undefined ? rupiah : rupiah ? rupiah : "";
        }
    </script>
@endpush
