@extends('staff.analisa.5c.menu')
@section('title', 'Analisa Karakter')

@section('content')
<div class="tab-content">
    <div class="tab-pane active">

        <form action="">
            @csrf
            <div class="box-body" style="margin-top: -10px;font-size:12px;">

                <div class="div-left">
                    <div style="width: 49.5%;float:left;">
                        <span class="fw-bold">KONTINUITAS USAHA</span>
                        <select class="form-control input-sm form-border text-uppercase" name="kontinuitas" id="capacity1" required>
                            <option value="">--Pilih--</option>
                            <option value="1" {{ old('kontinuitas') == 1 ? 'selected' : '' }}>Tidak Tentu</option>
                            <option value="3" {{ old('kontinuitas') == 3 ? 'selected' : '' }}>Terus Menerus</option>
                            <option value="2" {{ old('kontinuitas') == 2 ? 'selected' : '' }}>Kadang-Kadang</option>
                        </select>
                    </div>

                    <div style="width: 49.5%;float:right;">
                        <span class="fw-bold">PENGALAMAN USAHA</span>
                        <select class="form-control input-sm form-border text-uppercase" name="pengalaman_usaha" id="capacity3" required>
                            <option value="">--Pilih--</option>
                            <option value="1" {{ old('pengalaman_usaha') == '1' ? 'selected' : '' }}>0 Tahun</option>
                            <option value="2" {{ old('pengalaman_usaha') == '2' ? 'selected' : '' }}>&lt; 1 Tahun</option>
                            <option value="3" {{ old('pengalaman_usaha') == '3' ? 'selected' : '' }}>1 - 3 Tahun</option>
                            <option value="4" {{ old('pengalaman_usaha') == '4' ? 'selected' : '' }}>&gt; 3 - 5 Tahun</option>
                            <option value="5" {{ old('pengalaman_usaha') == '5' ? 'selected' : '' }}>&gt; 5 Tahun</option>
                        </select>
                    </div>
                    
                    <div style="margin-top:5px;width: 49.5%;float:left;">
                        <span class="fw-bold">PERTUMBUHAN USAHA</span>
                        <select class="form-control input-sm form-border text-uppercase" name="pertumbuhan_usaha" id="capacity5" required>
                            <option value="">--Pilih--</option>
                            <option value="2" {{ old('pertumbuhan_usaha') == 2 ? 'selected' : '' }}>Tetap</option>
                            <option value="1" {{ old('pertumbuhan_usaha') == 1 ? 'selected' : '' }}>Turun</option>
                            <option value="3" {{ old('pertumbuhan_usaha') == 3 ? 'selected' : '' }}>Meningkat</option>
                        </select>
                    </div>

                    <div style="margin-top:5px;width: 49.5%;float:right;">
                        <span class="fw-bold">CATATAN LAPORAN KEUANGAN</span>
                        <select class="form-control input-sm form-border text-uppercase" name="laporan_keuangan" id="capacity7" required>
                            <option value="">--Pilih--</option>
                            <option value="1" {{ old('laporan_keuangan') == '1' ? 'selected' : '' }}>Tidak Ada</option>
                            <option value="2" {{ old('laporan_keuangan') == '2' ? 'selected' : '' }}>Transaksi Harian</option>
                            <option value="3" {{ old('laporan_keuangan') == '3' ? 'selected' : '' }}>Mengumpulkan Bukti</option>
                        </select>
                    </div>

                    <div style="margin-top:5px;width: 100%;float:left;">
                        <span class="fw-bold">CATATAN KREDIT MASA LALU</span>
                        <select class="form-control input-sm form-border text-uppercase" name="catatan_kredit" id="capacity8" required>
                            <option value="">--Pilih--</option>
                            <option value="3" {{ old('catatan_kredit') == 3 ? 'selected' : '' }}>Lancar</option>
                            <option value="2" {{ old('catatan_kredit') == 2 ? 'selected' : '' }}>Menunggak > 2 Bulan</option>
                            <option value="1" {{ old('catatan_kredit') == '1' ? 'selected' : '' }}>Lancar Menunggak 2 Bulan</option>
                        </select>
                    </div>
                </div>


                <div class="div-right">
                    <div style="width: 49.5%;float:left;">
                        <span class="fw-bold">KONDISI SLIK</span>
                        <select class="form-control input-sm form-border text-uppercase" name="kondisi_slik" id="capacity2" required>
                            <option value="">--Pilih--</option>
                            <option value="3" {{ old('kondisi_slik') == 3 ? 'selected' : '' }}>Lancar</option>
                            <option value="2" {{ old('kondisi_slik') == 2 ? 'selected' : '' }}>Tidak Ada</option>
                            <option value="1" {{ old('kondisi_slik') == 1 ? 'selected' : '' }}>Tidak Baik</option>
                        </select>
                    </div>

                    <div style="width: 49.5%;float:right;">
                        <span class="fw-bold">ASET DILUAR USAHA</span>
                        <select class="form-control input-sm form-border text-uppercase" name="aset_diluar_usaha" id="capacity4" required>
                            <option value="">--Pilih--</option>
                            <option value="3" {{ old('aset_diluar_usaha') == 3 ? 'selected' : '' }}>Liquid</option>
                            <option value="2" {{ old('aset_diluar_usaha') == 2 ? 'selected' : '' }}>Cukup Liquid</option>
                            <option value="1" {{ old('aset_diluar_usaha') == 1 ? 'selected' : '' }}>Tidak Liquid</option>
                        </select>
                    </div>
                    
                    <div style="margin-top:5px;width: 49.5%;float:left;">
                        <span class="fw-bold">ASET TERKAIT USAHA</span>
                        <select class="form-control input-sm form-border text-uppercase" name="aset_terkait_usaha" id="capacity6" required>
                            <option value="">--Pilih--</option>
                            <option value="3" {{ old('aset_terkait_usaha') == 3 ? 'selected' : '' }}>Mengcover</option>
                            <option value="2" {{ old('aset_terkait_usaha') == 2 ? 'selected' : '' }}>Cukup Mengcover</option>
                            <option value="1" {{ old('aset_terkait_usaha') == 1 ? 'selected' : '' }}>Tidak Mengcover</option>
                        </select>
                    </div>

                    <div style="margin-top:5px;width: 49.5%;float:right;">
                        <span class="fw-bold">REPAYMENT CAPACITY</span>
                        <input class="form-control input-sm form-border text-uppercase" name="rc" value="{{ old('rc') ?? 0 }}%" readonly>
                    </div>

                    <div style="margin-top:5px;width: 100%;float:right;">
                        <span class="fw-bold">EVALUASI</span>
                        <input class="form-control input-sm form-border bg-blue" type="text" name="evaluasi_capacity" id="evaluasi_capacity"
                        value="{{ old('evaluasi_capacity') ?? 'KOSONG' }}" readonly required>
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