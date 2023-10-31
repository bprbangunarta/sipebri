<div class="modal fade" id="otor-lain">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-blue">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">OTORISASI AGUNAN LAINNYA</h4>
            </div>

            <form action="{{ route('otorlain') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="box-body">
                        <div class="row">

                            <input type="text" name="jenis_jaminan" value="Lainnya" hidden>

                            <div class="div-left">
                                <div style="margin-top: -15px;">
                                    <span class="fw-bold">JENIS AGUNAN</span>
                                    <input type="text" value="{{ $data->auth }}" name="input_user" hidden>
                                    <input type="text" name="id" id='idl' hidden>
                                    <input type="text" value="{{ $pengajuan->kode_pengajuan }}" name="pengajuan_kode"
                                        hidden>
                                    <select type="text" class="form-control jenis_agunan" style="width: 100%;"
                                        name="jenis_agunan_kode" id='jenis_agunanl' required readonly>
                                        <option value="" selected>--PILIH--</option>
                                        {{ $jenis_lain }}
                                        @foreach ($jenis_lain as $item)
                                            <option value="{{ $item->kode }}">{{ $item->jenis_agunan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">JENIS DOKUMEN</span>
                                    <select type="text" class="form-control jenis_dokumen" style="width: 100%;"
                                        name="jenis_dokumen_kode" id='jenis_dokumenl' required readonly>
                                        <option value="" selected>--PILIH--</option>
                                        @foreach ($data_lain as $item)
                                            <option value="{{ $item->kode }}">{{ $item->jenis_dokumen }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">NOMOR DOKUMEN</span>
                                    <input class="form-control text-uppercase" type="text" name="no_dokumen"
                                        id="no_dokl" value="{{ old('no_dokumen') }}" readonly>
                                </div>

                            </div>

                            <div class="div-right">
                                <div style="margin-top: -15px;">
                                    <span class="fw-bold">NAMA PEMILIK</span>
                                    <input class="form-control text-uppercase" type="text" name="atas_nama"
                                        id="atas_namal" value="{{ old('atas_nama') }}" readonly>
                                </div>
                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">LOKASI AGUNAN</span>
                                    <input class="form-control text-uppercase" type="text" name="lokasi"
                                        id="lokasil" value="{{ old('lokasi') }}" readonly>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">CATATAN</span>
                                    <input class="form-control text-uppercase" type="text" name="catatan"
                                        id="catatanl" placeholder="Catatan" value="{{ old('catatan') }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="margin-top: -10px;">
                    <button type="submit" class="btn btn-primary">OTORISASI</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('myscript')
    <script src="{{ asset('assets/js/myscript/edit_agunan_lain.js') }}"></script>
@endpush
