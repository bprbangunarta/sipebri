@extends('skrining.menu_data')
@section('title', 'Data Screening')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-plus"></i>
                            <h3 class="box-title">DATA ANALISA SKRINING</h3>

                            <div class="box-tools">
                                <form action="#" method="GET">
                                    <div class="input-group input-group-sm hidden-xs" style="width: 305px;">
                                        <input type="text" class="form-control text-uppercase pull-right"
                                            style="width: 180px;font-size:11.4px;" name="keyword" id="keyword"
                                            value="{{ request('keyword') }}" placeholder="NIK/ NAMA/">

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
                            <table class="table table-bordered" style="font-size:14px;">
                                <thead>
                                    <tr class="bg-blue">
                                        <th class="text-center" width="3%">NO</th>
                                        <th class="text-center" width="3%">Tanggal Screening</th>
                                        <th class="text-center">NIK</th>
                                        <th class="text-center">NAMA</th>
                                        <th class="text-center">STATUS</th>
                                        <th class="text-center" width="7%">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($data as $item)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>
                                                @if (!empty($item[14]))
                                                    {{ $item[14] }}
                                                @else
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $item[0] }}</td>
                                            <td>{{ $item[1] }}</td>
                                            <td class="text-center">{{ $item[10] }}</td>
                                            <td class="text-center" style="text-align: right;">

                                                @if ($user == 'Staff Kepatuhan' && $item[10] === 'ANALISA SKRINING')
                                                    <a href="{{ route('analisa.skrining.index', ['nik' => $item[0], 'nama' => $item[1]]) }}"
                                                        class="btn-circle btn-sm bg-yellow" title="Analisa Skrining"
                                                        disabled>
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                @elseif ($user == 'Kabag Kepatuhan' && $item[10] === 'APPROVE KABAG')
                                                    <a href="{{ route('approve.analisa.skrining', ['nik' => $item[0], 'nama' => $item[1]]) }}"
                                                        class="btn-circle btn-sm bg-yellow" title="Analisa Skrining"
                                                        disabled>
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                @elseif ($user == 'Staff Kepatuhan' && ($item[10] === 'APPROVE KABAG' || $item[10] === 'DONE'))
                                                    <a href="{{ route('update.skrining.data.index', ['nik' => $item[0], 'nama' => $item[1], 'no' => $item[17]]) }}"
                                                        class="btn-circle btn-sm bg-green" title="Analisa Skrining"
                                                        disabled>
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                @else
                                                    <a href="#" class="btn-circle btn-sm bg-gray"
                                                        title="Analisa Skrining" disabled>
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                @endif

                                                &nbsp;
                                                @if (
                                                    ($user == 'Customer Service' ||
                                                        $user == 'Realisasi' ||
                                                        $user == 'Kabag Operasional' ||
                                                        $user == 'Kepala Kantor Kas') &&
                                                        $item[10] == 'DONE')
                                                    <a href="{{ route('analisa.skrining.cetak', ['nik' => $item[0], 'nama' => $item[1]]) }}"
                                                        class="btn-circle btn-sm bg-primary" title="Print" target="_blank">
                                                        <i class="fa fa-print"></i>
                                                    </a>
                                                @elseif (
                                                    ($user == 'Staff Kepatuhan' || $user == 'Kabag Kepatuhan' || $user == 'Administrator') &&
                                                        ($item[10] == 'DONE' || $item[10] == 'APPROVE KABAG'))
                                                    <a href="{{ route('analisa.skrining.cetak', ['nik' => $item[0], 'nama' => $item[1]]) }}"
                                                        class="btn-circle btn-sm bg-primary" title="Print" target="_blank">
                                                        <i class="fa fa-print"></i>
                                                    </a>
                                                @else
                                                    <a href="#" class="btn-circle btn-sm bg-gray" title="Print"
                                                        disabled>
                                                        <i class="fa fa-print"></i>
                                                    </a>
                                                @endif

                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-center" colspan="5">TIDAK ADA DATA</td>
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
