@extends('staff.analisa.jaminan.menu', [$data, 'pengajuan' => $data->kd_pengajuan])
@section('title', 'Analisa Jaminan')

@section('content')
    <div class="tab-content">
        <div class="tab-pane active">
            <table class="table table-striped table-hover table-bordered table-condensed">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 200px">Agunan</th>
                        <th class="text-center" style="width: 150px">Informasi</th>
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
                                {{ $item->jenis_agunan }}
                                <p></p>
                                <b>Dokumen: </b><br>
                                {{ $item->jenis_dokumen }}
                            </td>
                            <td style="vertical-align: middle;">
                                <b>Atas Nama: </b><br>
                                {{ $item->atas_nama }} <br>
                                <p></p>
                                <b>No Doukumen: </b><br>
                                {{ $item->no_dokumen }}
                            </td>
                            <td style="vertical-align: middle;">
                                <b>Lokasi: </b> <br>
                                {{ $item->lokasi }}
                                <p></p>
                                <b>Catatan: </b><br>
                                {{ $item->catatan }}
                            </td>
                            <td style="vertical-align: middle;">
                                {{ 'RP. ' . ' ' . number_format($item->nilai_taksasi, 0, ',', '.') }}</td>
                            <td class="text-center" style="vertical-align: middle;text-transform:uppercase;">
                                <button data-toggle="modal" data-target="#modal-edit" class="btn btn-sm btn-warning">
                                    <i class="fa fa-file-text-o"></i>
                                </button>

                                <button data-toggle="modal" data-target="#modal-foto-lain"
                                    data-id="{{ $item->id }}, {{ $item->atas_nama }}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-image"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center" colspan="7">Tidak ada jaminan lainnya.</td>
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
                <form action="{{ route('taksasi.simpanlain', ['pengajuan' => $data->kd_pengajuan]) }}" method="POST">
                    @csrf
                    <div class="modal-body">

                        <div class="box-body">
                            <div class="row">
                                <div class="div-left">
                                    <div style="margin-top: -15px;">
                                        <span class="fw-bold">JENIS AGUNAN</span>
                                        <input type="text" name="id" id="id" hidden>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            value="Kendaraan Bermotor Roda 2" name="jenis_agunan" id="jenis_agunan"
                                            readonly>
                                    </div>
                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">JENIS DOKUMEN</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            value="BPKB Motor Non Fiducia" name="dokumen" id="dokumen" readonly>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">NOMOR DOKUMEN</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            value="P007772168" name="no_dok" id="" readonly>
                                    </div>
                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">NAMA PEMILIK</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            value="ZULFADLI RIZAL" name="nama" id="nama" readonly>
                                    </div>
                                </div>

                                <div class="div-right">
                                    <div style="margin-top: -15px;">
                                        <span class="fw-bold">LOKASI AGUNAN</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            value="Motor Metik" name="lokasi" id="lokasi" readonly>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">NILAI PASAR</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            placeholder="Rp." name="nilai_pasar" id="nilai_pasar">
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">NILAI TAKSASI</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            name="nilai_taksasi" id="nilai_taksasi" placeholder="Rp.">
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">CATATAN</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            name="catatan" id="catatan" placeholder="ENTRI">
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

    <div class="modal fade" id="modal-foto-lain">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-blue">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">FOTO AGUNAN</h4>
                </div>
                <form action="" method="POST">
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
                                        <input type="text" id="nil" name="id" hidden>
                                        <input type="text" id="atas_namal" name="nama" hidden>
                                        <input class="form-control input-sm form-border text-uppercase" type="file"
                                            name="foto1" accept="image/*">
                                    </div>
                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">
                                            TAMPAK KIRI
                                            <a href="#" class="pull-right" id="prevkiri"
                                                data-target="kiri">PREVIEW</a>
                                        </span>
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
                                        <input class="form-control input-sm form-border text-uppercase" type="file"
                                            name="foto2" accept="image/*">
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">
                                            TAMPAK KANAN
                                            <a href="#" class="pull-right" id="prevkanan"
                                                data-target="kanan">PREVIEW</a>
                                        </span>
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
@endsection

@push('myscript')
    <script src="{{ asset('assets/js/myscript/jaminan_lainnya.js') }}"></script>
    <script src="{{ asset('assets/js/myscript/preview_fhoto_lain.js') }}"></script>
    <script>
        $("button[data-target='#modal-foto']").click(function() {
            var dataId = $(this).data('id').split(",");

            var nilaiid = dataId[0];
            var atasNama = dataId[1];

            // Menyalin nilai 'id' ke elemen di dalam modal
            $('#nil').val(nilaiid);
            $('#atas_namal').val(atasNama);
        });
    </script>
@endpush
