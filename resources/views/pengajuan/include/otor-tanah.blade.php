<div class="modal fade" id="otor-tanah">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-blue">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">OTORISASI AGUNAN TANAH</h4>
            </div>

            <form action="{{ route('otortanah') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="box-body">
                        <div class="row">

                            <input type="text" name="jenis_jaminan" value="Tanah" hidden>

                            <div class="div-left">
                                <div style="margin-top: -15px;">
                                    <span class="fw-bold">JENIS AGUNAN</span>
                                    <input type="text" value="{{ $data->auth }}" name="input_user" hidden>
                                    <input type="text" name="id" id="idd" hidden>
                                    <input type="text" value="{{ $pengajuan->kode_pengajuan }}" name="pengajuan_kode"
                                        hidden>
                                    <select type="text" class="form-control jenis_agunan" style="width: 100%;"
                                        name="jenis_agunan_kode" id="ja" required readonly>
                                        <option value="" selected>--PILIH--</option>
                                        {{ $jenis_tanah }}
                                        @foreach ($jenis_tanah as $item)
                                            <option value="{{ $item->kode }}">{{ $item->jenis_agunan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">JENIS DOKUMEN</span>
                                    <select type="text" class="form-control jenis_dokumen" style="width: 100%;"
                                        name="jenis_dokumen_kode" id="jd" required readonly>
                                        <option value="" selected>--PILIH--</option>
                                        @foreach ($data_tanah as $item)
                                            <option value="{{ $item->kode }}">{{ $item->jenis_dokumen }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">NOMOR SERTIFIKAT</span>
                                    <input class="form-control text-uppercase" type="text"
                                        value="{{ old('no_dok') }}" name="no_dokumen" id="no_d" readonly>
                                </div>

                            </div>

                            <div class="div-right">

                                <div style="margin-top: -15px;">
                                    <span class="fw-bold">PEMILIK SERTIFIKAT</span>
                                    <input class="form-control text-uppercase" type="text"
                                        value="{{ old('atas_nama') }}" name="atas_nama" id="atas" readonly>
                                </div>
                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">LUAS TANAH (M2)</span>
                                    <input class="form-control text-uppercase" type="text"
                                        value="{{ old('luas') }}" name="luas" id="lu" readonly>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">LOKASI TANAH</span>
                                    <input class="form-control text-uppercase" type="text" name="lokasi"
                                        id="lo" value="{{ old('lokasi') }}" readonly>
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
    <script src="{{ asset('assets/js/myscript/edit_agunan_tanah.js') }}"></script>
@endpush
