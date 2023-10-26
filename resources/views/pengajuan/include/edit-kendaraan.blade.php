<div class="modal fade" id="modal-edit-kendaraan">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-blue">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">EDIT AGUNAN KENDARAAN</h4>
            </div>

            <form action="{{ route('kendaraan.update') }}" method="POST">
                @method('put')
                @csrf
                <div class="modal-body">
                    <div class="box-body">
                        <div class="row">

                            <input type="text" name="jenis_jaminan" value="Kendaraan" hidden>

                            <div class="div-left">
                                <div style="margin-top: -15px;">
                                    <span class="fw-bold">JENIS AGUNAN</span>
                                    <select type="text" class="form-control jenis_agunan" style="width: 100%;"
                                        name="jenis_agunan_kode" required>
                                        <option value="" selected>--PILIH--</option>
                                        {{ $agunan }}
                                        @foreach ($agunan as $item)
                                            <option value="{{ $item->kode }}">{{ $item->jenis_agunan }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">JENIS DOKUMEN</span>
                                    <select type="text" class="form-control jenis_dokumen" style="width: 100%;"
                                        name="jenis_dokumen_kode" required>
                                        <option value="" selected>--PILIH--</option>
                                        @foreach ($dok as $item)
                                            <option value="{{ $item->kode }}">{{ $item->jenis_dokumen }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">NOMOR BPKB</span>
                                    <input class="form-control text-uppercase" type="text" name="no_dokumen"
                                        placeholder="ENTRI" value="{{ old('no_dokumen') }}" required>
                                </div>
                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">PEMILIK KENDARAAN</span>
                                    <input class="form-control text-uppercase" type="text" name="atas_nama"
                                        placeholder="ENTRI" value="{{ old('atas_nama') }}" required>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">NOMOR MESIN</span>
                                    <input class="form-control text-uppercase" type="text" name="no_mesin"
                                        value="{{ old('no_mesin') }}" placeholder="ENTRI" required>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">NOMOR POLISI</span>
                                    <input class="form-control text-uppercase" type="text" name="no_polisi"
                                        value="{{ old('no_polisi') }}" placeholder="ENTRI" required>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">NOMOR RANGKA</span>
                                    <input class="form-control text-uppercase" type="text" name="no_rangka"
                                        value="{{ old('no_rangka') }}" placeholder="ENTRI" required>
                                </div>
                            </div>

                            <div class="div-right">
                                <div style="margin-top: -15px;">
                                    <span class="fw-bold">TIPE KENDARAAN</span>
                                    <input class="form-control text-uppercase" type="text" name="tipe_kendaraan"
                                        value="{{ old('tipe_kendaraan') }}" placeholder="ENTRI" required>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">MEREK KENDARAAN</span>
                                    <input class="form-control text-uppercase" type="text" name="merek"
                                        value="{{ old('merek') }}" placeholder="ENTRI" required>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">TAHUN KENDARAAN</span>
                                    <input class="form-control text-uppercase" type="text" name="tahun"
                                        value="{{ old('tahun') }}" placeholder="ENTRI" required>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">WARNA KENDARAAN</span>
                                    <input class="form-control text-uppercase" type="text" name="warna"
                                        value="{{ old('warna') }}" placeholder="ENTRI" required>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">LOKASI KENDARAAN</span>
                                    <input class="form-control text-uppercase" type="text" name="lokasi"
                                        {{ old('lokasi') }} placeholder="ENTRI" required>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">LOKASI DATI2</span>
                                    <select type="text" class="form-control dati2" style="width:100%;"
                                        name="kode_dati" required>
                                        <option value="" selected>--PILIH--</option>
                                        @foreach ($dati as $item)
                                            <option value="{{ $item->kode_dati }}">{{ $item->nama_dati }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">CATATAN AGUNAN</span>
                                    <input class="form-control text-uppercase" type="text" name="catatan"
                                        {{ old('catatan') }} placeholder="ENTRI">
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
    <script src="{{ asset('assets/js/myscript/edit_agunan_kendaraan.js') }}"></script>
@endpush
