@extends('theme.app')
@section('title', 'Putusan Komite')

@section('content')
    <div class="content-wrapper">

        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-file-text-o"></i>
                            <h3 class="box-title">PUTUSAN KOMITE</h3>
                        </div>
                        <div class="box-body">
                            @forelse ($data as $item)
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
                                                <b>KODE: </b>{{ $item->kode_pengajuan }} <br>
                                                <b>TANGGAL</b> : {{ substr($item->created_at, 0, 10) }}
                                            </td>
                                            <td style="text-transform: uppercase;vertical-align: middle;">
                                                {{ $item->nama_nasabah }} <br>
                                                <b>Kaetegori:</b> {{ $item->kategori }}
                                            </td>

                                            <td style="text-transform: uppercase;">
                                                {{ $item->alamat_ktp }} <br>
                                                <b>Desa: </b>{{ $item->kelurahan }} | <b>Kecamatan:
                                                </b>{{ $item->kecamatan }}
                                            </td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                {{ $item->nama_kantor }}
                                            </td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                <span class="label label-warning">{{ $item->tracking }}</span>
                                            </td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                <a data-toggle="modal" data-target="#modal-edit"
                                                    class="btn-circle btn-sm btn-warning"
                                                    data-pengajuan="{{ $item->kode_pengajuan }}">
                                                    <i class="fa fa-file-text-o"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-center" colspan="7">Tidak ada permohonan Persetujuan Komite.
                                            </td>
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

    <div class="modal fade" id="modal-edit">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-yellow">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">PUTUSAN KOMITE</h4>
                </div>
                <form action="{{ route('komite.simpan') }}" method="POST">
                    @csrf
                    <div class="modal-body">

                        <div class="box-body">
                            <div class="row">

                                <div style="margin-top: -15px;">
                                    <span class="fw-bold">KODE PENGAJUAN</span>
                                    <input type="text" name="id" hidden>
                                    <input class="form-control text-uppercase" type="text" value="123456789S"
                                        id="kode" readonly>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">NAMA NASABAH</span>
                                    <input class="form-control text-uppercase" type="text" value="ZULFADLI RIZAL"
                                        id="nama" readonly>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">USULAN PLAFOND</span>
                                    <input class="form-control text-uppercase" type="text" value="RP. 20.000.000"
                                        id="plafon" readonly>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">PUTUSAN KOMITE</span>
                                    <select class="form-control text-uppercase" style="width:100%;" name="putusan_komite"
                                        id="komite" required>
                                        <option value="">--Pilih--</option>
                                        <option value="Disetujui">Disetujui</option>
                                        <option value="Ditolak">Ditolak</option>
                                        <option value="Dibatalkan">Dibatalkan</option>
                                        <option value="Naik Kasi">Naik Kasi</option>
                                        <option value="Proses Analisa">Proses Analisa</option>
                                    </select>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">KETERANGAN</span>
                                    <textarea class="form-control text-uppercase" name="" id=""></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="margin-top: -10px;">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">BATAL</button>
                        <button type="submit" class="btn btn-warning">SIMPAN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('myscript')
    <script src="{{ asset('assets/js/myscript/persetujuan_komite.js') }}"></script>
@endpush
