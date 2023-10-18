@extends('theme.app')
@section('title', 'Permohonan Analisa')

@section('content')
    <div class="content-wrapper">

        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-file-text-o"></i>
                            <h3 class="box-title">PERMOHONAN ANALISA</h3>
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="bg-blue">
                                        <th class="text-center" style="width: 10px">#</th>
                                        <th class="text-center" style="width: 150px">PENGAJUAN</th>
                                        <th class="text-center" style="width: 200px">NASABAH</th>
                                        <th class="text-center">ALAMAT</th>
                                        <th class="text-center" style="width: 100px">WILAYAH</th>
                                        <th class="text-center">STATUS</th>
                                        <th class="text-center" style="width: 130px">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $no = 1;
                                    @endphp
                                    @forelse ($data as $item)
                                        <tr>
                                            <td class="text-center" style="vertical-align: middle;">{{ $no }}</td>
                                            <td style="vertical-align: middle;">
                                                <b>KODE: </b>{{ $item->kode_nasabah }} <br>
                                                <b>TANGGAL</b> : {{ $item->tgl_survei }}
                                            </td>
                                            <td style="text-transform: uppercase;vertical-align: middle;">
                                                {{ $item->nama_nasabah }} <br>
                                                <b>Kategori:</b> {{ $item->kategori }}

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
                                                @if ($item->tracking == 'Proses Analisa')
                                                    <a href="{{ route('perdagangan.in', ['pengajuan' => $item->kd_pengajuan]) }}"
                                                        class="btn-circle btn-sm btn-warning" title="Input Analisa">
                                                        <i class="fa fa-file-text-o"></i>
                                                    </a>
                                                @else
                                                    <a data-toggle="modal" data-target="#jadwal-ulang"
                                                        class="btn-circle btn-sm btn-danger" title="Reschedule">
                                                        <i class="fa fa-history"></i>
                                                    </a>

                                                    &nbsp;
                                                    @if ($item->tracking == 'Proses Survei')
                                                        <a href="#" class="btn-circle btn-sm btn-grey"
                                                            title="Input Analisa"
                                                            style="pointer-events: none; text-decoration: none; cursor: default;">
                                                            <i class="fa fa-file-text-o" disabled="disabled"></i>
                                                        </a>
                                                    @endif

                                                    &nbsp;
                                                    <a href="#" class="btn-circle btn-sm btn-primary"
                                                        title="Cetak Analisa">
                                                        <i class="fa fa-print"></i>
                                                    </a>
                                            </td>
                                        </tr>
                                        @php
                                            $no++;
                                        @endphp
                                    @empty
                                        <tr>
                                            <td class="text-center" colspan="7">Tidak ada permohonan analisa.</td>
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

    <div class="modal fade" id="jadwal-ulang">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-red">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">PERMINTAAN RESCHEDULE</h4>
                </div>
                <form action="#" method="POST">
                    @csrf
                    <div class="modal-body">

                        <div class="box-body">
                            <div class="row">

                                <div style="margin-top: -15px;">
                                    <span class="fw-bold">KODE PENGAJUAN</span>
                                    <input type="text" id="id" name="id" hidden>
                                    <input class="form-control text-uppercase" type="text" value="123456789S" readonly>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">NAMA NASABAH</span>
                                    <input class="form-control text-uppercase" type="text" value="ZULFADLI RIZAL"
                                        readonly>
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
                        <button type="submit" class="btn btn-danger">SIMPAN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
