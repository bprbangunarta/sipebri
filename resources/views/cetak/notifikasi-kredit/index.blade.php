@extends('theme.app')
@section('title', 'Notifikasi Kredit')

@section('content')
    <div class="content-wrapper">

        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-file-text-o"></i>
                            <h3 class="box-title">NOTIFIKASI KREDIT</h3>
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
                                                    <span class="label label-warning"
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

                                                <a data-toggle="modal" data-target="#generate-code"
                                                    data-id="{{ $item->kode_pengajuan }}">
                                                    <span class="btn bg-blue" style="width: 120px;hight:100%;">Generate</span>
                                                </a>

                                                <p style="margin-top:-5px;"></p>
                                                <a data-toggle="modal" data-target="#catatan">
                                                    <span class="btn bg-yellow" style="width: 120px;hight:100%;">Catatan</span>
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

    <div class="modal fade" id="generate-code">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-blue">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">GENERATE NOMOR</h4>
                </div>
                <form action="{{ route('simpan.notifikasi') }}" method="POST">
                    @csrf
                    <div class="modal-body">

                        <div class="box-body">
                            <div class="row">

                                <div style="margin-top: -15px;">
                                    <span class="fw-bold">KODE PENGAJUAN</span>
                                    <input type="text" id="kode" hidden>
                                    <input type="text" name="nomor" id="nomor" hidden>
                                    <input class="form-control text-uppercase" type="text" name="kode_pengajuan"
                                        id="kd_pengajuan" readonly>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">NAMA NASABAH</span>
                                    <input class="form-control text-uppercase" name="nama_nasabah" id="nm_nasabah"
                                        type="text" readonly>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">KODE NOTIFIKASI</span>
                                    <input class="form-control text-uppercase" name="kode_notifikasi" id="generate"
                                        type="text" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="margin-top: -10px;">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">BATAL</button>
                        <button type="submit" class="btn btn-primary">GENERATE</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="catatan">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-yellow">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">CATATAN</h4>
                </div>
                <form action="{{ Route('simpan.spk') }}" method="POST">
                    @csrf
                    <div class="modal-body">

                        <div class="box-body">
                            <div class="row">

                                <div style="margin-top: -15px;">
                                    <span class="fw-bold">KETERANGAN BY PHONE</span>
                                    <input type="text" id="kode" hidden>
                                    <input type="text" id="nomor" name="nomor" hidden>
                                    <input type="text" id="kode_produk" name="kode_produk" hidden>

                                    <textarea class="form-control" name="keterangan" id="keterangan" rows="5"></textarea>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">RENCANA REALISASI</span>
                                    <textarea class="form-control" name="rencana_realisasi" id="rencana_realisasi" rows="5"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="margin-top: -10px;">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">BATAL</button>
                        <button type="submit" id="smb" class="btn btn-warning">SIMPAN</button>
                    </div>
                </form>
            </div>
        </div>
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
