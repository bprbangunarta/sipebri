@extends('theme.app')
@section('title', 'Fasilitas Kredit')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>LAPORAN</h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard') }}"><i class="fa fa-laptop"></i> Dashboard</a></li>
                <li>Laporan</li>
                <li class="active">Fasilitas</li>
            </ol>
        </section>

        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border" style="border-bottom: 1px solid #3C8DBC;">
                            <h3 class="box-title">FASILITAS KREDIT</h3>

                            <div class="box-tools">
                                <form action="{{ route('laporan.fasilitas') }}" method="GET">
                                    <div class="input-group input-group-sm hidden-xs" style="width: 170px;">
                                        <input type="text" class="form-control text-uppercase pull-right" name="name" id="name" value="{{ request('name') }}" placeholder="Nama/ Kode/ Wilayah">

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
                                        <th class="text-center" width="7%">NO. LOAN</th>
                                        <th class="text-center" width="7%">NO. SPK</th>
                                        <th class="text-center">NAMA LENGKAP</th>
                                        <th class="text-center" width="41%">ALAMAT</th>
                                        <th class="text-center" width="5%">WIL</th>
                                        <th class="text-center" width="8%">PLAFON</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $no = 1;
                                    @endphp
                                    @foreach ($data as $item)
                                        <tr class="text-uppercase">
                                            <td class="text-center">{{ $loop->iteration + $data->firstItem() - 1 }}</td>
                                            <td class="text-center">
                                                {{ \Carbon\Carbon::parse($item->akad_kredit)->format('Y-m-d') }}</td>
                                            <td class="text-center">
                                                @if (is_null($item->no_loan))
                                                    {{ $item->kode_pengajuan }}
                                                @else
                                                    {{ $item->no_loan }} 
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $item->no_spk }}</td>
                                            <td>{{ $item->nama_nasabah }}</td>
                                            <td>{{ $item->alamat_ktp }}</td>
                                            <td class="text-center">{{ $item->kantor_kode }}</td>
                                            <td class="text-right">{{ number_format($item->plafon, 0, ',', '.') }}</td>
                                        </tr>
                                        @php
                                            $no++;
                                        @endphp
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="box-footer clearfix">
                            <button data-toggle="modal" data-target="#modal-export" class="btn btn-success btn-sm pull-left"><i class="fa fa-download"></i>&nbsp; Export Data</button>

                            {{ $data->withQueryString()->links('vendor.pagination.adminlte') }}
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
