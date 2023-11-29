@extends('theme.app')
@section('title', 'Surat Penjaminan Fiducia')

@section('content')
    <div class="content-wrapper">

        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-file-text-o"></i>
                            <h3 class="box-title">PENDAFTARAN FIDUCIA</h3>

                            <div class="box-tools">
                                <form action="{{ route('fiducia') }}" method="GET">
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
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="bg-blue">
                                        <th class="text-center" width="3%">NO</th>
                                        <th class="text-center">NASABAH</th>
                                        <th class="text-center" width="30%">KENDARAAN</th>
                                        <th class="text-center" width="15%">NOMOR</th>
                                        <th class="text-center" style="width: 100px">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $no = 1;
                                    @endphp
                                    @forelse ($data as $item)
                                        <tr>
                                            <td class="text-center" style="vertical-align: middle;">1</td>

                                            <td style="text-transform: uppercase;vertical-align: middle;">
                                                <b>KODE :</b> {{ $item->kode_pengajuan }} [ {{ $item->kategori }} ] <br>
                                                <b>AN. </b>{{ $item->nama_nasabah }} <br>
                                                @if (is_null($item->no_spk))
                                                    <span class="label label-danger" style="font-size: 12px;">NOMOR TIDAK
                                                        ADA</span>
                                                @else
                                                    <span class="label label-success"
                                                        style="font-size: 12px;">{{ $item->no_spk }}</span>
                                                @endif

                                                <p></p>
                                                {{ $item->alamat_ktp }} <br>
                                                <b>Desa: </b>{{ $item->kelurahan }} | <b>Kecamatan:
                                                </b>{{ $item->kecamatan }}
                                            </td>

                                            <td class="text-uppercase" style="vertical-align: middle;">
                                                <b>Atas Nama: </b><br> {{ $item->atas_nama }} <br>
                                                <b>Merek/ Tipe:</b><br>{{ $item->merek }}/ {{ $item->tipe_kendaraan }} /
                                                {{ $item->agunan_jenis }} <br>
                                                <b>Tahun / Warna:</b><br> {{ $item->tahun }} / {{ $item->warna }}
                                            </td>

                                            <td class="text-uppercase" style="vertical-align: middle;">
                                                <b>No. Polisi: </b><br> {{ $item->no_polisi }} <br>
                                                <b>No. Mesin:</b><br> {{ $item->no_mesin }} <br>
                                                <b>No. Rangka: </b><br> {{ $item->no_rangka }}
                                            </td>

                                            <td class="text-center" style="vertical-align: middle;">
                                                <a
                                                    href="{{ route('cetak.fiducia', ['pengajuan' => $item->kd_pengajuan]) }}">
                                                    <span class="btn bg-blue" style="width: 120px;">Cetak</span>
                                                </a>
                                            </td>
                                        </tr>
                                        @php
                                            $no++;
                                        @endphp
                                    @empty
                                        <tr>
                                            <td class="text-center text-uppercase" colspan="7">Tidak Ada Data</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="box-footer clearfix">
                            {{ $data->withQueryString()->links('vendor.pagination.adminlte') }}
                        </div>

                    </div>
                </div>
            </div>
        </section>

    </div>
@endsection

@push('myscript')
    {{-- <script src="{{ asset('assets/js/myscript/generate_kode_notifikasi.js') }}"></script> --}}
    <script>
        $("button[data-target='#generate-code']").click(function() {
            // Mendapatkan nilai 'id' dari tombol yang diklik
            var kode = $(this).data('id');
            $('#kode').val(kode);
        });
    </script>
@endpush
