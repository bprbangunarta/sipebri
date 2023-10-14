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
                            <h3 class="box-title">Permohonan Analisa</h3>
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
                                        <th class="text-center" style="width: 90px">AKSI</th>
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
                                                <b>Kaetegori:</b> RELOAN

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
                                                @if ($item->tracking == 'Proses Survei')
                                                    <a href="#" class="btn-circle btn-sm btn-grey"
                                                        title="Input Analisa"
                                                        style="pointer-events: none; text-decoration: none; cursor: default;">
                                                        <i class="fa fa-file-text-o" disabled="disabled"></i>
                                                    </a>
                                                @else
                                                    <a href="{{ route('perdagangan.in', ['pengajuan' => $item->kd_pengajuan]) }}"
                                                        class="btn-circle btn-sm btn-warning" title="Input Analisa">
                                                        <i class="fa fa-file-text-o"></i>
                                                    </a>
                                                @endif
                                                &nbsp;
                                                <a href="#" class="btn-circle btn-sm btn-primary"><i
                                                        class="fa fa-print"></i></a>
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
@endsection
