@extends('analisa.menu_penjadwalan')
@section('title', 'Penjadwalan SurveI')

@section('content')
    <div class="tab-content">
        <div class="tab-pane active">
            <div class="box-header with-border">
                <div class="box-tools">
                    <form action="{{ route('hasil.survei') }}" method="GET">
                        <div class="input-group input-group-sm hidden-xs" style="width: 305px;">
                            <input type="text" class="form-control text-uppercase pull-right"
                                style="width: 180px;font-size:11.4px;" name="keyword" id="keyword"
                                value="{{ request('keyword') }}" placeholder="Nama/ Kode/ Wilayah/ Produk">

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
                <table class="table table-bordered" style="font-size:12px;">
                    <thead>
                        <tr class="bg-blue">
                            <th class="text-center" width="3%">NO</th>
                            <th class="text-center" width="8%">TGL DAFTAR</th>
                            <th class="text-center" width="8%">KODE</th>
                            <th class="text-center" width="16%">NAMA NASABAH</th>
                            <th class="text-center" width="42%">ALAMAT</th>
                            <th class="text-center" width="42%">PRODUK</th>
                            <th class="text-center" width="5%">WIL</th>
                            <th class="text-center" width="8%">PLAFON</th>
                            <th class="text-center" width="8%">SURVEYOR</th>
                            <th class="text-center" width="8%">SURVEI1</th>
                            <th class="text-center" width="8%">SURVEI2</th>
                            <th class="text-center" width="8%">SURVEI3</th>
                            <th class="text-center" width="8%">KET1</th>
                            <th class="text-center" width="8%">KET2</th>
                            <th class="text-center" width="8%">KET3</th>
                            <th class="text-center" width="8%">TIKOR</th>
                            <th class="text-center" width="8%">STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $item)
                            <tr>
                                <td class="text-center" style="vertical-align: middle;">
                                    {{ $loop->iteration + $data->firstItem() - 1 }}
                                </td>

                                <td class="text-center" style="vertical-align: middle;">
                                    {{ \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') }}
                                </td>

                                <td class="text-center" style="vertical-align: middle;">
                                    {{ $item->kode_pengajuan }}</td>

                                <td style="vertical-align: middle;">{{ $item->nama_nasabah }}</td>
                                <td style="vertical-align: middle;">{{ $item->alamat_ktp }}</td>
                                <td class="text-center" style="vertical-align: middle;">{{ $item->produk_kode }}
                                </td>
                                <td class="text-center" style="vertical-align: middle;">{{ $item->kantor_kode }}
                                </td>
                                <td class="text-right" style="vertical-align: middle;">
                                    {{ number_format($item->plafon, 0, ',', '.') }}
                                </td>
                                <td style="vertical-align: middle;">{{ $item->nama_user }}</td>
                                <td style="vertical-align: middle;">{{ $item->tgl_survei }}</td>
                                <td style="vertical-align: middle;">{{ $item->tgl_jadul_1 }}</td>
                                <td style="vertical-align: middle;">{{ $item->tgl_jadul_2 }}</td>
                                <td style="vertical-align: middle;">{{ $item->catatan_survei }}</td>
                                <td style="vertical-align: middle;">{{ $item->catatan_jadul_1 }}</td>
                                <td style="vertical-align: middle;">{{ $item->catatan_jadul_2 }}</td>
                                <td>
                                    @if (!empty($item->latitude))
                                        <a data-latitude="{{ $item->latitude }}" data-longtitude="{{ $item->longitude }}"
                                            class="btn-circle btn-sm bg-blue" style="cursor: pointer" title="Lihat Lokasi">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    @endif
                                </td>
                                <td style="vertical-align: middle;">{{ $item->tracking }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center" colspan="17">TIDAK ADA DATA</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="box-footer clearfix">
                <div class="pull-left hidden-xs">
                    <button class="btn btn-default btn-sm">
                        Showing {{ $data->firstItem() }} to {{ $data->lastItem() }} of {{ $data->total() }}
                        entries
                    </button>
                </div>

                {{ $data->withQueryString()->onEachSide(0)->links('vendor.pagination.adminlte') }}
            </div>
        </div>
    </div>
@endsection
@push('myscript')
    <script>
        $(document).ready(function() {
            $('a[data-latitude]').on('click', function(e) {
                e.preventDefault()
                var latitude = $(this).data('latitude');
                var longitude = $(this).data('longtitude');

                var url = 'https://www.google.com/maps?q=' + latitude + ',' + longitude + '&z=18&t=k';
                window.open(url, '_blank');
            })
        })
    </script>
@endpush
