@extends('rsc.jaminan.menu', [$data])
@section('title', 'RSC Analisa Jaminan Kendaraan')

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
                        @if ($item->jenis_agunan == 'kendaraan')
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
                                    <button data-toggle="modal" data-target="#modal-edit" data-ct="{{ $item->catatan }}"
                                        data-kode="{{ $data->kode }}" class="btn btn-sm btn-warning">
                                        <i class="fa fa-file-text-o"></i>
                                    </button>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td class="text-center" colspan="7">Tidak ada jaminan kendaraan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <a data-toggle="modal" data-target="#tambah-kendaraan" class="btn btn-sm btn-primary"
                style="margin-top:10px;">TAMBAH</a>
        </div>
    </div>

    <div class="modal fade" id="tambah-kendaraan">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-blue">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">AGUNAN KENDARAAN</h4>
                </div>

                <form action="{{ route('rsc.jaminan.add.kendaraan') }}" method="POST">
                    @method('POST')
                    @csrf
                    <div class="modal-body">
                        <div class="box-body">
                            <div class="row">

                                <input type="text" value="{{ $data->kode }}" name="pengajuan_kode" hidden>
                                <input type="text" value="{{ $data->rsc }}" name="kode_rsc" hidden>

                                <div class="div-left">
                                    <div style="margin-top: -15px;">
                                        <span class="fw-bold">JENIS AGUNAN</span>
                                        <select type="text" class="form-control jenis_agunan" style="width: 100%;"
                                            name="jenis_agunan_kode" required>
                                            <option value="" selected>--PILIH--</option>
                                            {{ $jenis_kendaraan }}
                                            @foreach ($jenis_kendaraan as $item)
                                                <option value="{{ $item->kode }}">{{ $item->jenis_agunan }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">JENIS DOKUMEN</span>
                                        <select type="text" class="form-control jenis_dokumen" style="width: 100%;"
                                            name="jenis_dokumen_kode" required>
                                            <option value="" selected>--PILIH--</option>
                                            @foreach ($data_kendaraan as $item)
                                                <option value="{{ $item->kode }}">{{ $item->jenis_dokumen }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">MEREK KENDARAAN</span>
                                        <input class="form-control text-uppercase" type="text" name="merek"
                                            value="{{ old('merek') }}" placeholder="ENTRI" required>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">TIPE KENDARAAN</span>
                                        <input class="form-control text-uppercase" type="text" name="tipe_kendaraan"
                                            value="{{ old('tipe_kendaraan') }}" placeholder="ENTRI" required>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">TAHUN KENDARAAN</span>
                                        <input class="form-control text-uppercase" type="text" name="tahun"
                                            value="{{ old('tahun') }}" placeholder="ENTRI" required>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">NOMOR RANGKA</span>
                                        <input class="form-control text-uppercase" type="text" name="no_rangka"
                                            value="{{ old('no_rangka') }}" placeholder="ENTRI" required>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">NOMOR MESIN</span>
                                        <input class="form-control text-uppercase" type="text" name="no_mesin"
                                            value="{{ old('no_mesin') }}" placeholder="ENTRI" required>
                                    </div>
                                </div>

                                <div class="div-right">
                                    <div style="margin-top: -15px;">
                                        <span class="fw-bold">NOMOR POLISI</span>
                                        <input class="form-control text-uppercase" type="text" name="no_polisi"
                                            value="{{ old('no_polisi') }}" placeholder="ENTRI" required>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">NOMOR BPKB</span>
                                        <input class="form-control text-uppercase" type="text" name="no_dokumen"
                                            placeholder="ENTRI" value="{{ old('no_dokumen') }}" required>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">WARNA KENDARAAN</span>
                                        <input class="form-control text-uppercase" type="text" name="warna"
                                            value="{{ old('warna') }}" placeholder="ENTRI" required>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">PEMILIK KENDARAAN</span>
                                        <input class="form-control text-uppercase" type="text" name="atas_nama"
                                            placeholder="ENTRI" value="{{ old('atas_nama') }}" required>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">LOKASI KENDARAAN</span>
                                        <input class="form-control text-uppercase" type="text" name="lokasi"
                                            value="{{ old('lokasi') ?? $data->alamat_ktp }}" placeholder="ENTRI"
                                            required>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">LOKASI DATI2</span>
                                        <select type="text" class="form-control select2 dati2" style="width:100%;"
                                            name="kode_dati" required>
                                            <option value="" selected>--PILIH--</option>
                                            @foreach ($dati as $item)
                                                <option value="{{ $item->kode_dati }}">{{ $item->nama_dati }}</option>
                                            @endforeach
                                        </select>
                                    </div>

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
                <form action="#" method="POST">
                    @csrf
                    <div class="modal-body">

                        <div class="box-body">
                            <div class="row">
                                <div class="col" style="margin-bottom: 10px;">
                                    <span class="fw-bold">INFORMASI</span>
                                    <textarea class="form-control" name="" id="text_catatan" cols="50" rows="3"
                                        style="resize: none;"></textarea>
                                </div>
                                <div class="col">
                                    <span class="fw-bold">NILAI TAKSASI</span>
                                    <input class="form-control input-sm text-uppercase" type="text" value="T 4414 YB"
                                        id="no_polisi" name="no_polisi">
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
                        console.log(response)
                    }
                })
            })
        })
    </script>
@endpush
