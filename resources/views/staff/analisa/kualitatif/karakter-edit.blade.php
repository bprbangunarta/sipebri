@extends('staff.analisa.kualitatif.menu', [$data, 'pengajuan' => $data->kd_pengajuan])
{{-- @extends('staff.analisa.kualitatif.menu') --}}
@section('title', 'Analisa Kualitatif')

@section('content')
    <div class="tab-content">
        <div class="tab-pane active">

            <form action="{{ route('kualitatif.updatekarakter', ['pengajuan' => $data->kd_pengajuan]) }}" method="post">
                @method('put')
                @csrf
                <div class="box-body" style="margin-top: -10px;font-size:12px;">

                    {{-- <div class="div-left">
                        <div style="width: 100%;float:left;">
                            <span class="fw-bold">SLIK (SID BANK INDONESIA)</span>
                            <select class="form-control input-sm form-border text-uppercase" name="bi_checking"
                                id="" required>
                                <option value="">--Pilih--</option>
                                <option value="4" {{ $karakter->bi_checking == 4 ? 'selected' : '' }}>Lancar
                                </option>
                                <option value="3" {{ $karakter->bi_checking == 3 ? 'selected' : '' }}>Kurang
                                    Lancar
                                </option>
                                <option value="2" {{ $karakter->bi_checking == 2 ? 'selected' : '' }}>Diragukan
                                </option>
                                <option value="1" {{ $karakter->bi_checking == 1 ? 'selected' : '' }}>Macet
                                </option>
                            </select>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">KEWAJIBAN PIHAK LAIN</span>
                            <select class="form-control input-sm form-border text-uppercase" name="kewajiban1"
                                id="">
                                <option value="" {{ $karakter->kewajiban1 == null ? 'selected' : '' }}>--Pilih--
                                </option>
                                <option value="Bank Umum" {{ $karakter->kewajiban1 == 'Bank Umum' ? 'selected' : '' }}>Bank
                                    Umum
                                </option>
                                <option value="BPR" {{ $karakter->kewajiban1 == 'BPR' ? 'selected' : '' }}>BPR
                                </option>
                                <option value="Koperasi" {{ $karakter->kewajiban1 == 'Koperasi' ? 'selected' : '' }}>
                                    Koperasi
                                </option>
                                <option value="Leasing" {{ $karakter->kewajiban1 == 'Leasing' ? 'selected' : '' }}>Leasing
                                </option>
                                <option value="lainnya" {{ $karakter->kewajiban1 == 'lainnya' ? 'selected' : '' }}>lainnya
                                </option>
                            </select>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">KETERANGAN</span>
                            <input type="text" class="form-control input-sm form-border text-uppercase"
                                name="ket_kewajiban1" id="" placeholder="ENTRI"
                                value="{{ $karakter->ket_kewajiban1 }}">
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">KEWAJIBAN PIHAK LAIN</span>
                            <select class="form-control input-sm form-border text-uppercase" name="kewajiban2"
                                id="">
                                <option value="">--Pilih--</option>
                                <option value="Bank Umum" {{ $karakter->kewajiban2 == 'Bank Umum' ? 'selected' : '' }}>Bank
                                    Umum
                                </option>
                                <option value="BPR" {{ $karakter->kewajiban2 == 'BPR' ? 'selected' : '' }}>BPR
                                </option>
                                <option value="Koperasi" {{ $karakter->kewajiban2 == 'Koperasi' ? 'selected' : '' }}>
                                    Koperasi
                                </option>
                                <option value="Leasing" {{ $karakter->kewajiban2 == 'Leasing' ? 'selected' : '' }}>
                                    Leasing
                                </option>
                                <option value="lainnya" {{ $karakter->kewajiban2 == 'lainnya' ? 'selected' : '' }}>
                                    lainnya
                                </option>
                            </select>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">KETERANGAN</span>
                            <input type="text" class="form-control input-sm form-border text-uppercase"
                                name="ket_kewajiban2" id="" placeholder="ENTRI"
                                value="{{ $karakter->ket_kewajiban2 }}">
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">KEWAJIBAN PIHAK LAIN</span>
                            <select class="form-control input-sm form-border text-uppercase" name="kewajiban3"
                                id="">
                                <option value="">--Pilih--</option>
                                <option value="Bank Umum" {{ $karakter->kewajiban3 == 'Bank Umum' ? 'selected' : '' }}>
                                    Bank Umum
                                </option>
                                <option value="BPR" {{ $karakter->kewajiban3 == 'BPR' ? 'selected' : '' }}>BPR
                                </option>
                                <option value="Koperasi" {{ $karakter->kewajiban3 == 'Koperasi' ? 'selected' : '' }}>
                                    Koperasi
                                </option>
                                <option value="Leasing" {{ $karakter->kewajiban3 == 'Leasing' ? 'selected' : '' }}>
                                    Leasing
                                </option>
                                <option value="lainnya" {{ $karakter->kewajiban3 == 1 ? 'selected' : '' }}>lainnya
                                </option>
                            </select>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">KETERANGAN</span>
                            <input type="text" class="form-control input-sm form-border text-uppercase"
                                name="ket_kewajiban3" id="" placeholder="ENTRI"
                                value="{{ $karakter->ket_kewajiban3 }}">
                        </div>
                    </div>


                    <div class="div-right">
                        <div style="width: 49.5%;float:left;">
                            <span class="fw-bold">BERURUSAN DGN PIHAK BERWAJIB</span>
                            <select class="form-control input-sm form-border text-uppercase" name="pihak_berwajib"
                                id="" required>
                                <option value="">--Pilih--</option>
                                <option value="2"
                                    {{ old('pihak_berwajib') == 2 || $karakter->pihak_berwajib == 2 ? 'selected' : '' }}>
                                    Pernah</option>
                                <option value="1"
                                    {{ old('pihak_berwajib') == 1 || $karakter->pihak_berwajib == 1 ? 'selected' : '' }}>
                                    Tidak Pernah
                                </option>
                            </select>
                        </div>

                        <div style="width: 49.5%;float:right;">
                            <span class="fw-bold">HUBUNGAN DENGAN TETANGGA</span>
                            <select class="form-control input-sm form-border text-uppercase" name="hubungan_tetangga"
                                id="" required>
                                <option value="">--Pilih--</option>
                                <option value="Baik" {{ $karakter->hubungan_tetangga == 'Baik' ? 'selected' : '' }}>
                                    Baik</option>
                                <option value="Cukup Baik"
                                    {{ $karakter->hubungan_tetangga == 'Cukup Baik' ? 'selected' : '' }}>
                                    Cukup Baik
                                </option>
                                <option value="Kurang Baik"
                                    {{ $karakter->hubungan_tetangga == 'Kurang Baik' ? 'selected' : '' }}>
                                    Kurang
                                    Baik</option>
                            </select>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">PENGALAMAN MENJADI TKI</span>
                            <select class="form-control input-sm form-border text-uppercase" name="pengalaman_tki"
                                id="">
                                <option value="">--Pilih--</option>
                                <option value="Pernah" {{ $karakter->pengalaman_tki == 'Pernah' ? 'selected' : '' }}>Pernah
                                </option>
                                <option value="Tidak Pernah"
                                    {{ $karakter->pengalaman_tki == 'Tidak Pernah' ? 'selected' : '' }}>Tidak
                                    Pernah</option>
                            </select>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">KETERANGAN</span>
                            <select class="form-control input-sm form-border text-uppercase" name="ket_pengalaman"
                                id="">
                                <option value="">--Pilih--</option>
                                <option value="Pemohon" {{ $karakter->ket_pengalaman == 'Pemohon' ? 'selected' : '' }}>
                                    Pemohon
                                </option>
                                <option value="Pendamping"
                                    {{ $karakter->ket_pengalaman == 'Pendamping' ? 'selected' : '' }}>Pendamping
                                </option>
                            </select>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">PEMOHON WAKTU DI RUMAH</span>
                            <input type="text" class="form-control input-sm form-border text-uppercase pemohon_ada"
                                name="pemohon_ada" id="" placeholder="00:00 AM"
                                value="{{ $karakter->pemohon_ada }}">
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">PENDAMPING WAKTU DI RUMAH</span>
                            <input type="text" class="form-control input-sm form-border text-uppercase pendamping_ada"
                                name="pendamping_ada" id="" placeholder="00:00 AM"
                                value="{{ $karakter->pendamping_ada }}">
                        </div>

                        <div style="margin-top:5px;width: 100%;float:right;">
                            <span class="fw-bold">INFO MASYARAKAT</span>
                            <input type="text" class="form-control input-sm form-border text-uppercase"
                                name="info_masyarakat" id="" placeholder="ENTRI"
                                value="{{ $karakter->info_masyarakat }}">
                        </div>
                    </div> --}}

                    <div class="div-left">
                        <div style="width: 49.5%;float:left;">
                            <span class="fw-bold">SLIK (SID BANK INDONESIA)</span>
                            <select class="form-control input-sm form-border text-uppercase" name="bi_checking"
                                id="" required>
                                <option value="">--Pilih--</option>
                                <option value="4" {{ $karakter->bi_checking == 4 ? 'selected' : '' }}>Lancar
                                </option>
                                <option value="3" {{ $karakter->bi_checking == 3 ? 'selected' : '' }}>Kurang
                                    Lancar
                                </option>
                                <option value="2" {{ $karakter->bi_checking == 2 ? 'selected' : '' }}>Diragukan
                                </option>
                                <option value="1" {{ $karakter->bi_checking == 1 ? 'selected' : '' }}>Macet
                                </option>
                            </select>
                        </div>

                        <div style="width: 49.5%;float:right;">
                            <span class="fw-bold">BERURUSAN DGN PIHAK BERWAJIB</span>
                            <select class="form-control input-sm form-border text-uppercase" name="pihak_berwajib"
                                id="" required>
                                <option value="">--Pilih--</option>
                                <option value="2"
                                    {{ old('pihak_berwajib') == 2 || $karakter->pihak_berwajib == 2 ? 'selected' : '' }}>
                                    Pernah</option>
                                <option value="1"
                                    {{ old('pihak_berwajib') == 1 || $karakter->pihak_berwajib == 1 ? 'selected' : '' }}>
                                    Tidak Pernah
                                </option>
                            </select>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">PENGALAMAN MENJADI TKI</span>
                            <select class="form-control input-sm form-border text-uppercase" name="pengalaman_tki"
                                id="">
                                <option value="">--Pilih--</option>
                                <option value="Pernah" {{ $karakter->pengalaman_tki == 'Pernah' ? 'selected' : '' }}>Pernah
                                </option>
                                <option value="Tidak Pernah"
                                    {{ $karakter->pengalaman_tki == 'Tidak Pernah' ? 'selected' : '' }}>Tidak
                                    Pernah</option>
                            </select>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">KETERANGAN</span>
                            <select class="form-control input-sm form-border text-uppercase" name="ket_pengalaman"
                                id="">
                                <option value="">--Pilih--</option>
                                <option value="Pemohon" {{ $karakter->ket_pengalaman == 'Pemohon' ? 'selected' : '' }}>
                                    Pemohon
                                </option>
                                <option value="Pendamping"
                                    {{ $karakter->ket_pengalaman == 'Pendamping' ? 'selected' : '' }}>Pendamping
                                </option>
                            </select>
                        </div>

                    </div>


                    <div class="div-right">

                        <div style="width: 100%;float:left;">
                            <span class="fw-bold">HUBUNGAN DENGAN TETANGGA</span>
                            <select class="form-control input-sm form-border text-uppercase" name="hubungan_tetangga"
                                id="" required>
                                <option value="">--Pilih--</option>
                                <option value="Baik" {{ $karakter->hubungan_tetangga == 'Baik' ? 'selected' : '' }}>
                                    Baik</option>
                                <option value="Cukup Baik"
                                    {{ $karakter->hubungan_tetangga == 'Cukup Baik' ? 'selected' : '' }}>
                                    Cukup Baik
                                </option>
                                <option value="Kurang Baik"
                                    {{ $karakter->hubungan_tetangga == 'Kurang Baik' ? 'selected' : '' }}>
                                    Kurang
                                    Baik</option>
                            </select>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">PEMOHON WAKTU DI RUMAH</span>
                            <input type="text" class="form-control input-sm form-border text-uppercase pemohon_ada"
                                name="pemohon_ada" id="" placeholder="00:00 AM"
                                value="{{ $karakter->pemohon_ada }}">
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">PENDAMPING WAKTU DI RUMAH</span>
                            <input type="text" class="form-control input-sm form-border text-uppercase pendamping_ada"
                                name="pendamping_ada" id="" placeholder="00:00 AM"
                                value="{{ $karakter->pendamping_ada }}">
                        </div>

                    </div>

                    <div style="margin-top:5px;width: 100%;float:right;">
                        <span class="fw-bold">INFO MASYARAKAT</span>
                        <input type="text" class="form-control input-sm form-border text-uppercase"
                            name="info_masyarakat" id="" placeholder="ENTRI"
                            value="{{ $karakter->info_masyarakat }}">
                    </div>

                    <div class="div-left" style="display: flex; justify-content: space-between; width:100%;">
                        <div style="margin-top:5px;width: 33%;">
                            <span class="fw-bold">KEWAJIBAN PIHAK LAIN</span>
                            <select class="form-control input-sm form-border text-uppercase" name="kewajiban1"
                                id="">
                                <option value="" {{ $karakter->kewajiban1 == null ? 'selected' : '' }}>--Pilih--
                                </option>
                                <option value="Bank Umum" {{ $karakter->kewajiban1 == 'Bank Umum' ? 'selected' : '' }}>
                                    Bank
                                    Umum
                                </option>
                                <option value="BPR" {{ $karakter->kewajiban1 == 'BPR' ? 'selected' : '' }}>BPR
                                </option>
                                <option value="Koperasi" {{ $karakter->kewajiban1 == 'Koperasi' ? 'selected' : '' }}>
                                    Koperasi
                                </option>
                                <option value="Leasing" {{ $karakter->kewajiban1 == 'Leasing' ? 'selected' : '' }}>Leasing
                                </option>
                                <option value="lainnya" {{ $karakter->kewajiban1 == 'lainnya' ? 'selected' : '' }}>lainnya
                                </option>
                            </select>
                        </div>

                        <div style="margin-top:5px;width: 33%;">
                            <span class="fw-bold">KETERANGAN</span>
                            <input type="text" class="form-control input-sm form-border text-uppercase"
                                name="ket_kewajiban1" id="" placeholder="ENTRI"
                                value="{{ $karakter->ket_kewajiban1 }}">
                        </div>

                        <div style="margin-top:5px;width: 33%;">
                            <span class="fw-bold">STATUS</span>
                            <select class="form-control input-sm form-border text-uppercase" name="status1" id=""
                                required>
                                <option value="">--Pilih--</option>
                                <option value="Lancar" {{ $karakter->status1 == 'Lancar' ? 'selected' : '' }}>Lancar
                                </option>
                                <option value="Kurang Lancar"
                                    {{ $karakter->status1 == 'Kurang Lancar' ? 'selected' : '' }}>Kurang Lancar
                                </option>
                                <option value="Diragukan" {{ $karakter->status1 == 'Diragukan' ? 'selected' : '' }}>
                                    Diragukan</option>
                                <option value="Macet" {{ $karakter->status1 == 'Macet' ? 'selected' : '' }}>Macet
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="div-left" style="display: flex; justify-content: space-between; width:100%;">
                        <div style="margin-top:5px;width: 33%;">
                            <span class="fw-bold">KEWAJIBAN PIHAK LAIN</span>
                            <select class="form-control input-sm form-border text-uppercase" name="kewajiban2"
                                id="">
                                <option value="">--Pilih--</option>
                                <option value="Bank Umum" {{ $karakter->kewajiban2 == 'Bank Umum' ? 'selected' : '' }}>
                                    Bank
                                    Umum
                                </option>
                                <option value="BPR" {{ $karakter->kewajiban2 == 'BPR' ? 'selected' : '' }}>BPR
                                </option>
                                <option value="Koperasi" {{ $karakter->kewajiban2 == 'Koperasi' ? 'selected' : '' }}>
                                    Koperasi
                                </option>
                                <option value="Leasing" {{ $karakter->kewajiban2 == 'Leasing' ? 'selected' : '' }}>
                                    Leasing
                                </option>
                                <option value="lainnya" {{ $karakter->kewajiban2 == 'lainnya' ? 'selected' : '' }}>
                                    lainnya
                                </option>
                            </select>
                        </div>

                        <div style="margin-top:5px;width: 33%;">
                            <span class="fw-bold">KETERANGAN</span>
                            <input type="text" class="form-control input-sm form-border text-uppercase"
                                name="ket_kewajiban2" id="" placeholder="ENTRI"
                                value="{{ $karakter->ket_kewajiban2 }}">
                        </div>

                        <div style="margin-top:5px;width: 33%;">
                            <span class="fw-bold">STATUS</span>
                            <select class="form-control input-sm form-border text-uppercase" name="status2"
                                id="" required>
                                <option value="">--Pilih--</option>
                                <option value="Lancar" {{ $karakter->status2 == 'Lancar' ? 'selected' : '' }}>Lancar
                                </option>
                                <option value="Kurang Lancar"
                                    {{ $karakter->status2 == 'Kurang Lancar' ? 'selected' : '' }}>Kurang Lancar
                                </option>
                                <option value="Diragukan" {{ $karakter->status2 == 'Diragukan' ? 'selected' : '' }}>
                                    Diragukan</option>
                                <option value="Macet" {{ $karakter->status2 == 'Macet' ? 'selected' : '' }}>Macet
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="div-left" style="display: flex; justify-content: space-between; width:100%;">
                        <div style="margin-top:5px;width: 33%;">
                            <span class="fw-bold">KEWAJIBAN PIHAK LAIN</span>
                            <select class="form-control input-sm form-border text-uppercase" name="kewajiban3"
                                id="">
                                <option value="">--Pilih--</option>
                                <option value="Bank Umum" {{ $karakter->kewajiban3 == 'Bank Umum' ? 'selected' : '' }}>
                                    Bank Umum
                                </option>
                                <option value="BPR" {{ $karakter->kewajiban3 == 'BPR' ? 'selected' : '' }}>BPR
                                </option>
                                <option value="Koperasi" {{ $karakter->kewajiban3 == 'Koperasi' ? 'selected' : '' }}>
                                    Koperasi
                                </option>
                                <option value="Leasing" {{ $karakter->kewajiban3 == 'Leasing' ? 'selected' : '' }}>
                                    Leasing
                                </option>
                                <option value="lainnya" {{ $karakter->kewajiban3 == 1 ? 'selected' : '' }}>lainnya
                                </option>
                            </select>
                        </div>

                        <div style="margin-top:5px;width: 33%;">
                            <span class="fw-bold">KETERANGAN</span>
                            <input type="text" class="form-control input-sm form-border text-uppercase"
                                name="ket_kewajiban3" id="" placeholder="ENTRI"
                                value="{{ $karakter->ket_kewajiban3 }}">
                        </div>

                        <div style="margin-top:5px;width: 33%;">
                            <span class="fw-bold">STATUS</span>
                            <select class="form-control input-sm form-border text-uppercase" name="status3"
                                id="" required>
                                <option value="">--Pilih--</option>
                                <option value="Lancar" {{ $karakter->status3 == 'Lancar' ? 'selected' : '' }}>Lancar
                                </option>
                                <option value="Kurang Lancar"
                                    {{ $karakter->status3 == 'Kurang Lancar' ? 'selected' : '' }}>Kurang Lancar
                                </option>
                                <option value="Diragukan" {{ $karakter->status3 == 'Diragukan' ? 'selected' : '' }}>
                                    Diragukan</option>
                                <option value="Macet" {{ $karakter->status3 == 'Macet' ? 'selected' : '' }}>Macet
                                </option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-sm btn-primary"
                        style="margin-top:10px;width:100%">SIMPAN</button>
                </div>
            </form>

        </div>
    </div>
@endsection

@push('myscript')
    <script src="{{ asset('assets/js/myscript/kualitatif.js') }}"></script>

    <script>
        $(function() {
            //Timepicker
            $('.pemohon_ada').timepicker({
                showMeridian: false, // Menonaktifkan AM/PM
                minuteStep: 1, // Menampilkan detik
                secondStep: 1, // Menampilkan detik
                showInputs: false
            })

            $('.pendamping_ada').timepicker({
                showMeridian: false, // Menonaktifkan AM/PM
                minuteStep: 1, // Menampilkan detik
                secondStep: 1, // Menampilkan detik
                showInputs: false
            })
        })
    </script>
@endpush
