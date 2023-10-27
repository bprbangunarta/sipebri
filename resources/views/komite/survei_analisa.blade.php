@extends('theme.app')
@section('title', 'Putusan Komite')

@section('content')
    <div class="content-wrapper">

        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-file-text-o"></i>
                            <h3 class="box-title">SURVEI DAN ANALISA</h3>
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="bg-blue">
                                        <th class="text-center" style="width: 10px">#</th>
                                        <th class="text-center" style="width: 200px">NASABAH</th>
                                        <th class="text-center" style="width: 150px">PENGAJUAN</th>
                                        <th class="text-center">STATUS</th>
                                        <th class="text-center" style="width: 50px;">K1</th>
                                        <th class="text-center" style="width: 50px;">K2</th>
                                        <th class="text-center" style="width: 50px;">K3</th>
                                        <th class="text-center" style="width: 50px;">K4</th>
                                        <th class="text-center">CATATAN</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($data as $item)
                                        <tr>
                                            <td class="text-center" style="vertical-align: middle;">1</td>
                                            <td style="text-transform: uppercase;vertical-align: middle;">
                                                {{ $item->nama_nasabah }} <br>
                                                <b>Kategori:</b> {{ $item->kategori ?? 'KOSONG' }}
                                            </td>

                                            <td style="vertical-align: middle;">
                                                <b>KODE: </b>{{ $item->kode_pengajuan }} <br>
                                                <b>PLAFON</b> : <br>
                                                {{ 'Rp.' . ' ' . number_format($item->plafon, 0, ',', '.') }}
                                            </td>

                                            <td class="text-center" style="vertical-align: middle;">
                                                <span class="label label-warning">{{ $item->tracking }}</span>
                                            </td>

                                            <td class="text-center" style="vertical-align: middle;">
                                                @if ($item->plafon >= 1000 && $item->plafon <= 10000000)
                                                    <i class="fa fa-circle text-success"></i>
                                                @else
                                                    <i class="fa fa-circle text-success"></i>
                                                @endif
                                            </td>

                                            <td class="text-center" style="vertical-align: middle;">
                                                @if ($item->plafon >= 10000001 && $item->plafon <= 35000000)
                                                    <i class="fa fa-circle text-success"></i>
                                                @elseif ($item->plafon >= 35000000)
                                                    <i class="fa fa-circle text-success"></i>
                                                @else
                                                    <i class="fa fa-circle text-danger"></i>
                                                @endif
                                            </td>

                                            <td class="text-center" style="vertical-align: middle;">
                                                @if ($item->plafon >= 35000001 && $item->plafon <= 75000000)
                                                    <i class="fa fa-circle text-success"></i>
                                                @elseif ($item->plafon >= 75000000)
                                                    <i class="fa fa-circle text-success"></i>
                                                @else
                                                    <i class="fa fa-circle text-danger"></i>
                                                @endif
                                            </td>

                                            <td class="text-center" style="vertical-align: middle;">
                                                @if ($item->plafon > 75000001)
                                                    <i class="fa fa-circle text-success"></i>
                                                @else
                                                    <i class="fa fa-circle text-danger"></i>
                                                @endif
                                            </td>

                                            <td class="text-center" style="vertical-align: middle;">
                                                <a data-toggle="modal" data-target="#modal-catatan"
                                                    data-pengajuan="{{ $item->kode_pengajuan }}">
                                                    <span class="label label-warning">BERIKAN CATATAN</span>
                                                </a>
                                            </td>

                                            <td class="text-center" style="vertical-align: middle;">
                                                <a data-toggle="modal" data-target="#" class="btn-circle btn-sm btn-success"
                                                    data-pengajuan="{{ $item->kode_pengajuan }}" title="Lihat Analisa">
                                                    <i class="fa fa-file-text-o"></i>
                                                </a>
                                                &nbsp;
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-center" colspan="10">TIDAK ADA DATA</td>
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

@push('myscript')
    <script src="{{ asset('assets/js/myscript/persetujuan_komite.js') }}"></script>
    <script src="{{ asset('assets/js/myscript/catatan_komite.js') }}"></script>
@endpush
