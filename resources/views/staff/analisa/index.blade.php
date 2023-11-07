@extends('theme.app')
@section('title', 'Permohonan Analisa')

@section('content')
    <div class="content-wrapper">

        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-file-text-o"></i>
                            <h3 class="box-title">PERMOHONAN ANALISA</h3>
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
                                                <b>NAMA :</b> {{ strtoupper($item->nama_nasabah) }} <br>
                                                <b>TANGGAL :</b>
                                                {{ \Carbon\Carbon::parse($item->tgl_daftar)->format('Y-m-d') }}
                                            </td>

                                            <td style="text-transform: uppercase;vertical-align:middle;">
                                                {{ $item->alamat_ktp }} <br>
                                                <b>Desa: </b>{{ $item->kelurahan }} | <b>Kecamatan:
                                                </b>{{ $item->kecamatan }}
                                            </td>

                                            @php
                                            $item->plafon = number_format($item->plafon, 0, ',', '.');
                                            @endphp
                                            <td style="vertical-align: middle;">
                                                <b>KANTOR :</b> {{ $item->kantor_kode }} <br>
                                                <b>{{ $item->produk_kode }} - JK :</b> {{ $item->jk }} BULAN <br>
                                                <b>PLAFON :</b> {{ $item->plafon }}
                                            </td>

                                            <td class="text-center" style="vertical-align: middle;">
                                                @if ($item->tracking == 'Proses Survei')
                                                <a href="{{ route('perdagangan.in', ['pengajuan' => $item->kd_pengajuan]) }}" style="pointer-events: none; text-decoration: none; cursor: default;" title="Input Analisa" disabled="disabled">
                                                    <span class="btn bg-gray" style="width: 120px;hight:100%;">Input Analisa</span>
                                                </a>
                                                @elseif ($item->tracking == 'Proses Analisa')
                                                <a href="{{ route('perdagangan.in', ['pengajuan' => $item->kd_pengajuan]) }}" title="Input Analisa">
                                                    <span class="btn bg-yellow" style="width: 120px;hight:100%;">Input Analisa</span>
                                                </a>
                                                @else
                                                <a href="{{ route('perdagangan.in', ['pengajuan' => $item->kd_pengajuan]) }}" title="Input Analisa">
                                                    <span class="btn bg-blue" style="width: 120px;hight:100%;">Input Analisa</span>
                                                </a>
                                                @endif

                                                <p style="margin-top:-5px;"></p>
                                                <a data-toggle="modal" data-target="#jadwal-ulang" data-pengajuan="{{ $item->kode_pengajuan }}" title="Jadwal Ulang">
                                                    <span class="btn bg-red" style="width: 120px;hight:100%;">Jadwal Ulang</span>
                                                </a>
                                            </td>
                                        </tr>
                                        @php
                                            $no++;
                                        @endphp
                                    @empty
                                        <tr>
                                            <td class="text-center text-uppercase" colspan="7">Tidak ada data</td>
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

    <div class="modal fade" id="jadwal-ulang">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-red">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">PERMINTAAN JADUL</h4>
                </div>
                <form action="{{ Route('permohonan.simpanjadul') }}" method="POST">
                    @csrf
                    <div class="modal-body">

                        <div class="box-body">
                            <div class="row">

                                <div style="margin-top: -15px;">
                                    <span class="fw-bold">KODE PENGAJUAN</span>
                                    <input type="text" id="id" name="id" hidden>
                                    <input class="form-control text-uppercase" type="text" value="123456789S"
                                        name="kode_pengajuan" id="kd_pengajuan" readonly>
                                    <input type="text" value="" name="tgl_survei" id="tgl_survei" hidden>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">NAMA NASABAH</span>
                                    <input class="form-control text-uppercase" name="nama_nasabah" id="nm_nasabah"
                                        type="text" value="ZULFADLI RIZAL" readonly>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">KETERANGAN</span>
                                    <textarea class="form-control text-uppercase" name="keterangan" id=""></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="margin-top: -10px;">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">BATAL</button>
                        <button type="submit" class="btn btn-danger">SIMPAN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('myscript')
    <script src="{{ asset('assets/js/myscript/permintaan_jadul.js') }}"></script>
@endpush
