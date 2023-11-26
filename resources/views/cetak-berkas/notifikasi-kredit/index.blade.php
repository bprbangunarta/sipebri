@extends('theme.app')
@section('title', 'Notifikasi Kredit')

@section('content')
    <div class="content-wrapper">

        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-print"></i>
                            <h3 class="box-title">CETAK NOTIFIKASI KREDIT</h3>
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="bg-blue">
                                        <th class="text-center" width="3%">NO</th>
                                        <th class="text-center">NOTIFIKASI</th>
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
                                            <td class="text-center" style="vertical-align: middle;">{{ $no }}</td>

                                            <td style="vertical-align: middle;">
                                                <b>KODE :</b> {{ $item->kode_pengajuan }} [ {{ $item->kategori }} ] <br>
                                                <b>AN. </b>{{ $item->nama_nasabah }} <br>
                                                @if (is_null($item->no_notifikasi))
                                                    <span class="label label-danger" style="font-size: 12px;">NOMOR TIDAK
                                                        ADA</span>
                                                @else
                                                    <span class="label label-primary"
                                                        style="font-size: 12px;">{{ $item->no_notifikasi }}</span>
                                                @endif
                                            </td>

                                            <td style="text-transform: uppercase;vertical-align: middle;">
                                                {{ $item->alamat_ktp }} <br>
                                                <b>Desa: </b>{{ $item->kelurahan }} | <b>Kecamatan:
                                                </b>{{ $item->kecamatan }}
                                            </td>

                                            <td style="vertical-align: middle;">
                                                <b>KANTOR :</b> {{ $item->kantor_kode }} <br>
                                                <b>{{ $item->produk_kode }} - JK :</b> {{ $item->jangka_waktu }} BULAN <br>
                                                <b>PLAFON :</b>
                                                {{ 'Rp.' . ' ' . number_format($item->plafon, 0, ',', '.') }} <br>
                                                <b>METODE :</b> {{ $item->metode_rps }}
                                            </td>

                                            <td style="vertical-align: middle;">
                                                {{-- <b>KREDIT: </b> {{ number_format($item->b_admin + $item->b_provisi, 2) }} --}}

                                                <b>S. BUNGA&nbsp;: </b> {{ $item->suku_bunga }}% <br>
                                                <b>PENALTI &nbsp;&nbsp;&nbsp;: </b> {{ $item->b_penalti }} <br>
                                                <b>PROVISI &nbsp;&nbsp;&nbsp;: </b>
                                                {{ number_format($item->b_provisi, 2) }} <br>
                                                <b>BY ADMIN&nbsp;: </b> {{ number_format($item->b_admin, 2) }} <br>
                                            </td>

                                            <td class="text-center" style="vertical-align: middle;">

                                                <a href="{{ route('cetak.notifikasi_kredit', ['pengajuan' => $item->kd_pengajuan]) }}" target="_blank">
                                                    <span class="btn bg-blue" style="width: 120px;hight:100%;">Cetak Berkas</span>
                                                </a>

                                            </td>
                                        </tr>
                                        @php
                                            $no++;
                                        @endphp
                                    @empty
                                        <tr>
                                            <td class="text-center text-uppercase" colspan="7">Tidak Ada Data.</td>
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
    <script src="{{ asset('assets/js/myscript/generate_kode_notifikasi.js') }}"></script>
    <script>
        $("button[data-target='#generate-code']").click(function() {
            // Mendapatkan nilai 'id' dari tombol yang diklik
            var kode = $(this).data('id');
            $('#kode').val(kode);
        });
    </script>
@endpush
