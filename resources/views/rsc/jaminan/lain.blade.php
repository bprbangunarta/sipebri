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
                                    <button data-toggle="modal" data-target="#tambah-tanah" data-id="#"
                                        class="btn btn-sm btn-warning">
                                        <i class="fa fa-file-text-o"></i>
                                    </button>
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
                                                <option value="{{ $item->kode }}">{{ $item->kode }} -
                                                    {{ $item->jenis_agunan }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div style="margin-top: 5px;">
                                        <span class="fw-bold">JENIS DOKUMEN</span>
                                        <select type="text" class="form-control jenis_dokumen" style="width: 100%;"
                                            name="jenis_dokumen_kode" required>
                                            <option value="" selected>--PILIH--</option>
                                            @foreach ($data_lain as $item)
                                                <option value="{{ $item->kode }}">{{ $item->kode }} -
                                                    {{ $item->jenis_dokumen }}</option>
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
@endsection

@push('myscript')
    <script src="{{ asset('assets/js/myscript/jaminan_kendaraan.js') }}"></script>
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
        // $(function() {
        //     $(".confirmdelete").click(function(event) {
        //         event.preventDefault();

        //         var kd = $(this).data('kd');
        //         var deleteUrl = $(this).attr('href'); // Mendapatkan URL dari href

        //         Swal.fire({
        //             title: "Apakah anda yakin?",
        //             text: "Yakin, Ingin menghapus data",
        //             icon: "question",
        //             showCancelButton: true,
        //             confirmButtonColor: "#3085d6",
        //             cancelButtonColor: "#d33",
        //             confirmButtonText: "Ya, Hapus!",
        //             cancelButtonText: "Batal",
        //         }).then((willDelete) => {
        //             if (willDelete.isConfirmed) {
        //                 $.ajax({
        //                     url: deleteUrl,
        //                     type: 'DELETE',
        //                     headers: {
        //                         'X-CSRF-TOKEN': '{{ csrf_token() }}'
        //                     },
        //                     success: function(response) {

        //                         if (response.message == 'Data Berhasil Dihapus.') {
        //                             Swal.fire({
        //                                 title: 'Berhasil!',
        //                                 text: 'Data berhasil dihapus.',
        //                                 icon: 'success',
        //                                 showConfirmButton: false,
        //                                 timer: 2000
        //                             }).then((result) => {
        //                                 if (result.isConfirmed || result
        //                                     .dismiss ===
        //                                     'timer') {
        //                                     location.reload();
        //                                 }
        //                             });

        //                         }
        //                     },
        //                     error: function(xhr, status, error) {
        //                         console.error("Error:", xhr.responseText);
        //                     }
        //                 });
        //             }
        //         });
        //     });
        // })
    </script>
@endpush
