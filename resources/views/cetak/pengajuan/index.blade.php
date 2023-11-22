@extends('theme.app')
@section('title', 'Cetak Berkas Pengajuan')
@yield('jquery')
@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-print"></i>
                            <h3 class="box-title">CETAK BERKAS PENGAJUAN</h3>

                            <div class="box-tools">
                                <form action="/cetak/pengajuan" method="GET">
                                    <div class="input-group input-group-sm hidden-xs" style="width: 170px;">
                                        <input type="text" class="form-control pull-right" name="name" id="name"
                                            value="{{ request('name') }}" placeholder="Search">

                                        <div class="input-group-btn">
                                            <button type="submit" class="btn btn-default">
                                                <i class="fa fa-search"></i>
                                            </button>
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
                                        <th class="text-center" width="10%">AKSI</th>

                                        {{-- @can('hapus pengajuan kredit')
                                            <th class="text-center" width="3%">HAPUS</th>
                                        @endcan --}}
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
                                                <b>TANGGAL :</b>
                                                {{ \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') }}
                                            </td>

                                            @if (is_null($item->alamat))
                                                <td class="text-center" style="vertical-align: middle;">-</td>
                                            @else
                                                <td class="text-uppercase" style="vertical-align: middle;">
                                                    {{ $item->alamat }} <br>
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
                                                <a href="{{ route('cetak.pengajuan', ['pengajuan' => $item->kd]) }}">
                                                    <span class="btn bg-blue" style="width: 120px;hight:100%;">
                                                    Cetak Berkas
                                                    </span>
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
