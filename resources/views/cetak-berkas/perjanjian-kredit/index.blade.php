@extends('theme.app')
@section('title', 'Cetak Perjanjian')

@section('content')
    <div class="content-wrapper">

        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-print"></i>
                            <h3 class="box-title">CETAK PERJANJIAN KREDIT</h3>

                            <div class="box-tools">
                                <form action="{{ route('cetak.perjanjian.index') }}" method="GET">
                                    <div class="input-group input-group-sm hidden-xs" style="width: 305px;">
                                        <input type="text" class="form-control text-uppercase pull-right"
                                            style="width: 170px;" name="keyword" id="keyword"
                                            value="{{ request('keyword') }}" placeholder="Nama/ Kode/ Wilayah">

                                        <div class="input-group-btn">
                                            <button type="submit" class="btn bg-blue">
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
                                        <th class="text-center">PERJANJIAN</th>
                                        <th class="text-center" width="33%">ALAMAT</th>
                                        <th class="text-center" width="18%">PENGAJUAN</th>
                                        <th class="text-center" width="13%">BIAYA</th>
                                        <th class="text-center" style="width: 100px">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $no = 1;
                                    @endphp
                                    @forelse ($data as $item)
                                        <tr>
                                            <td class="text-center" style="vertical-align: middle;">{{ $loop->iteration + $data->firstItem() - 1 }}</td>

                                            <td style="vertical-align: middle;">
                                                <b>KODE :</b> {{ $item->kode_pengajuan }} [ {{ $item->kategori }} ] <br>
                                                <b>AN. </b>{{ $item->nama_nasabah }} <br>
                                                @if (is_null($item->no_spk))
                                                <span class="label label-danger" style="font-size: 12px;">NOMOR TIDAK ADA</span>
                                                @else    
                                                <span class="label label-success" style="font-size: 12px;">{{ $item->no_spk }}</span>
                                                @endif
                                            </td>

                                            <td style="text-transform: uppercase;vertical-align: middle;">
                                                {{ $item->alamat_ktp }} <br>
                                                <b>Desa: </b>{{ $item->kelurahan }} | <b>Kecamatan:
                                                </b>{{ $item->kecamatan }}
                                            </td>

                                            <td style="vertical-align: middle;">
                                                <b>KANTOR :</b> {{ $item->kode_kantor }} <br>
                                                <b>{{ $item->produk_kode }} - JK :</b> {{ $item->jangka_waktu }} BULAN <br>
                                                <b>PLAFON :</b> {{ 'Rp.' . ' ' . number_format($item->plafon, 0, ',', '.') }} <br>
                                                <b>METODE :</b> {{ $item->metode_rps }}
                                            </td>

                                            <td style="vertical-align: middle;">
                                                {{-- <b>KREDIT: </b> {{ number_format($item->b_admin + $item->b_provisi, 2) }} --}}

                                                <b>S. BUNGA&nbsp;: </b> {{ $item->suku_bunga }}% <br>
                                                <b>PENALTI &nbsp;&nbsp;&nbsp;: </b> {{ $item->b_penalti }} <br>
                                                <b>PROVISI &nbsp;&nbsp;&nbsp;: </b> {{ number_format($item->b_provisi, 2) }} <br>
                                                <b>BY ADMIN&nbsp;: </b> {{ number_format($item->b_admin, 2) }} <br>
                                            </td>

                                            <td class="text-center" style="vertical-align: middle;">
                                                <a data-toggle="modal" data-target="#info-{{ $item->kode_pengajuan }}" class="btn-circle btn-sm bg-yellow" title="Informasi">
                                                    <i class="fa fa-eye"></i>
                                                </a>

                                                &nbsp;
                                                @if (is_null($item->no_spk))
                                                    <a data-toggle="modal" data-target="#modal-danger" class="btn-circle btn-sm bg-red">
                                                        <i class="fa fa-print"></i>
                                                    </a>
                                                @else
                                                    <a href="{{ route('cetak.otor_perjanjian_kredit', ['pengajuan' => $item->kd_pengajuan]) }}" target="_blank" class="btn-circle btn-sm bg-blue" title="Cetak Berkas">
                                                        <i class="fa fa-print"></i>
                                                    </a>
                                                @endif
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
                            <div class="pull-left">
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

    <div class="modal fade" id="modal-danger">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-red">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">PERJANJIAN KREDIT</h4>
                </div>
                
                <div class="modal-body">
                    <p>Mohon maaf cetak perjanjian kredit tidak bisa dilakukan karena nomor perjanjian kredit belum diturunkan. Silahkan hubungi bagian realisasi. Terimakasih</p>
                </div>
                <div class="modal-footer" style="margin-top: -10px;">
                    <button type="button" class="btn btn-danger" style="width: 100%;" data-dismiss="modal">TUTUP</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('myscript')
    <script src="{{ asset('assets/js/myscript/generate_kode_spk.js') }}"></script>
    <script>
        $("button[data-target='#generate-code']").click(function() {
            // Mendapatkan nilai 'id' dari tombol yang diklik
            var kode = $(this).data('id');
            $('#kode').val(kode);
        });
    </script>
@endpush
