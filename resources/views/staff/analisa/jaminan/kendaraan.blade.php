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
                        <th class="text-center" style="width: 150px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($jaminan as $item)
                        <tr>
                            <td style="vertical-align: middle;">
                                <b>Jenis: </b><br>
                                {{ Str::upper($item->jenis_agunan) }}
                                <p></p>
                                <b>Dokumen: </b><br>
                                {{ Str::upper($item->jenis_dokumen) }}
                            </td>
                            <td style="vertical-align: middle;">
                                <b>Atas Nama: </b><br>
                                {{ Str::upper($item->atas_nama) }} <br>
                                <p></p>
                                <b>No Doukumen: </b><br>
                                {{ Str::upper($item->no_dokumen) }}
                            </td>
                            <td style="vertical-align: middle;">
                                <b>Merek: </b> {{ Str::upper($item->merek) ?? null }} <br>
                                <b>Tahun: </b> {{ Str::upper($item->tahun) ?? null }} <br>
                                <b>No. Rangka: </b> {{ Str::upper($item->no_rangka) ?? null }} <br>
                                <b>No. Mesin: </b> {{ Str::upper($item->no_mesin) ?? null }} <br>
                                <b>No. Polisi: </b> {{ Str::upper($item->no_polisi) ?? null }}
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

                                <a href="{{ route('taksasi.delete_lain', ['id' => $item->id]) }}" data-toggle="modal"
                                    data-target="#hapus" class="btn btn-sm bg-red confirmdelete" title="Hapus"
                                    style="cursor: pointer;">
                                    <i class="fa fa-trash"></i>
                                </a>

                            </td>
                        </tr>
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

                <form action="{{ route('analis.simpan_kendaraan') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="box-body">
                            <div class="row">

                                <input type="text" value="" name="input_user" hidden>
                                <input type="text" value="{{ $data->kode_pengajuan }}" name="pengajuan_kode" hidden>
                                <input type="text" value="{{ $data->nasabah_kode }}" name="nasabah_kode" hidden>
                                <input type="text" name="jenis_jaminan" value="Kendaraan" hidden>

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
                <form action="{{ route('taksasi.updatekendaraan') }}" method="POST">
                    @csrf
                    <div class="modal-body">

                        <div class="box-body">
                            <div class="row">
                                <div class="div-left">
                                    <div style="margin-top: -15px;">
                                        <span class="fw-bold">JENIS AGUNAN</span>
                                        <input type="text" id="id" name="id" hidden>
                                        <input type="text" id="kode" name="jenis_agunan_kode" hidden>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            value="Kendaraan Bermotor Roda 2" id="jenis" name="jenis_agunan"
                                            readonly>
                                    </div>
                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">JENIS DOKUMEN</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            value="BPKB Motor Non Fiducia" id="dokumen" readonly>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">MEREK KENDARAAN</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            value="Honda" id="merk" name="merek">
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">TIPE KENDARAAN</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            value="Motor Metik" id="tipe" name="tipe_kendaraan">
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">TAHUN KENDARAAN</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            value="2020" id="tahun_kendaraan" name="tahun">
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">NOMOR RANGKA</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            value="BDAS594168" id="no_rangka" name="no_rangka">
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">NOMOR MESIN</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            value="458131564616" id="no_mesin" name="no_mesin">
                                    </div>
                                </div>

                                <div class="div-right">
                                    <div style="margin-top: -15px;">
                                        <span class="fw-bold">NOMOR POLISI</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            value="T 4414 YB" id="no_polisi" name="no_polisi">
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">NOMOR BPKB</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            value="P007772168" id="no_dok" name="no_dokumen">
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">WARNA KENDARAAN</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            value="Hitam" id="warna" name="warna">
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">PEMILIK KENDARAAN</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            value="ZULFADLI RIZAL" id="nama" name="atas_nama">
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">LOKASI KENDARAAN</span>
                                        <input class="form-control input-sm form-border text-uppercase" type="text"
                                            id="lok" name="lokasi">
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
                                        <input type="text" name="jenis" value="kendaraan" hidden>
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

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">
                                            LAINNYA
                                            <a href="#" class="pull-right" id="lain1"
                                                data-target="lain1">PREVIEW</a>
                                        </span>
                                        <input type="text" name="name_img_5" id="name_img_5" hidden>
                                        <input class="form-control input-sm form-border text-uppercase" type="file"
                                            name="foto5" accept="image/*">
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

                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">
                                            LAINNYA
                                            <a href="#" class="pull-right" id="lain2"
                                                data-target="lain2">PREVIEW</a>
                                        </span>
                                        <input type="text" name="name_img_6" id="name_img_6" hidden>
                                        <input class="form-control input-sm form-border text-uppercase" type="file"
                                            name="foto6" accept="image/*">
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
    <script src="{{ asset('assets/js/myscript/jaminan_kendaraan.js') }}"></script>
    <script src="{{ asset('assets/js/myscript/preview_fhoto_kendaraan.js') }}"></script>
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
        $(function() {
            $(".confirmdelete").click(function(event) {
                event.preventDefault();

                var kd = $(this).data('kd');
                var deleteUrl = $(this).attr('href'); // Mendapatkan URL dari href

                Swal.fire({
                    title: "Apakah anda yakin?",
                    text: "Yakin, Ingin menghapus data",
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Ya, Hapus!",
                    cancelButtonText: "Batal",
                }).then((willDelete) => {
                    if (willDelete.isConfirmed) {
                        $.ajax({
                            url: deleteUrl,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            success: function(response) {

                                if (response.message == 'Data Berhasil Dihapus.') {
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: 'Data berhasil dihapus.',
                                        icon: 'success',
                                        showConfirmButton: false,
                                        timer: 2000
                                    }).then((result) => {
                                        if (result.isConfirmed || result
                                            .dismiss ===
                                            'timer') {
                                            location.reload();
                                        }
                                    });

                                }
                            },
                            error: function(xhr, status, error) {
                                console.error("Error:", xhr.responseText);
                            }
                        });
                    }
                });
            });
        })
    </script>
@endpush
