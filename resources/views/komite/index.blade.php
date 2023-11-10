@extends('theme.app')
@section('title', 'Persetujuan Komite')

@section('content')
    <div class="content-wrapper">

        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-file-text-o"></i>
                            <h3 class="box-title">PERSETUJUAN KOMITE</h3>
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="bg-blue">
                                        <th class="text-center" width="3%">NO</th>
                                        <th class="text-center">NASABAH</th>
                                        <th class="text-center" width="30%">ALAMAT</th>
                                        <th class="text-center" width="17%">PENGAJUAN</th>
                                        <th class="text-center">KOMITE</th>
                                        <th class="text-center" width="10%">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($data as $item)
                                        <tr>
                                            <td class="text-center" style="vertical-align: middle;">1</td>

                                            <td style="vertical-align: middle;">
                                                <b>KODE :</b> {{ $item->kode_pengajuan }} [ {{ $item->kategori }} ] <br>
                                                <b>NAMA :</b> {{ $item->nama_nasabah }} <br>
                                                <b>TANGGAL :</b> {{ date('Y-m-d', strtotime($item->created_at)) }}
                                            </td>

                                            <td class="text-uppercase" style="vertical-align: middle;">
                                                {{ $item->alamat_ktp }} <br>
                                                <b>Desa: </b>{{ $item->kelurahan }} | <b>Kecamatan:
                                                </b>{{ $item->kecamatan }}
                                            </td>

                                            <td style="vertical-align: middle;">
                                                <b>KANTOR :</b> {{ $item->kode_kantor }} <br>
                                                <b>KRU - JK :</b> {{ $item->produk_kode }} - {{ $item->jk }} Bulan
                                                <br>
                                                <b>METODE :</b> {{ $item->metode_rps }} <br>
                                                <b>PLAFON :</b>
                                                {{ 'Rp. ' . ' ' . number_format($item->plafon, 0, ',', '.') }}
                                            </td>

                                            <td style="vertical-align: middle;">
                                                <b>USULAN K1 :</b>
                                                {{ 'Rp. ' . ' ' . number_format($item->usulan1, 0, ',', '.') }} <br>
                                                <b>USULAN K2 :</b>
                                                {{ 'Rp. ' . ' ' . number_format($item->usulan2, 0, ',', '.') }} <br>
                                                <b>USULAN K3 :</b>
                                                {{ 'Rp. ' . ' ' . number_format($item->usulan3, 0, ',', '.') }} <br>
                                                <b>USULAN K4 :</b>
                                                {{ 'Rp. ' . ' ' . number_format($item->usulan4, 0, ',', '.') }}
                                            </td>

                                            <td class="text-center" style="vertical-align: middle;">
                                                <a data-toggle="modal" data-target="#modal-persetujuan"
                                                    data-pengajuan="{{ $item->kode_pengajuan }}">
                                                    <span class="btn bg-yellow"
                                                        style="width: 120px;hight:100%;">Persetujuan</span>
                                                </a>

                                                <p style="margin-top:-5px;"></p>
                                                <a data-toggle="modal" data-target="#modal-catatan"
                                                    data-pengajuan="{{ $item->kode_pengajuan }}">
                                                    <span class="btn bg-blue" style="width: 120px;">Lihat Catatan</span>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-center" colspan="10">TIDAK ADA DATA</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>

    <div class="modal fade" id="modal-catatan">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-blue">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">CATATAN KOMITE</h4>
                </div>
                <form action="{{ route('komite.simpan') }}" method="POST">
                    @csrf
                    <div class="modal-body">

                        <div class="box-body">
                            <div class="row">

                                <div style="margin-top: -15px;">
                                    <span class="fw-bold">STAFF ANALIS</span>
                                    <textarea class="form-control text-uppercase" rows="3" name="" id="staff_analis" readonly>Catatan dari staff analis</textarea>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">KASI ANALIS</span>
                                    <textarea class="form-control text-uppercase" rows="3" name="" id="kasi_analiss" readonly>Catatan dari kasi analis</textarea>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">KABAG ANALIS</span>
                                    <textarea class="form-control text-uppercase" rows="3" name="" id="kabag_analis" readonly>Catatan dari kabag analis</textarea>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">DIREKSI</span>
                                    <textarea class="form-control text-uppercase" rows="3" name="" id="direksi" readonly>Catatan dari direksi</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-persetujuan">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-yellow">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">PERSETUJUAN KOMITE</h4>
                </div>
                <form action="{{ route('komite.simpan') }}" method="POST">
                    @csrf
                    <div class="modal-body">

                        <div class="box-body">
                            <div class="row">

                                <div class="div-left">
                                    <div style="margin-top: -20px;">
                                        <span class="fw-bold">MAX PLAFON</span>
                                        <input type="text" name="kode_pengajuan" id="pengajuan" hidden>
                                        <input class="form-control text-uppercase" type="text" name="max_plafon"
                                            value="" id="max" readonly>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">BIAYA PROVISI (%)</span>
                                        <input class="form-control text-uppercase" type="text" name="b_provisi"
                                            value="" id="provisi">
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">SUKU BUNGA (%)</span>
                                        <input class="form-control text-uppercase" type="text" name="suku_bunga"
                                            id="bunga" required>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">KEPUTUSAN KOMITE</span>
                                        <select class="form-control text-uppercase" style="width:100%;"
                                            name="putusan_komite" id="komite" required>

                                        </select>
                                    </div>
                                </div>

                                <div class="div-right">
                                    <div style="margin-top: -20px;">
                                        <span class="fw-bold">METODE RPS</span>
                                        <select class="form-control" name="metode_rps" id="metode">
                                            <option value=""></option>
                                        </select>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">BIAYA ADMIN (%)</span>
                                        <input class="form-control text-uppercase" type="text" name="b_admin"
                                            value="" id="admin">
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">RC</span>
                                        <input class="form-control text-uppercase" type="text" name="rc"
                                            id="rc" readonly required>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">USULAN PLAFON</span>
                                        <input class="form-control text-uppercase" type="text" name="usulan_plafon"
                                            placeholder="RP." id="usulan_plafon" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">CATATAN KOMITE</span>
                                    <textarea class="form-control text-uppercase" rows="3" name="catatan" id="catatan" required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="margin-top: -10px;">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">BATAL</button>
                        <button type="submit" class="btn bg-yellow">SIMPAN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('myscript')
    <script src="{{ asset('assets/js/myscript/persetujuan_komite.js') }}"></script>
    <script src="{{ asset('assets/js/myscript/catatan_komite.js') }}"></script>
@endpush
