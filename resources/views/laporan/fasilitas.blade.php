@extends('theme.app')
@section('title', 'Fasilitas Kredit')

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
                            <h3 class="box-title">FASILITAS KREDIT</h3>

                            <div class="box-tools">
                                <form action="#" method="GET">
                                    <div class="input-group input-group-sm hidden-xs" style="width: 170px;">
                                        <input type="text" class="form-control pull-right" name="name" id="name"
                                            value="" placeholder="Search">

                                        <div class="input-group-btn">
                                            <button type="submit" class="btn btn-default"><i
                                                    class="fa fa-search"></i></button>
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
                                        <th class="text-center" width="7%">STATUS</th>
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
                                            <td class="text-right">{{ 'Rp. ' . ' ' . $item->plafon }}</td>
                                            <td class="text-center">{{ $item->status }}</td>
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
