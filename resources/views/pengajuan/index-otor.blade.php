@extends('theme.app')
@section('title', 'Otor Pengajuan')
@yield('jquery')
@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-check"></i>
                            <h3 class="box-title">OTOR PENGAJUAN KREDIT</h3>

                            <div class="box-tools">
                                <form action="{{ route('otor.pengajuan') }}" method="GET">
                                    <div class="input-group input-group-sm hidden-xs" style="width: 305px;">
                                        <input type="text" class="form-control text-uppercase pull-right" style="width: 180px;font-size:11.4px;" name="keyword" id="keyword" value="{{ request('keyword') }}" placeholder="Nama/ Kode/ Wilayah/ Produk">

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
                                        <th class="text-center" width="8%">TANGGAL</th>
                                        <th class="text-center" width="8%">KODE</th>
                                        <th class="text-center" width="16%">NAMA NASABAH</th>
                                        <th class="text-center" width="42%">ALAMAT</th>
                                        <th class="text-center" width="5%">WIL</th>
                                        <th class="text-center" width="8%">PLAFON</th>
                                        <th class="text-center" width="10%">AKSI</th>
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
                                            <td class="text-center" style="vertical-align: middle;">
                                                {{ \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') }}
                                            </td>
                                            <td class="text-center" style="vertical-align: middle;">{{ $item->kode }}</td>
                                            <td style="vertical-align: middle;">{{ strtoupper($item->nama) }}</td>
                                            @if (is_null($item->alamat))
                                            <td class="text-center" style="vertical-align: middle;">-</td>
                                            @else
                                            <td class="text-uppercase" style="vertical-align: middle;">
                                                {{ $item->alamat }}
                                            </td>
                                            @endif

                                            <td class="text-center" style="vertical-align: middle;">
                                                {{ $item->kantor }}
                                            </td>

                                            @php
                                                $item->plafon = number_format($item->plafon, 0, ',', '.');
                                            @endphp
                                            <td class="text-right" style="vertical-align: middle;">
                                                {{ $item->plafon }}
                                            </td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                <a href="{{ route('nasabah.edit', ['nasabah' => $item->kd]) }}" class="btn-circle btn-sm bg-green" title="Otor Pengajuan">
                                                    <i class="fa fa-check-circle"></i>
                                                </a>

                                                &nbsp;
                                                <a data-toggle="modal" data-target="#info-{{ $item->kode }}" class="btn-circle btn-sm bg-blue" title="Informasi">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            </td>

                                            {{-- MODAL INFO --}}
                                            <div class="modal fade" id="info-{{ $item->kode }}">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-blue">
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span></button>
                                                            <h4 class="modal-title">INFORMASI PENGAJUAN</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>NAMA NASABAH</label>
                                                                        <input type="text" class="form-control"
                                                                        value="{{ $item->nama }} - {{ $item->kategori }}">
                                                                    </div>
                                                                    
                                                                    <div class="form-group"  style="margin-top:-10px;">
                                                                        <label>PRODUK KREDIT</label>
                                                                        <input type="text" class="form-control" value="{{ $item->produk_kode }} - {{ $item->nama_produk }}">
                                                                    </div>
                                        
                                                                    <div class="form-group" style="margin-top:-10px;">
                                                                        <label>PLAFON KREDIT</label>
                                                                        <input type="text" class="form-control" value="{{ $item->plafon }}">
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label>JANGKA WAKTU</label>
                                                                        <input type="text" class="form-control" value="{{ $item->jk }} BULAN - {{ $item->metode_rps }}">
                                                                    </div>
                                                                </div>
                                        
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>DESA KECAMATAN</label>
                                                                        <input type="text" class="form-control" value="{{ $item->kelurahan }} - {{ $item->kecamatan }}">
                                                                    </div>
                                        
                                                                    <div class="form-group" style="margin-top:-10px;">
                                                                        <label>INPUT USER</label>
                                                                        <input type="text" class="form-control" value="{{ $item->input_user }}">
                                                                    </div>

                                                                    <div class="form-group" style="margin-top:-10px;">
                                                                        <label>TRACKING</label>
                                                                        <input type="text" class="form-control text-uppercase" value="{{ $item->tracking }}">
                                                                    </div>

                                                                    <div class="form-group" style="margin-top:-10px;">
                                                                        <label>PERSETUJUAN</label>
                                                                        @if ($item->status == 'Disetujui' || $item->status == 'Ditolak' || $item->status == 'Dibatalkan')
                                                                            <input type="text" class="form-control text-uppercase" value="{{ $item->status }}">
                                                                        @else
                                                                            <input type="text" class="form-control text-uppercase" value="BELUM ADA PERSETUJUAN">
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                        
                                                        <div class="modal-footer" style="margin-top: -10px;">
                                                            <button type="submit" class="btn bg-blue" data-dismiss="modal" style="width: 100%;">TUTUP</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- END MODAL INFO --}}
                                        </tr>
                                        @php
                                            $no++;
                                        @endphp
                                    @empty
                                        <tr>
                                            <td class="text-center" colspan="8">TIDAK ADA DATA</td>
                                        </tr>
                                    @endforelse
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

@push('myscript')
    <script src="{{ asset('assets/js/myscript/batal.js') }}"></script>
    <script>
        //Datemask yyyy/mm/dd
        $('#datemask').inputmask('yyyy-mm-dd', {
            'placeholder': 'yyyy-mm-dd'
        })

        //Format rupiah
        var rupiah = document.getElementById('plafond');
        rupiah.addEventListener('keyup', function(e) {
            rupiah.value = formatRupiah(this.value, 'Rp. ');
        });

        function formatRupiah(angka, prefix) {
            var number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);


            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
        }
    </script>
@endpush
