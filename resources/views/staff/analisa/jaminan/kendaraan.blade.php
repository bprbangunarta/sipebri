@extends('staff.analisa.jaminan.menu', [$data, 'pengajuan' => $data->kd_pengajuan])
@section('title', 'Analisa Jaminan')

@section('content')
    <div class="tab-content">
        <div class="tab-pane active">
            <table class="table table-striped table-hover table-bordered table-condensed">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 200px">Agunan</th>
                        <th class="text-center" style="width: 200px">Informasi</th>
                        <th class="text-center">Detail</th>
                        <th class="text-center" style="width: 100px">Taksasi</th>
                        <th class="text-center" style="width: 100px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($jaminan as $item)
                        <tr>
                            <td style="vertical-align: middle;">
                                <b>Jenis: </b><br>
                                Kendaraan Bermotor Roda 2
                                <p></p>
                                <b>Dokumen: </b><br>
                                {{ $item->jenis_agunan }}
                            </td>
                            <td style="vertical-align: middle;">
                                <b>Atas Nama: </b><br>
                                {{ $item->atas_nama }} <br>
                                <p></p>
                                <b>No Doukumen: </b><br>
                                {{ $item->no_dokumen }}
                            </td>
                            <td style="vertical-align: middle;">
                                <b>Merek: </b> {{ $item->merek ?? null }} <br>
                                <b>Tahun: </b> {{ $item->tahun ?? null }} <br>
                                <b>No. Rangka: </b> {{ $item->no_rangka ?? null }} <br>
                                <b>No. Mesin: </b> {{ $item->no_mesin ?? null }} <br>
                                <b>No. Polisi: </b> {{ $item->no_polisi ?? null }}
                            </td>
                            <td style="vertical-align: middle;">
                                {{ 'Rp. ' . ' ' . number_format($item->nilai_taksasi, 0, ',', '.') }}</td>
                            <td class="text-center" style="vertical-align: middle;text-transform:uppercase;">
                                <button data-toggle="modal" data-target="#modal-edit" data-id="{{ $item->id }}"
                                    class="btn btn-sm btn-warning">
                                    <i class="fa fa-file-text-o"></i>
                                </button>

                                <button id="{{ $item->id }}" data-toggle="modal" data-target="#modal-foto"
                                    class="btn btn-sm btn-primary" data-id="{{ $item->id }}, {{ $item->atas_nama }}">
                                    <i class="fa fa-image"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center" colspan="7">Tidak ada jaminan kendaraan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="modal-edit">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-yellow">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">TAKSASI AGUNAN</h4>
                </div>
                <form action="{{ route('taksasi.updatekendaraan') }}" method="POST">
                    @csrf
                    <div class="modal-body">

                        <div class="box-body">
                            <div class="row">
                                <div class="div-left">
                                    <div style="margin-top: -15px;">
                                        <span class="fw-bold">JENIS AGUNAN</span>
                                        <input type="text" id="id" name="id" hidden>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            value="Kendaraan Bermotor Roda 2" id="jenis" readonly>
                                    </div>
                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">JENIS DOKUMEN</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            value="BPKB Motor Non Fiducia" id="dokumen" readonly>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">NOMOR BPKB</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            value="P007772168" id="no_dok" readonly>
                                    </div>
                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">PEMILIK KENDARAAN</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            value="ZULFADLI RIZAL" id="nama" readonly>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">NOMOR MESIN</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            value="458131564616" id="no_mesin" readonly>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">NOMOR POLISI</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            value="T 4414 YB" id="no_polisi" readonly>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">NOMOR RANGKA</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            value="BDAS594168" id="no_rangka" readonly>
                                    </div>
                                </div>

                                <div class="div-right">
                                    <div style="margin-top: -15px;">
                                        <span class="fw-bold">TIPE KENDARAAN</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            value="Motor Metik" id="tipe" readonly>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">MEREK KENDARAAN</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            value="Honda" id="merk" readonly>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">TAHUN KENDARAAN</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            value="2020" id="tahun_kendaraan" readonly>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">WARNA KENDARAAN</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            value="Hitam" id="warna" readonly>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">LOKASI KENDARAAN</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            value="Jl. H. Iksan No.89, Pamanukan, Kec. Pamanukan, Kabupaten Subang, Jawa Barat"
                                            id="lok" readonly>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">NILAI PASAR</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            name="nilai_pasar" id="nilai_pasar" placeholder="Rp.">
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">NILAI TAKSASI</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            name="nilai_taksasi" id="nilai_taksasi" placeholder="Rp.">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="margin-top: -10px;">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">BATAL</button>
                        <button type="submit" class="btn btn-warning">SIMPAN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-foto">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-blue">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">FOTO AGUNAN</h4>
                </div>
                <form action="{{ route('taksasi.fhotokendaraan') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">

                        <div class="box-body">
                            <div class="row">
                                <div class="div-left">
                                    <div style="margin-top: -15px;">
                                        <span class="fw-bold">
                                            TAMPAK DEPAN
                                            <a href="#" class="pull-right" id="prevdepan"
                                                data-target="depan">PREVIEW</a>
                                        </span>
                                        <input type="text" id="nid" name="id" hidden>
                                        <input type="text" id="ats_nama" name="nama" hidden>
                                        <input type="text" name="name_img_1" id="name_img_1" hidden>
                                        <input class="form-control input-sm form-border text-uppercase" type="file"
                                            name="foto1" accept="image/*">
                                    </div>
                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">
                                            TAMPAK KIRI
                                            <a href="#" class="pull-right" id="prevkiri"
                                                data-target="kiri">PREVIEW</a>
                                        </span>
                                        <input type="text" name="name_img_2" id="name_img_2" hidden>
                                        <input class="form-control input-sm form-border text-uppercase" type="file"
                                            name="foto3" accept="image/*">
                                    </div>
                                </div>

                                <div class="div-right">
                                    <div style="margin-top: -15px;">
                                        <span class="fw-bold">
                                            TAMPAK BELAKANG
                                            <a href="#" class="pull-right" id="prevbelakang"
                                                data-target="belakang">PREVIEW</a>
                                        </span>
                                        <input type="text" name="name_img_3" id="name_img_3" hidden>
                                        <input class="form-control input-sm form-border text-uppercase" type="file"
                                            name="foto2" accept="image/*">
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">
                                            TAMPAK KANAN
                                            <a href="#" class="pull-right" id="prevkanan"
                                                data-target="kanan">PREVIEW</a>
                                        </span>
                                        <input type="text" name="name_img_4" id="name_img_4" hidden>
                                        <input class="form-control input-sm form-border text-uppercase" type="file"
                                            name="foto4" accept="image/*">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="margin-top: -10px;">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">BATAL</button>
                        <button type="submit" class="btn btn-primary">SIMPAN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="view-foto">
        <div class="modal-dialog bg-primary">
            <div class="modal-content text-center">
                <img id="image-depan" alt="Image 1">
            </div>
        </div>
    </div>


@endsection

@push('myscript')
    <script src="{{ asset('assets/js/myscript/jaminan_kendaraan.js') }}"></script>
    <script src="{{ asset('assets/js/myscript/preview_fhoto.js') }}"></script>
    <script>
        $("button[data-target='#modal-foto']").click(function() {
            // Mendapatkan nilai 'id' dari tombol yang diklik
            var dataId = $(this).data('id').split(',');

            var nilaiid = dataId[0];
            var atasNama = dataId[1];

            // Menyalin nilai 'id' ke elemen di dalam modal
            $('#nid').val(nilaiid);
            $('#ats_nama').val(atasNama);
        });
    </script>
@endpush
