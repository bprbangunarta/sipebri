@extends('theme.app')
@section('title', 'Penolakan Penolakan')

@section('content')
    <div class="content-wrapper">

        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-file-text-o"></i>
                            <h3 class="box-title">DATA PENOLAKAN</h3>
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="bg-blue">
                                        <th class="text-center" width="3%">NO</th>
                                        <th class="text-center">INFORMASI NASABAH</th>
                                        <th class="text-center" width="40%">ALAMAT</th>
                                        <th class="text-center" width="17%">PENGAJUAN</th>
                                        <th class="text-center" width="10%">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($data as $item)
                                        <tr>
                                            <td class="text-center" style="vertical-align: middle;">1</td>

                                            <td style="vertical-align: middle;">
                                                <b>KODE :</b> {{ $item->kode_pengajuan }} [ {{ $item->kategori }} ] <br>
                                                <b>NAMA :</b> {{ strtoupper($item->nama_nasabah) }} <br>
                                                <b>TANGGAL :</b>
                                                {{ \Carbon\Carbon::parse($item->tgl_daftar)->format('Y-m-d') }}
                                            </td>

                                            <td style="text-transform: uppercase;">
                                                {{ $item->alamat_ktp }} <br>
                                                <b>Desa: </b>{{ $item->kelurahan }} | <b>Kecamatan:
                                                </b>{{ $item->kecamatan }}
                                            </td>

                                            @php
                                            $item->plafon = number_format($item->plafon, 0, ',', '.');
                                            @endphp
                                            <td style="vertical-align: middle;">
                                                <b>KANTOR :</b> {{ $item->kantor_kode }} <br>
                                                <b>{{ $item->produk_kode }} - JK :</b> {{ $item->jk }} BULAN <br>
                                                <b>PLAFON :</b> {{ $item->plafon }}
                                            </td>

                                            <td class="text-center" style="vertical-align: middle;">
                                                <a href="{{ route('penolakan.edit', ['pengajuan' => $item->kd_pengajuan]) }}">
                                                    <span class="btn bg-red" style="width: 120px;hight:100%;">Input Penolakan</span>
                                                </a>

                                                <p style="margin-top:-5px;"></p>
                                                <a data-toggle="modal" data-target="#jadwal-ulang" data-pengajuan="{{ $item->kode_pengajuan }}" title="Jadwal Ulang">
                                                    <span class="btn bg-blue" style="width: 120px;hight:100%;">Lihat Analisa</span>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-center" colspan="7">TIDAK ADA DATA</td>
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
@endsection
