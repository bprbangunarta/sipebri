@extends('theme.app')
@section('title', 'Pengecekan CIF')

@section('content')
<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border" style="border-bottom: 1px solid #3C8DBC;">
                        <i class="fa fa-download"></i>
                        <h3 class="box-title">DATA CIF</h3>

                        <div class="box-tools">
                            <form action="{{ route('cif.index') }}" method="GET">
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
                                    <th class="text-center">NO CIF</th>
                                    <th class="text-center">NAMA NASABAH</th>
                                    <th class="text-center">ALAMAT</th>
                                    <th class="text-center">TEMPAT LAHIR</th>
                                    <th class="text-center">TGL. LAHIR</th>
                                    <th class="text-center">NO. IDENTITAS</th>
                                    <th class="text-center">IBU KANDUNG</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $no = 1;
                                @endphp
                                @forelse ($tabungan as $item)
                                <tr class="text-uppercase">
                                    <td class="text-center">
                                        {{ $loop->iteration + $tabungan->firstItem() - 1 }}
                                    </td>
                                    <td>{{ $item->nocif }}</td>
                                    <td>{{ $item->fname }}</td>
                                    <td>{{ $item->alamat }}</td>
                                    <td>{{ $item->tempatlahir }}</td>
                                    <td class="text-center">
                                        {{ $item->tanggal_lahir }}
                                    </td>
                                    <td class="text-center">{{ $item->noid }}</td>
                                    <td>{{ $item->nmibukandung }}</td>
                                </tr>
                                @php
                                $no++;
                                @endphp
                                @empty
                                <tr>
                                    <td class="text-center" colspan="15">TIDAK ADA DATA</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="box-footer clearfix">
                        <div class="pull-left hidden-xs">
                            <button class="btn btn-default btn-sm">
                                Showing {{ $tabungan->firstItem() }} to {{ $tabungan->lastItem() }} of
                                {{ $tabungan->total() }}
                                entries
                            </button>
                        </div>

                        {{ $tabungan->withQueryString()->onEachSide(0)->links('vendor.pagination.adminlte') }}
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection