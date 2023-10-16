@extends('staff.analisa.5c.menu')
@section('title', 'Analisa Agunan')

@section('content')
<div class="tab-content">
    <div class="tab-pane active">

        <form action="">
            @csrf
            <div class="box-body" style="margin-top: -10px;font-size:12px;">

                <div class="div-left">
                    <div style="width: 49.5%;float:left;">
                        <span class="fw-bold">KEPEMILIKAN AGUNAN UTAMA</span>
                        <select class="form-control input-sm form-border text-uppercase" name="agunan_utama" id="collateral1" required>
                            <option value="">--Pilih--</option>
                            <option value="3" {{ old('agunan_utama') == 3 ? 'selected' : '' }}>Milik Sendiri</option>
                            <option value="1" {{ old('agunan_utama') == 1 ? 'selected' : '' }}>Orang Lain/Milik Sendiri dan Orang Lain (Wariasan)</option>
                        </select>
                    </div>

                    <div style="width: 49.5%;float:right;">
                        <span class="fw-bold">LEGALITAS AGUNAN UTAMA</span>
                        <select class="form-control input-sm form-border text-uppercase" name="legalitas_agunan" id="collateral3" required>
                            <option value="">--Pilih--</option>
                            <option value="3" {{ old('legalitas_agunan') == 3 ? 'selected' : '' }}>Milik Sendiri</option>
                            <option value="1" {{ old('legalitas_agunan') == 1 ? 'selected' : '' }}>Orang Lain/Milik Sendiri dan Orang Lain (Wariasan)</option>
                        </select>
                    </div>

                    <div style="margin-top:5px;width: 49.5%;float:left;">
                        <span class="fw-bold">MUDAH DIUANGKAN</span>
                        <select class="form-control input-sm form-border text-uppercase" name="mudah_diuangkan" id="collateral5" required>
                            <option value="">--Pilih--</option>
                            <option value="3" {{ old('mudah_diuangkan') == 3 ? 'selected' : '' }}>Deposito,Tabungan, Emas</option>
                            <option value="2" {{ old('mudah_diuangkan') == 2 ? 'selected' : '' }}>BPKB, SHM</option>
                            <option value="1" {{ old('mudah_diuangkan') == 1 ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>

                    <div style="margin-top:5px;width: 49.5%;float:right;">
                        <span class="fw-bold">KONDISI KENDARAAN</span>
                        <select class="form-control input-sm form-border text-uppercase" name="kondisi_kendaraan" id="collateral7" required>
                            <option value="">--Pilih--</option>
                            <option value="3" {{ old('kondisi_kendaraan') == 3 ? 'selected' : '' }}>Original, Lengkap, Tidak Cacat</option>
                            <option value="2" {{ old('kondisi_kendaraan') == 2 ? 'selected' : '' }}>Original, Tidak Lengkap</option>
                            <option value="1" {{ old('kondisi_kendaraan') == 1 ? 'selected' : '' }}>Tidak Original, Tidak Lengkap, Cacat</option>
                        </select>
                    </div>

                    <div style="margin-top:5px;width: 49.5%;float:left;">
                        <span class="fw-bold">PENGIKATAN / ASPEK HUKUM</span>
                        <select class="form-control input-sm form-border text-uppercase" name="aspek_hukum" id="collateral9" required>
                            <option value="">--Pilih--</option>
                            <option value="4" {{ old('aspek_hukum') == 5 ? 'selected' : '' }}>
                                Emas dan deposito/tabungan yang saldonya di blokir dan dilengkapi dengan surat kuasa pencairan
                            </option>
                            <option value="4" {{ old('aspek_hukum') == 4 ? 'selected' : '' }}>
                                SHM (dilengkapi dengan SPPT tahun berjalan atau 1 tahun yang lalu) diikat dengan hak tanggungan / BPKB (Kendaraan) diikat dengan fidusia
                            </option>
                            <option value="3" {{ old('aspek_hukum') == 3 ? 'selected' : '' }}>
                                SHM (dilengkapi dengan SPPT tahun berjalan atau 1 tahun yang lalu) / BPKB tanpa pengikatan
                            </option>
                            <option value="2" {{ old('aspek_hukum') == 2 ? 'selected' : '' }}>
                                AJB / SPOP (dilengkapi dengan SPPT tahun berjalan atau 1 tahun yang lalu) tanpa pengikatan hak
                            </option>
                            <option value="1" {{ old('aspek_hukum') == 1 ? 'selected' : '' }}>
                                Agunan lain yang tidak memenuhi syarat
                            </option>
                        </select>
                    </div>

                    <div style="margin-top:5px;width: 49.5%;float:right;">
                        <span class="fw-bold">KEPEMILIKAN AGUNAN TAMBAHAN</span>
                        <select class="form-control input-sm form-border text-uppercase" name="agunan_tambahan" id="collateral2" required>
                            <option value="">--Pilih--</option>
                            <option value="3" {{ old('agunan_tambahan') == 3 ? 'selected' : '' }}>Milik Sendiri</option>
                            <option value="1" {{ old('agunan_tambahan') == 1 ? 'selected' : '' }}>Orang Lain/Milik Sendiri dan Orang Lain (Wariasan)</option>
                        </select>
                    </div>
                </div>


                <div class="div-right">
                    <div style="width: 49.5%;float:left;">
                        <span class="fw-bold">LEGALITAS AGUNAN TAMBAHAN</span>
                        <select class="form-control input-sm form-border text-uppercase" name="legalitas_agunan_tambahan" id="collateral4" required>
                            <option value="">--Pilih--</option>
                            <option value="3" {{ old('legalitas_agunan_tambahan') == 3 ? 'selected' : '' }}>Milik Sendiri</option>
                            <option value="1" {{ old('legalitas_agunan_tambahan') == 1 ? 'selected' : '' }}>Orang Lain/Milik Sendiri dan Orang Lain (Wariasan)</option>
                        </select>
                    </div>

                    <div style="width: 49.5%;float:right;">
                        <span class="fw-bold">STABILITAS HARGA</span>
                        <select class="form-control input-sm form-border text-uppercase" name="stabilitas_harga" id="collateral6" required>
                            <option value="">--Pilih--</option>
                            <option value="3" {{ old('stabilitas_harga') == 3 ? 'selected' : '' }}>SHM</option>
                            <option value="2" {{ old('stabilitas_harga') == 2 ? 'selected' : '' }}>Deposito,Tabungan, Emas</option>
                            <option value="1" {{ old('stabilitas_harga') == 1 ? 'selected' : '' }}>BPKB</option>
                        </select>
                    </div>
                    
                    <div style="margin-top:5px;width: 49.5%;float:left;">
                        <span class="fw-bold">LOKASI SHM</span>
                        <select class="form-control input-sm form-border text-uppercase" name="lokasi_shm" id="collateral8" required>
                            <option value="">--Pilih--</option>
                            <option value="3" {{ old('lokasi_shm') == 3 ? 'selected' : '' }}>Strategis dan atau Produktif</option>
                            <option value="2" {{ old('lokasi_shm') == 2 ? 'selected' : '' }}>Strategis dan Produktif (Atau Sebaliknya)</option>
                            <option value="1" {{ old('lokasi_shm') == 1 ? 'selected' : '' }}>Kurang Strategis dan Kurang Produktif</option>
                        </select>
                    </div>

                    <div style="margin-top:5px;width: 49.5%;float:right;">
                        <span class="fw-bold">PERMOHONAN TAKSASI AGUNAN</span>
                        <input class="form-control input-sm form-border text-uppercase" name="taksasi_agunan" value="{{ old('taksasi_agunan') ?? (0) }}%" required readonly>
                    </div>

                    <div style="margin-top:5px;width: 100%;float:right;">
                        <span class="fw-bold">EVALUASI</span>
                        <input class="form-control input-sm form-border bg-blue" type="text" name="evaluasi_collateral" id="evaluasi_collateral" value="{{ old('evaluasi_collateral') ?? 'KOSONG' }}" readonly required>
                    </div>
                </div>

                <a href="#" class="btn btn-sm btn-primary" style="margin-top:10px;width:100%">SIMPAN</a>
            </div>
        </form>

    </div>
</div>
@endsection

@push('myscript')
<script src="{{ asset('assets/js/myscript/analisa5c.js') }}"></script>
@endpush