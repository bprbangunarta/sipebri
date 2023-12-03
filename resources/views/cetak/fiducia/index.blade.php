@extends('theme.app')
@section('title', 'Surat Penjaminan Fiducia')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border" style="border-bottom: 1px solid #3C8DBC;">
                            <h3 class="box-title">CETAK PENJAMINAN FIDUCIA</h3>

                            <div class="box-tools">
                                <form action="{{ route('fiducia') }}" method="GET">
                                    <div class="input-group input-group-sm hidden-xs" style="width: 305px;">
                                        <input type="text" class="form-control text-uppercase pull-right"
                                            style="width: 170px;" name="keyword" id="keyword"
                                            value="{{ request('keyword') }}" placeholder="Nama/ Kode/ Wilayah">

                                        <div class="input-group-btn">
                                            <button type="submit" class="btn bg-blue">
                                                <i class="fa fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="box-body">
                            <table class="table table-bordered text-uppercase" style="font-size: 12px;">
                                <thead>
                                    <tr class="bg-blue">
                                        <th class="text-center" width="3%">#</th>
                                        <th class="text-center" width="7%">TANGGAL</th>
                                        <th class="text-center" width="7%">KODE</th>
                                        <th class="text-center" width="7%">NO. SPK</th>
                                        <th class="text-center">NAMA NASABAH</th>
                                        <th class="text-center" width="5%">WIL</th>
                                        <th class="text-center" width="16%">JENIS KENDARAAN</th>
                                        <th class="text-center" width="8%">MEREK</th>
                                        <th class="text-center" width="8%">NO. POLISI</th>
                                        <th class="text-center" width="8%">TAKSASI</th>
                                        <th class="text-center" width="10%">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $no = 1;
                                    @endphp
                                    @forelse ($data as $item)
                                        <tr>
                                            <td class="text-center" style="vertical-align: middle;">
                                                {{ $loop->iteration + $data->firstItem() - 1 }}
                                            </td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                {{ \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') }}
                                            </td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                {{ $item->kode_pengajuan }}</td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                @if (is_null($item->no_spk))
                                                    <span class="label label-danger" style="font-size: 10px;">
                                                        BELUM DITURUNKAN
                                                    </span>
                                                @else
                                                    {{ $item->no_spk }}
                                                @endif
                                            </td>
                                            <td style="vertical-align: middle;">{{ $item->nama_nasabah }}</td>
                                            <td class="text-center" style="vertical-align: middle;">{{ $item->kantor_kode }}
                                            </td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                {{ $item->jenis_kendaraan }}</td>
                                            <td class="text-center" style="vertical-align: middle;">{{ $item->merek }}</td>
                                            <td class="text-center" style="vertical-align: middle;">{{ $item->no_polisi }}
                                            </td>
                                            <td class="text-right" style="vertical-align: middle;">
                                                {{ number_format($item->nilai_taksasi, 0, ',', '.') }}
                                            </td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                <a data-toggle="modal" data-target="#modal-info"
                                                    class="btn-circle btn-sm bg-yellow">
                                                    <i class="fa fa-eye"></i>
                                                </a>

                                                &nbsp;
                                                <a href="{{ route('cetak.fiducia', ['pengajuan' => $item->kd_pengajuan]) }}"
                                                    target="_blank" class="btn-circle btn-sm bg-blue">
                                                    <i class="fa fa-print"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @php
                                            $no++;
                                        @endphp
                                    @empty
                                        <tr>
                                            <td class="text-center" colspan="10">TIDAK ADA DATA</td>
                                        </tr>
                                </tbody>
                                @endforelse
                            </table>
                        </div>

                        <div class="box-footer clearfix">
                            <div class="pull-left">
                                <button class="btn btn-default btn-sm">
                                    Showing {{ $data->firstItem() }} to {{ $data->lastItem() }} of {{ $data->total() }}
                                    entries
                                </button>
                            </div>

                            {{ $data->withQueryString()->links('vendor.pagination.adminlte') }}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="modal fade" id="modal-info">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-yellow">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">INFORMASI KENDARAAN</h4>
                </div>
                <form action="#" method="POST">
                    @csrf
                    <div class="modal-body">

                        <div class="box-body">
                            <div class="row">
                                <div class="div-left">
                                    <div style="margin-top: -15px;">
                                        <span class="fw-bold">JENIS AGUNAN</span>
                                        <input type="text" id="id" name="id" hidden>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            value="Kendaraan Bermotor Roda 2" id="jenis">
                                    </div>
                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">JENIS DOKUMEN</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            value="BPKB Motor Non Fiducia" id="dokumen">
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">NOMOR BPKB</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            value="P007772168" id="no_dok">
                                    </div>
                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">PEMILIK KENDARAAN</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            value="ZULFADLI RIZAL" id="nama">
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">NOMOR MESIN</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            value="458131564616" id="no_mesin">
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">NOMOR POLISI</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            value="T 4414 YB" id="no_polisi">
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">NOMOR RANGKA</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            value="BDAS594168" id="no_rangka">
                                    </div>
                                </div>

                                <div class="div-right">
                                    <div style="margin-top: -15px;">
                                        <span class="fw-bold">TIPE KENDARAAN</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            value="Motor Metik" id="tipe">
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">MEREK KENDARAAN</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            value="Honda" id="merk">
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">TAHUN KENDARAAN</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            value="2020" id="tahun_kendaraan">
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">WARNA KENDARAAN</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            value="Hitam" id="warna">
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">LOKASI KENDARAAN</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            value="Jl. H. Iksan No.89, Pamanukan, Kec. Pamanukan, Kabupaten Subang, Jawa Barat"
                                            id="lok">
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">NILAI PASAR</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            name="nilai_pasar" id="nilai_pasar" placeholder="Rp.">
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">NILAI TAKSASI</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            name="nilai_taksasi" id="nilai_taksasi" placeholder="Rp.">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="margin-top: -10px;">
                        <button type="button" class="btn btn-warning" style="width:100%;"
                            data-dismiss="modal">TUTUP</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
