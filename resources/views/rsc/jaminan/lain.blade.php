@extends('rsc.jaminan.menu', [$data])
@section('title', 'RSC Analisa Jaminan Lain')

@section('content')
    <div class="tab-content">
        <div class="tab-pane active">
            <table class="table table-striped table-hover table-bordered table-condensed">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 35%">Agunan</th>
                        <th class="text-center" style="width: 50%">Informasi</th>
                        <th class="text-center" style="width: 15%">Taksasi</th>
                        <th class="text-center" style="width: 10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($jaminan as $item)
                        @if ($item->jenis_agunan == 'lainnya')
                            <tr>
                                <td style="vertical-align: middle; font-size:12px;">
                                    <b>Jenis: </b><br>
                                    {{ Str::upper($item->jenis_agunan) }}
                                    <p></p>
                                    <b>Dokumen: </b><br>
                                    {{ Str::upper($item->jenis_dokumen) }}
                                </td>
                                <td style="vertical-align: middle; font-size:12px;">
                                    {{ Str::upper(trim($item->catatan)) }}
                                </td>
                                <td style="vertical-align: middle; font-size:12px;">
                                    {{ 'Rp. ' . ' ' . number_format($item->nilai_taksasi, 0, ',', '.') }}</td>
                                <td class="text-center" style="vertical-align: middle;text-transform:uppercase;">
                                    @if ($item->validasi_data == 'Tersimpan')
                                        <button data-toggle="modal" data-target="#modal-edit" data-ct="{{ $item->catatan }}"
                                            data-kode="{{ $data->kode }}" class="btn btn-sm btn-warning">
                                            <i class="fa fa-file-text-o"></i>
                                        </button>
                                    @else
                                        <form action="{{ route('rsc.jaminan.add.jaminan') }}" method="POST">
                                            @csrf

                                            <input type="text" value="{{ $data->kode }}" name="pengajuan_kode" hidden>
                                            <input type="text" value="{{ $data->rsc }}" name="kode_rsc" hidden>
                                            <input type="text" value="{{ $item->jnsjaminan }}" name="jenis_agunan_kode"
                                                hidden>
                                            <input type="text" value="{{ $item->jnsdokumen }}" name="jenis_dokumen_kode"
                                                hidden>
                                            <input type="text" value="{{ $item->nilai_taksasi }}" name="taksasi_lama"
                                                hidden>
                                            <input type="text" value="{{ $item->catatan }}" name="catatan" hidden>

                                            <button type="submit" class="btn btn-sm btn-danger" title="Add Lain">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td class="text-center" colspan="7">Tidak ada jaminan Lain.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <a data-toggle="modal" data-target="#tambah-lainnya" class="btn btn-sm btn-primary"
                style="margin-top:10px;">TAMBAH</a>
        </div>
    </div>

    <div class="modal fade" id="tambah-lainnya">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-blue">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">AGUNAN LAINNYA</h4>
                </div>

                <form action="{{ route('rsc.jaminan.add.lain') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="box-body">
                            <div class="row">

                                <input type="text" name="jenis_jaminan" value="Lainnya" hidden>

                                <div class="div-left">
                                    <div style="margin-top: -15px;">
                                        <span class="fw-bold">JENIS AGUNAN</span>

                                        <input type="text" value="{{ $data->kode }}" name="pengajuan_kode" hidden>
                                        <input type="text" value="{{ $data->rsc }}" name="kode_rsc" hidden>

                                        <select type="text" class="form-control jenis_agunan" style="width: 100%;"
                                            name="jenis_agunan_kode" required>
                                            <option value="" selected>--PILIH--</option>

                                            @foreach ($jenis_lain as $item)
                                                <option value="{{ $item->kode }}">{{ $item->jenis_agunan }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">JENIS DOKUMEN</span>
                                        <select type="text" class="form-control jenis_dokumen" style="width: 100%;"
                                            name="jenis_dokumen_kode" required>
                                            <option value="" selected>--PILIH--</option>
                                            @foreach ($data_lain as $item)
                                                <option value="{{ $item->kode }}">{{ $item->jenis_dokumen }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">NOMOR DOKUMEN</span>
                                        <input class="form-control text-uppercase" type="text" name="no_dokumen"
                                            id="no_dok" value="{{ old('no_dokumen') }}" placeholder="ENTRI" required>
                                    </div>

                                </div>

                                <div class="div-right">
                                    <div style="margin-top: -15px;">
                                        <span class="fw-bold">NAMA PEMILIK</span>
                                        <input class="form-control text-uppercase" type="text" name="atas_nama"
                                            id="atas_nama" value="{{ old('atas_nama') }}" placeholder="ENTRI" required>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">LOKASI AGUNAN</span>
                                        <input class="form-control text-uppercase" type="text" name="lokasi"
                                            id="lokasi" value="{{ old('lokasi') ?? $data->alamat_ktp }}"
                                            placeholder="ENTRI">
                                    </div>

                                    <input class="form-control text-uppercase" type="hidden" name="kode_dati"
                                        {{ old('kode_dati') }} value="0121">

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">CATATAN</span>
                                        <input class="form-control text-uppercase" type="text" name="catatan"
                                            {{ old('catatan') }} placeholder="DIBUAT SECARA OTOMATIS" readonly>
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

    <div class="modal fade" id="modal-edit">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-yellow">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">TAKSASI AGUNAN</h4>
                </div>
                <form action="{{ route('rsc.simpan.taksasi', ['kode' => $data->kode]) }}" method="POST">
                    @csrf
                    <div class="modal-body">

                        <div class="box-body">
                            <div class="row">
                                <div class="col" style="margin-bottom: 10px;">

                                    <input type="text" value="" name="id" id="id" hidden>

                                    <span class="fw-bold">INFORMASI</span>
                                    <textarea class="form-control" name="" id="text_catatan" cols="50" rows="3"
                                        style="resize: none;"></textarea>
                                </div>
                                <div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>POSISI AGUNAN</label>
                                                <input type="text" class="form-control text-uppercase"
                                                    name="posisi_agunan" id="posisi_agunan" placeholder="ENTRI"
                                                    value="" required>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>KONDISI AGUNAN</label>
                                                <input type="text" class="form-control text-uppercase"
                                                    name="kondisi_agunan" id="kondisi_agunan" placeholder="ENTRI"
                                                    value="" required>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>NILAI TAKSASI AGUNAN</label>
                                                <input type="text" class="form-control text-uppercase"
                                                    name="nilai_taksasi" id="nilai_taksasi" placeholder="ENTRI"
                                                    value="" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>TAKSASI LAMA</label>
                                                <input type="text" class="form-control text-uppercase"
                                                    name="taksasi_lama" id="taksasi_lama" placeholder="ENTRI"
                                                    value="" readonly>
                                            </div>
                                        </div>
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
@endsection

@push('myscript')
    <script>
        //Initialize Select2 Elements
        $('.jenis_agunan').select2()
        $('.jenis_dokumen').select2()
        $('.dati2').select2()
    </script>

    <script>
        $(document).ready(function() {
            $('#modal-edit').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget);
                const kode = button.data('kode')
                const ct = button.data('ct');

                $.ajax({
                    url: '{{ route('rsc.jaminan.get.kendaraan') }}',
                    type: "GET",
                    dataType: "json",
                    cache: false,
                    data: {
                        kode: kode,
                        catatan: ct
                    },
                    success: function(response) {

                        $('#text_catatan').val(response.catatan)
                        $('#posisi_agunan').val(response.posisi_agunan)
                        $('#kondisi_agunan').val(response.kondisi_agunan)
                        $('#nilai_taksasi').val(response.nilai_taksasi ?? 0)
                        $('#taksasi_lama').val(response.taksasi_lama ?? 0)
                        $('#id').val(response.id)
                    }
                })
            })
        })

        var taksasi = document.getElementById('nilai_taksasi')

        if (taksasi) {
            taksasi.addEventListener("keyup", function(e) {
                taksasi.value = formatRupiah(this.value, "Rp. ");
            });
        }

        function formatRupiah(angka, prefix) {
            var number_string = angka.replace(/[^,\d]/g, "").toString(),
                split = number_string.split(","),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                separator = sisa ? "." : "";
                rupiah += separator + ribuan.join(".");
            }

            rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
            return prefix == undefined ? rupiah : rupiah ? "" + rupiah : "";
        }
    </script>
@endpush
