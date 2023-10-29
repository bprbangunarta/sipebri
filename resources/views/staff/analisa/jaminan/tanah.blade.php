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
                        <th class="text-center" style="width: 120px">Taksasi</th>
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
                                <b>No Dokumen: </b><br>
                                {{ $item->no_dokumen }}
                            </td>
                            <td style="vertical-align: middle;">
                                <b>Luas: </b> {{ $item->luas }} M2 <br>
                                <b>Lokasi: </b> <br>
                                {{ $item->lokasi }}
                            </td>
                            <td style="vertical-align: middle;">
                                {{ 'RP. ' . ' ' . number_format($item->nilai_taksasi, 0, ',', '.') }}
                            </td>
                            <td class="text-center" style="vertical-align: middle;text-transform:uppercase;">
                                <button data-toggle="modal" data-target="#modal-edit" data-id="{{ $item->id }}"
                                    class="btn btn-sm btn-warning">
                                    <i class="fa fa-file-text-o"></i>
                                </button>

                                <button data-toggle="modal" data-id="{{ $item->id }}, {{ $item->atas_nama }}"
                                    data-target="#modal-foto-tanah" class="btn btn-sm btn-primary">
                                    <i class="fa fa-image"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center" colspan="7">Tidak ada jaminan tanah.</td>
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
                <form action="{{ route('taksasi.simpantanah', ['pengajuan' => $data->kd_pengajuan]) }}" method="POST">
                    @csrf
                    <div class="modal-body">

                        <div class="box-body">
                            <div class="row">
                                <div class="div-left">
                                    <div style="margin-top: -15px;">
                                        <span class="fw-bold">JENIS AGUNAN</span>
                                        <input type="text" name="id" id="id" hidden>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            value="" name="jenis_agunan" id="jenis_agunan" readonly>
                                    </div>
                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">JENIS DOKUMEN</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            value="" name="jenis_dokumen" id="jenis_dokumen" readonly>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">NOMOR SERTIFIKAT</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            value="" name="no_dok" id="no_dok" readonly>
                                    </div>
                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">PEMILIK SERTIFIKAT</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            value="" name="atas_nama" id="atas_nama" readonly>
                                    </div>
                                </div>

                                <div class="div-right">
                                    <div style="margin-top: -15px;">
                                        <span class="fw-bold">LUAS TANAH (M2)</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            value="" name="luas" id="luas" readonly>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">LOKASI TANAH</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            name="lokasi" id="lokasi" value="" readonly>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">NILAI PASAR</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            name="nilai_pasar" id="nilai_pasar" placeholder="Rp." value="">
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">NILAI TAKSASI</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            name="nilai_taksasi" id="nilai_taksasi" placeholder="Rp." value="">
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

    <div class="modal fade" id="modal-foto-tanah">
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
                                        <input type="text" id="nit" name="id" hidden>
                                        <input type="text" id="atas_namat" name="nama" hidden>
                                        <input type="text" name="jenis" value="tanah" hidden>
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
    <script src="{{ asset('assets/js/myscript/jaminan_tanah.js') }}"></script>
    <script src="{{ asset('assets/js/myscript/preview_fhoto_tanah.js') }}"></script>
    <script>
        $("button[data-target='#modal-foto-tanah']").click(function() {
            var dataId = $(this).data('id').split(",");

            var nilaiid = dataId[0];
            var atasNama = dataId[1];

            // Menyalin nilai 'id' ke elemen di dalam modal
            $('#nit').val(nilaiid);
            $('#atas_namat').val(atasNama);
        });

        $("button[data-target='#modal-edit']").click(function() {
            var dataId = $(this).data('id');

            // Menyalin nilai 'id' ke elemen di dalam modal
            $('#id').val(dataId);
        });
    </script>
@endpush
