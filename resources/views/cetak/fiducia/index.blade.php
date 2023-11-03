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
                            <h3 class="box-title">PENDAFTARAN FIDUCIA</h3>
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
                                            <span class="label label-danger" style="font-size: 12px;">NOMOR TIDAK ADA</span>
                                            @else    
                                            <span class="label label-success" style="font-size: 12px;">{{ $item->no_spk }}</span>
                                            @endif

                                            <p></p>
                                            {{ $item->alamat_ktp }} <br>
                                            <b>Desa: </b>{{ $item->kelurahan }} | <b>Kecamatan: </b>{{ $item->kecamatan }}
                                        </td>

                                        <td class="text-uppercase" style="vertical-align: middle;">
                                            <b>Atas Nama: </b><br> {{ $item->atas_nama }} <br>
                                            <b>Merek/ Tipe:</b><br>{{ $item->merek }}/ {{ $item->tipe_kendaraan }} / {{ $item->agunan_jenis }} <br>
                                            <b>Tahun / Warna:</b><br> {{ $item->tahun }} / {{ $item->warna }}
                                        </td>

                                        <td class="text-uppercase" style="vertical-align: middle;">
                                            <b>No. Polisi: </b><br> {{ $item->no_polisi }} <br>
                                            <b>No. Mesin:</b><br> {{ $item->no_mesin }} <br>
                                            <b>No. Rangka: </b><br> {{ $item->no_rangka }}
                                        </td>
                                        
                                        <td class="text-center" style="vertical-align: middle;">
                                            <a href="#">
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
                    </div>
                </div>
            </div>
        </section>

    </div>

    <div class="modal fade" id="generate-code">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-red">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">PENDAFTARAN FIDUCIA</h4>
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
                        <button type="submit" class="btn btn-danger">SIMPAN</button>
                    </div>
                </form>
            </div>
        </div>
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
