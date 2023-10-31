<div class="modal fade" id="modal-edit-tanah">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-blue">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">EDIT AGUNAN TANAH</h4>
            </div>

            <form action="{{ route('tanah.update') }}" method="POST">
                @method('put')
                @csrf
                <div class="modal-body">
                    <div class="box-body">
                        <div class="row">

                            <input type="text" name="jenis_jaminan" value="Tanah" hidden>

                            <div class="div-left">
                                <div style="margin-top: -15px;">
                                    <span class="fw-bold">JENIS AGUNAN</span>
                                    <input type="text" value="{{ $data->auth }}" name="input_user" hidden>
                                    <input type="text" name="id" id="idt" hidden>
                                    <input type="text" value="{{ $pengajuan->kode_pengajuan }}" name="pengajuan_kode"
                                        hidden>
                                    <select type="text" class="form-control jenis_agunan" style="width: 100%;"
                                        name="jenis_agunan_kode" id="jenis_agunans" required>
                                        <option value="" selected>--PILIH--</option>
                                    </select>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">JENIS DOKUMEN</span>
                                    <select type="text" class="form-control jenis_dokumen" style="width: 100%;"
                                        name="jenis_dokumen_kode" id="jenis_dokumens" required>
                                        <option value="" selected>--PILIH--</option>
                                    </select>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">NOMOR SERTIFIKAT</span>
                                    <input class="form-control text-uppercase" type="text"
                                        value="{{ old('no_dok') }}" name="no_dokumen" id="no_doks" placeholder="ENTRI"
                                        required>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">PEMILIK SERTIFIKAT</span>
                                    <input class="form-control text-uppercase" type="text"
                                        value="{{ old('atas_nama') }}" name="atas_nama" id="atas_namas"
                                        placeholder="ENTRI" required>
                                </div>
                            </div>

                            <div class="div-right">

                                <div style="margin-top: -15px;">
                                    <span class="fw-bold">LUAS TANAH (M2)</span>
                                    <input class="form-control text-uppercase" type="text"
                                        value="{{ old('luas') }}" name="luas" id="luass" placeholder="ENTRI"
                                        required>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">LOKASI TANAH</span>
                                    <input class="form-control text-uppercase" type="text" name="lokasi"
                                        id="lokasis" value="{{ old('lokasi') }}" placeholder="ENTRI" required>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">LOKASI DATI2</span>
                                    <select type="text" class="form-control dati2" style="width:100%;"
                                        name="kode_dati" id="datit" required>
                                        <option value="" selected>--PILIH--</option>
                                        @foreach ($dati as $item)
                                            <option value="{{ $item->kode_dati }}">{{ $item->nama_dati }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">CATATAN AGUNAN</span>
                                    <input class="form-control text-uppercase" type="text" name="catatan"
                                        value="{{ old('catatan') }}" id="catat" placeholder="ENTRI">
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
    <script src="{{ asset('assets/js/myscript/edit_agunan_tanah.js') }}"></script>
@endpush
