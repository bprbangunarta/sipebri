<div class="modal fade" id="modal-edit-lain">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-blue">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">EDIT AGUNAN LAINNYA</h4>
            </div>

            <form action="{{ route('lain.simpan') }}" method="POST">
                @method('put')
                @csrf
                <div class="modal-body">
                    <div class="box-body">
                        <div class="row">

                            <input type="text" name="jenis_jaminan" value="Lainnya" hidden>

                            <div class="div-left">
                                <div style="margin-top: -15px;">
                                    <span class="fw-bold">JENIS AGUNAN</span>
                                    <input type="text" value="{{ $data->auth }}" name="input_user" hidden>
                                    <input type="text" name="id" id='idx' hidden>
                                    <input type="text" value="{{ $pengajuan->kode_pengajuan }}" name="pengajuan_kode"
                                        hidden>
                                    <select type="text" class="form-control jenis_agunan" style="width: 100%;"
                                        name="jenis_agunan_kode" id='jenis_agunanx' required>
                                        <option value="" selected>--PILIH--</option>
                                    </select>
                                </div>
                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">JENIS DOKUMEN</span>
                                    <select type="text" class="form-control jenis_dokumen" style="width: 100%;"
                                        name="jenis_dokumen_kode" id='jenis_dokumenx' required>
                                        <option value="" selected>--PILIH--</option>
                                    </select>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">NOMOR DOKUMEN</span>
                                    <input class="form-control text-uppercase" type="text" name="no_dokumen"
                                        id="no_dokx" value="{{ old('no_dokumen') }}" placeholder="ENTRI" required>
                                </div>

                            </div>

                            <div class="div-right">
                                <div style="margin-top: -15px;">
                                    <span class="fw-bold">NAMA PEMILIK</span>
                                    <input class="form-control text-uppercase" type="text" name="atas_nama"
                                        id="atas_namax" value="{{ old('atas_nama') }}" placeholder="ENTRI" required>
                                </div>
                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">LOKASI AGUNAN</span>
                                    <input class="form-control text-uppercase" type="text" name="lokasi"
                                        id="lokasix" value="{{ old('lokasi') }}" placeholder="ENTRI">
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">CATATAN</span>
                                    <input class="form-control text-uppercase" type="text" name="catatan"
                                        id="catatanx" value="{{ old('catatan') }}" placeholder="ENTRI">
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

@push('myscript')
    <script src="{{ asset('assets/js/myscript/edit_agunan_lain.js') }}"></script>
@endpush
