@extends('theme.app')
@section('title', 'Dashboard')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Dashboard
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-laptop"></i> Dashboard</a></li>
                <li class="active">Index</li>
            </ol>
        </section>

        <section class="content">
            <div class="row">
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-blue">
                        <span class="info-box-icon"><i class="fa fa-users"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">PENGAJUAN</span>
                            <span class="info-box-number">{{ $pengajuan }} USER</span>

                            <div class="progress">
                                <div class="progress-bar" style="width: 100%"></div>
                            </div>
                            <span class="progress-description">
                                <a href="{{ route('laporan.pendaftaran') }}" style="color:white;">
                                    SELENGKAPNYA
                                </a>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-orange">
                        <span class="info-box-icon"><i class="fa fa-hourglass-start"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">SURVEI & ANALISA</span>
                            <span class="info-box-number">{{ $survei }} USER</span>

                            <div class="progress">
                                <div class="progress-bar" style="width: 100%"></div>
                            </div>
                            <span class="progress-description">
                                <a href="{{ route('laporan.survey') }}" style="color:white;">
                                    SELENGKAPNYA
                                </a>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-purple">
                        <span class="info-box-icon"><i class="fa fa-bullhorn"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">SIAP REALISASI</span>
                            <span class="info-box-number">{{ $siap_realisasi }} USER</span>

                            <div class="progress">
                                <div class="progress-bar" style="width: 100%"></div>
                            </div>
                            <span class="progress-description">
                                <a href="{{ route('laporan.siap-realisasi') }}" style="color:white;">
                                    SELENGKAPNYA
                                </a>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-green">
                        <span class="info-box-icon"><i class="fa fa-bank"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">REALISASI</span>
                            <span class="info-box-number">{{ $disetujui }} USER</span>

                            <div class="progress">
                                <div class="progress-bar" style="width: 100%"></div>
                            </div>
                            <span class="progress-description">
                                <a href="{{ route('laporan.fasilitas') }}" style="color:white;">
                                    SELENGKAPNYA
                                </a>
                            </span>
                        </div>
                    </div>
                </div>

                {{-- <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-red">
                        <span class="info-box-icon"><i class="fa fa-close"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">PENOLAKAN</span>
                            <span class="info-box-number">{{ $penolakan }} USER</span>

                            <div class="progress">
                                <div class="progress-bar" style="width: 100%"></div>
                            </div>
                            <span class="progress-description">
                                <a href="#" style="color:white;">
                                    SELENGKAPNYA
                                </a>
                            </span>
                        </div>
                    </div>
                </div> --}}
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-calendar"></i>
                            <h3 class="box-title">REALISASI HARI INI</h3>

                            <div class="box-tools">
                                <form action="{{ route('dashboard') }}" method="GET">
                                    <div class="input-group input-group-sm hidden-xs" style="width: 305px;">
                                        <input type="text" class="form-control text-uppercase pull-right" style="width: 170px;" name="keyword" id="keyword" value="{{ request('keyword') }}" placeholder="Nama/ Kode/ Wilayah">

                                        <div class="input-group-btn">
                                            <button type="submit" class="btn bg-blue">
                                                <i class="fa fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="box-body" style="overflow: auto;white-space: nowrap;width: 100%;">
                            <table class="table table-bordered text-uppercase" style="font-size: 12px;">
                                <thead>
                                    <tr class="bg-blue">
                                        <th class="text-center" width="3%">#</th>
                                        <th class="text-center">KODE</th>
                                        <th class="text-center">NO. SPK</th>
                                        <th class="text-center">NAMA DEBITUR</th>
                                        <th class="text-center">ALAMAT</th>
                                        <th class="text-center">WILAYAH</th>
                                        <th class="text-center">PLAFON</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $no = 1;
                                    @endphp
                                    @forelse ($data as $item)
                                        <tr class="bg-info">
                                            <td class="text-center" style="vertical-align: middle;">
                                                {{ $loop->iteration + $data->firstItem() - 1 }}
                                            </td>
                                            <td class="text-center">{{ $item->kode_pengajuan }}</td>
                                            <td class="text-center">{{ $item->no_spk }}</td>
                                            <td style="vertical-align: middle;">{{ $item->nama_nasabah }}</td>
                                            <td>{{ $item->alamat }}</td>
                                            <td class="text-center" style="vertical-align: middle;">{{ $item->nama_kantor }}
                                            </td>

                                            @php
                                                $item->plafon = number_format($item->plafon, 0, ',', '.');
                                            @endphp
                                            <td class="text-right" style="vertical-align: middle;">{{ $item->plafon }}</td>
                                        </tr>
                                        @php
                                            $no++;
                                        @endphp

                                    @empty
                                        <tr>
                                            <td class="text-center" colspan="7">TIDAK ADA DATA</td>
                                        </tr>
                                    @endforelse

                                    <tr class="bg-blue">
                                        <td class="text-center" colspan="6"><b>TOTAL</b></td>
                                        <td class="text-right"><b>{{ number_format($total, 0, ',', '.') }}</b>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="box-footer clearfix">
                            <div class="pull-left hidden-xs">
                                <button class="btn btn-default btn-sm">
                                    Showing {{ $data->firstItem() }} to {{ $data->lastItem() }} of {{ $data->total() }} entries
                                </button>
                            </div>

                            {{ $data->withQueryString()->onEachSide(0)->links('vendor.pagination.adminlte') }}
                        </div>
                        
                    </div>
                </div>
            </div>
        </section>

    </div>
@endsection
