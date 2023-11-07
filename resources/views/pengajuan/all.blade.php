@extends('theme.app')
@section('title', 'Data Pengajuan')
@yield('jquery')
@section('content')
<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <i class="fa fa-user"></i>
                        <h3 class="box-title">DATA PENGAJUAN KREDIT</h3>

                        <div class="box-tools">
                            <form {{ route('user.index') }}" method="GET">
                                <div class="input-group input-group-sm hidden-xs" style="width: 170px;">
                                    <input type="text" class="form-control pull-right" name="name" id="name" value="{{ request('name') }}" placeholder="Search">

                                    <div class="input-group-btn">
                                        <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="box-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="bg-blue">
                                    <th class="text-center" width="3%">NO</th>
                                    <th class="text-center">INFORMASI NASABAH</th>
                                    <th class="text-center" width="40%">ALAMAT</th>
                                    <th class="text-center" width="17%">PENGAJUAN</th>
                                    <th class="text-center" width="10%">STATUS</th>
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

                                    <td style="vertical-align: middle;">
                                        <b>KODE :</b> {{ $item->kode }} [ {{ $item->kategori }} ] <br>
                                        <b>NAMA :</b> {{ strtoupper($item->nama) }} <br>
                                        <b>TANGGAL :</b> {{ \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') }}
                                    </td>

                                    @if (is_null($item->alamat))
                                    <td class="text-center" style="vertical-align: middle;">-</td>
                                    @else
                                    <td class="text-uppercase" style="vertical-align: middle;">{{ $item->alamat }} <br>
                                        <b>Desa: </b>{{ $item->kelurahan }} | <b>Kecamatan:
                                        </b>{{ $item->kecamatan }}
                                    </td>
                                    @endif

                                    @php
                                    $item->plafon = number_format($item->plafon, 0, ',', '.');
                                    @endphp
                                    <td style="vertical-align: middle;">
                                        <b>KANTOR :</b> {{ $item->kantor }} <br>
                                        <b>{{ $item->produk_kode }} - JK :</b> {{ $item->jk }} BULAN <br>
                                        <b>PLAFON :</b> {{ $item->plafon }}
                                    </td>

                                    <td class="text-center" style="vertical-align: middle;">
                                        @if ($item->status == 'Lengkapi Data' || $item->status == 'Minta Otorisasi')
                                        <span class="btn bg-blue" style="width: 120px;hight:100%;">Verifikasi
                                            Data</span>
                                        @elseif ($item->status == 'Batal' || $item->status == 'Dibatalkan' || $item->status == 'Ditolak')
                                        <span class="btn bg-red" style="width: 120px;hight:100%;">{{ $item->status }}</span>
                                        @elseif ($item->status == 'Sudah Otorisasi')
                                        <span class="btn bg-yellow" style="width: 120px;hight:100%;">Survey &
                                            Analisa</span>
                                        @else
                                        <span class="btn bg-green" style="width: 120px;hight:100%;">{{ $item->status }}</span>
                                        @endif

                                        <p style="margin-top:-5px;"></p>
                                        <a href="/tracking/pengajuan">
                                            <span class="btn bg-purple" style="width: 120px;hight:100%;">Lihat Tracking</span>
                                        </a>
                                    </td>
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

                    <div class="box-footer clearfix">
                        {{ $data->links('vendor.pagination.adminlte') }}
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>
@endsection