@extends('theme.app')
@section('title', 'Laporan Survey dan Analisa')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>LAPORAN

                <a href="#" class="btn btn-sm btn-success pull-right">
                    <i class="fa fa-download"></i>&nbsp; Export Data
                </a>
            </h1>
        </section>

        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border" style="border-bottom: 1px solid #3C8DBC;">
                            <h3 class="box-title">SURVEY DAN ANALISA</h3>

                            <div class="box-tools">
                                <form method="get" action="{{ route('laporan.survey') }}">
                                    <div class="input-group input-group-sm hidden-xs pull-right" style="width: 370px;">
                                        <input type="date" class="form-control pull-left" style="width: 150px;"
                                            name="tgl1" id="tgl1" value="" required>

                                        <input type="date" class="form-control pull-right" style="width: 150px;"
                                            name="tgl2" id="tgl2" value="">

                                        <div class="input-group-btn">
                                            <button type="submit" class="btn btn-primary" style="margin-right: 3px;"><i
                                                    class="fa fa-filter"></i></button>
                                            <a href="{{ route('laporan.survey') }}" class="btn btn-primary"
                                                title="Refresh"><i class="fa fa-refresh" aria-hidden="true"></i></a>
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
                                        <th class="text-center">NAMA LENGKAP</th>
                                        <th class="text-center" width="45%">ALAMAT</th>
                                        <th class="text-center" width="7%">PLAFON</th>
                                        <th class="text-center" width="10%">STATUS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $no = 1;
                                    @endphp
                                    @forelse ($data as $item)
                                        <tr class="text-uppercase">
                                            <td class="text-center">{{ $no }}</td>
                                            <td class="text-center">
                                                {{ \Carbon\Carbon::parse($item->created_at)->format('Y-m-d') }}</td>
                                            <td class="text-center">{{ $item->kode_pengajuan }}</td>
                                            <td>{{ $item->nama_nasabah }}</td>
                                            <td>{{ $item->alamat_ktp }}</td>
                                            <td class="text-right">{{ number_format($item->plafon, 0, ',', '.') }}</td>
                                            <td class="text-center">{{ $item->tracking }}</td>
                                        </tr>
                                        @php
                                            $no++;
                                        @endphp
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
