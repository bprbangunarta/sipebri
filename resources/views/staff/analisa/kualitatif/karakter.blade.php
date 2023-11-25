@extends('staff.analisa.kualitatif.menu', [$data, 'pengajuan' => $data->kd_pengajuan])
{{-- @extends('staff.analisa.kualitatif.menu') --}}
@section('title', 'Analisa Kualitatif')

@section('content')
    <div class="tab-content">
        <div class="tab-pane active">

            <form action="{{ route('kualitatif.simpankarakter', ['pengajuan' => $data->kd_pengajuan]) }}" method="post">
                @csrf
                <div class="box-body" style="margin-top: -10px;font-size:12px;">

                    <div class="div-left">
                        <div style="width: 100%;float:left;">
                            <span class="fw-bold">SLIK (SID BANK INDONESIA)</span>
                            <select class="form-control input-sm form-border text-uppercase" name="bi_checking"
                                id="" required>
                                <option value="">--Pilih--</option>
                                <option value="4" {{ old('bi_checking') == 4 ? 'selected' : '' }}>Lancar</option>
                                <option value="3" {{ old('bi_checking') == 3 ? 'selected' : '' }}>Kurang Lancar
                                </option>
                                <option value="2" {{ old('bi_checking') == 2 ? 'selected' : '' }}>Diragukan</option>
                                <option value="1" {{ old('bi_checking') == 1 ? 'selected' : '' }}>Macet</option>
                            </select>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">KEWAJIBAN PIHAK LAIN</span>
                            <select class="form-control input-sm form-border text-uppercase" name="kewajiban1"
                                id="">
                                <option value="">--Pilih--</option>
                                <option value="Bank Umum" {{ old('kewajiban1') == 'Bank Umum' ? 'selected' : '' }}>Bank Umum
                                </option>
                                <option value="BPR" {{ old('kewajiban1') == 'BPR' ? 'selected' : '' }}>BPR</option>
                                <option value="Koperasi" {{ old('kewajiban1') == 'Koperasi' ? 'selected' : '' }}>Koperasi
                                </option>
                                <option value="Leasing" {{ old('kewajiban1') == 'Leasing' ? 'selected' : '' }}>Leasing
                                </option>
                                <option value="lainnya" {{ old('kewajiban1') == 'lainnya' ? 'selected' : '' }}>lainnya
                                </option>
                            </select>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">KETERANGAN</span>
                            <input type="text" class="form-control input-sm form-border text-uppercase"
                                name="ket_kewajiban1" id="" placeholder="ENTRI">
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">KEWAJIBAN PIHAK LAIN</span>
                            <select class="form-control input-sm form-border text-uppercase" name="kewajiban2"
                                id="">
                                <option value="">--Pilih--</option>
                                <option value="Bank Umum" {{ old('ket_kewajiban2') == 'Bank Umum' ? 'selected' : '' }}>Bank
                                    Umum
                                </option>
                                <option value="BPR" {{ old('ket_kewajiban2') == 'BPR' ? 'selected' : '' }}>BPR</option>
                                <option value="Koperasi" {{ old('ket_kewajiban2') == 'Koperasi' ? 'selected' : '' }}>
                                    Koperasi
                                </option>
                                <option value="Leasing" {{ old('ket_kewajiban2') == 'Leasing' ? 'selected' : '' }}>Leasing
                                </option>
                                <option value="lainnya" {{ old('ket_kewajiban2') == 'lainnya' ? 'selected' : '' }}>lainnya
                                </option>
                            </select>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">KETERANGAN</span>
                            <input type="text" class="form-control input-sm form-border text-uppercase"
                                name="ket_kewajiban2" id="" placeholder="ENTRI">
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">KEWAJIBAN PIHAK LAIN</span>
                            <select class="form-control input-sm form-border text-uppercase" name="kewajiban3"
                                id="">
                                <option value="">--Pilih--</option>
                                <option value="Bank Umum" {{ old('kewajiban3') == 'Bank Umum' ? 'selected' : '' }}>Bank
                                    Umum</option>
                                <option value="BPR" {{ old('kewajiban3') == 'BPR' ? 'selected' : '' }}>BPR</option>
                                <option value="Koperasi" {{ old('kewajiban3') == 'Koperasi' ? 'selected' : '' }}>Koperasi
                                </option>
                                <option value="Leasing" {{ old('kewajiban3') == 'Leasing' ? 'selected' : '' }}>Leasing
                                </option>
                                <option value="lainnya" {{ old('kewajiban3') == 'lainnya' ? 'selected' : '' }}>lainnya
                                </option>
                            </select>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">KETERANGAN</span>
                            <input type="text" class="form-control input-sm form-border text-uppercase"
                                name="ket_kewajiban3" id="" placeholder="ENTRI">
                        </div>
                    </div>


                    <div class="div-right">
                        <div style="width: 49.5%;float:left;">
                            <span class="fw-bold">BERURUSAN DGN PIHAK BERWAJIB</span>
                            <select class="form-control input-sm form-border text-uppercase" name="pihak_berwajib"
                                id="" required>
                                <option value="">--Pilih--</option>
                                <option value="2" {{ old('pihak_berwajib') == 2 ? 'selected' : '' }}>Pernah</option>
                                <option value="1" {{ old('pihak_berwajib') == 1 ? 'selected' : '' }}>Tidak Pernah
                                </option>
                            </select>
                        </div>

                        <div style="width: 49.5%;float:right;">
                            <span class="fw-bold">HUBUNGAN DENGAN TETANGGA</span>
                            <select class="form-control input-sm form-border text-uppercase" name="hubungan_tetangga"
                                id="" required>
                                <option value="">--Pilih--</option>
                                <option value="Baik" {{ old('hubungan_tetangga') == 3 ? 'selected' : '' }}>Baik</option>
                                <option value="Cukup Baik" {{ old('hubungan_tetangga') == 2 ? 'selected' : '' }}>Cukup Baik
                                </option>
                                <option value="Kurang Baik" {{ old('hubungan_tetangga') == 1 ? 'selected' : '' }}>Kurang
                                    Baik</option>
                            </select>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">PENGALAMAN MENJADI TKI</span>
                            <select class="form-control input-sm form-border text-uppercase" name="pengalaman_tki"
                                id="">
                                <option value="">--Pilih--</option>
                                <option value="Pernah" {{ old('pengalaman_tki') == 5 ? 'selected' : '' }}>Pernah</option>
                                <option value="Tidak Pernah" {{ old('pengalaman_tki') == 4 ? 'selected' : '' }}>Tidak
                                    Pernah</option>
                            </select>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">KETERANGAN</span>
                            <select class="form-control input-sm form-border text-uppercase" name="ket_pengalaman"
                                id="">
                                <option value="">--Pilih--</option>
                                <option value="Pemohon" {{ old('ket_pengalaman') == 5 ? 'selected' : '' }}>Pemohon
                                </option>
                                <option value="Pendamping" {{ old('ket_pengalaman') == 4 ? 'selected' : '' }}>Pendamping
                                </option>
                            </select>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">PEMOHON WAKTU DI RUMAH</span>
                            <input type="text" class="form-control input-sm form-border text-uppercase pemohon_ada"
                                name="pemohon_ada" id="" placeholder="00:00 AM">
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">PENDAMPING WAKTU DI RUMAH</span>
                            <input type="text" class="form-control input-sm form-border text-uppercase pendamping_ada"
                                name="pendamping_ada" id="" placeholder="00:00 AM">
                        </div>

                        <div style="margin-top:5px;width: 100%;float:right;">
                            <span class="fw-bold">INFO MASYARAKAT</span>
                            <input type="text" class="form-control input-sm form-border text-uppercase"
                                name="info_masyarakat" id="" placeholder="ENTRI">
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
