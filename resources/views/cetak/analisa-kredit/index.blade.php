@section('title', 'Cetak Analisa Kredit')
@extends('theme.app')

@section('content')
    <div class="content-wrapper">

        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-print"></i>
                            <h3 class="box-title">CETAK ANALISA KREDIT</h3>

                            <div class="box-tools">
                                <form action="/themes/cetak/analisa/kredit" method="GET">
                                    <div class="input-group input-group-sm hidden-xs" style="width: 170px;">
                                        <input type="text" class="form-control pull-right" name="name" id="name"
                                            value="{{ request('name') }}" placeholder="Search">

                                        <div class="input-group-btn">
                                            <button type="submit" class="btn btn-default"><i
                                                    class="fa fa-search"></i></button>
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
                                        <th class="text-center">NASABAH</th>
                                        <th class="text-center" width="30%">ALAMAT</th>
                                        <th class="text-center" width="17%">PENGAJUAN</th>
                                        <th class="text-center">KOMITE</th>
                                        <th class="text-center" width="10%">AKSI</th>
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
                                                <b>KODE :</b> {{ $item->kode_pengajuan }} [ {{ $item->kategori }} ] <br>
                                                <b>NAMA :</b> {{ $item->nama_nasabah }} <br>
                                                <b>TANGGAL :</b>
                                                {{ \Carbon\Carbon::parse($item->created_at)->format('Y-m-d') }}
                                            </td>

                                            @if (is_null($item->alamat_ktp))
                                                <td class="text-center" style="vertical-align: middle;">-</td>
                                            @else
                                                <td class="text-uppercase" style="vertical-align: middle;">
                                                    {{ $item->alamat_ktp }} <br>
                                                    <b>Desa: </b>{{ $item->kelurahan }} | <b>Kecamatan:
                                                    </b>{{ $item->kecamatan }}
                                                </td>
                                            @endif

                                            <td style="vertical-align: middle;">
                                                <b>KANTOR :</b> {{ $item->kode_kantor }} <br>
                                                <b>{{ $item->produk_kode }} - JK :</b> {{ $item->jangka_waktu }} BULAN -
                                                {{ $item->suku_bunga }}% <br>
                                                <b>METODE :</b> {{ $item->metode_rps }}<br>
                                                <b>PLAFON :</b>
                                                {{ 'Rp.' . ' ' . number_format($item->plafon, 0, ',', '.') }}
                                            </td>

                                            <td style="vertical-align: middle;">
                                                <b>USULAN K1 :</b>
                                                {{ 'Rp. ' . ' ' . number_format($item->usulan1, 0, ',', '.') }} <br>
                                                <b>USULAN K2 :</b>
                                                {{ 'Rp. ' . ' ' . number_format($item->usulan2, 0, ',', '.') }} <br>
                                                <b>USULAN K3 :</b>
                                                {{ 'Rp. ' . ' ' . number_format($item->usulan3, 0, ',', '.') }} <br>
                                                <b>USULAN K4 :</b>
                                                {{ 'Rp. ' . ' ' . number_format($item->usulan4, 0, ',', '.') }}
                                            </td>

                                            <td class="text-center" style="vertical-align: middle;">
                                                <a href="{{ route('cetak.analisa_kredit', ['pengajuan' => $item->kd_pengajuan]) }}"
                                                    target="_blank">
                                                    <span class="btn bg-blue" style="width: 120px;">Cetak Berkas</span>
                                                </a>

                                            </td>
                                            @php
                                                $no++;
                                            @endphp

                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-center" colspan="10">TIDAK ADA DATA</td>
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

@push('myscript')
@endpush
