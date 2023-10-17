{{-- @extends('staff.analisa.kualitatif.menu', [$data, 'pengajuan' => $data->kd_pengajuan]) --}}
@extends('staff.analisa.memorandum.menu')
@section('title', 'Analisa Memorandum')

@section('content')
    <div class="tab-content">
        <div class="tab-pane active">

            <form action="{{ route('analisa5c.simpancharacter') }}" method="post">
                @csrf
                <div class="box-body" style="margin-top: -10px;font-size:12px;">

                    <div class="div-left">
                        <div style="width: 100%;float:left;">
                            <span class="fw-bold">SIFAT</span>
                            <select class="form-control input-sm form-border text-uppercase bi_sifat_kode" name="bi_sifat_kode" id="" required>
                                <option value="">--Pilih--</option>
                                {{-- Ambil data pada tabel bi_sifat --}}
                                <option value="1">1 - Pembiayaan bersama Sindikasi</option>
                                <option value="2">2 - Disalurkan melalui Bank lain</option>
                            </select>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">PENGGUNAAN DEBITUR</span>
                            <select class="form-control input-sm form-border text-uppercase bi_penggunaan_kode" name="bi_penggunaan_kode" id="" required>
                                <option value="">--Pilih--</option>
                                {{-- Ambil data pada tabel bi_penggunaan_debitur --}}
                                <option value="10">10 - Modal Kerja</option>
                                <option value="20">20 - Investasi</option>
                            </select>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">GOLONGAN PENJAMIN</span>
                            <select class="form-control input-sm text-uppercase bi_gol_penjamin_kode" name="bi_gol_penjamin_kode" id="" required>
                                <option value="">--Pilih--</option>
                                {{-- Ambil data pada tabel bi_golongan_penjamin --}}
                                <option value="000">000 - Tanpa Penjamin</option>
                                <option value="880">880 - Asuransi Jiwa</option>
                            </select>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">BY FIDUCIA (%)</span>
                            <input type="text" class="form-control text-uppercase" name="ket_kewajiban1" id="" placeholder="00.00%">
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">BAGIAN YANG DIJAMIN (%)</span>
                            <input type="text" class="form-control text-uppercase" name="ket_kewajiban1" id="" placeholder="00.00%">
                        </div>
                    </div>


                    <div class="div-right">
                        <div style="width: 49.5%;float:left;">
                            <span class="fw-bold">SUMBER DANA PELUNASAN</span>
                            <select class="form-control input-sm form-border text-uppercase bi_sumber_pelunasan_kode" name="bi_sumber_pelunasan_kode" id="" required>
                                <option value="">--Pilih--</option>
                                {{-- Ambil data pada tabel bi_sumber_dana_pelunasan --}}
                                <option value="10">10 - Gaji / Honor</option>
                                <option value="21">21 - Usaha Subsidi</option>
                            </select>
                        </div>

                        <div style="width: 49.5%;float:right;">
                            <span class="fw-bold">JENIS USAHA</span>
                            <select class="form-control input-sm form-border text-uppercase bi_jenis_usaha_kode" name="bi_jenis_usaha_kode" id="" required>
                                <option value="">--Pilih--</option>
                                {{-- Ambil data pada tabel bi_jenis_usaha --}}
                                <option value="1">1 - Mikro</option>
                                <option value="2">2 - Kecil</option>
                            </select>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">SEKTOR EKONOMI</span>
                            <select class="form-control input-sm form-border text-uppercase bi_sek_ekonomi_kode" name="bi_sek_ekonomi_kode" id="" required>
                                <option value="">--Pilih--</option>
                                {{-- Ambil data pada tabel bi_sektor_ekonomi --}}
                                <option value="1001">1001 - Pertanian, Perburuan dan Kehutanan</option>
                                <option value="1008">1008 - Penyediaan Akomodasi dan Panyediaan Makan Minum</option>
                            </select>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">SEKTOR EKONOMI (SLIK)</span>
                            <select class="form-control input-sm form-border text-uppercase bi_sek_ekonomi_slik" name="bi_sek_ekonomi_slik" id="" required>
                                <option value="">--Pilih--</option>
                                {{-- Ambil data pada tabel bi_sektor_ekonimi_slik --}}
                                <option value="011110">011110 - Pertanian Padi</option>
                                <option value="011121">011121 - Pertanian Palawija Jagung</option>
                            </select>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">GOLONGAN DEBITUR</span>
                            <select class="form-control input-sm form-border text-uppercase bi_gol_debitur_kode" name="bi_gol_debitur_kode" id="" required>
                                <option value="">--Pilih--</option>
                                {{-- Ambil data pada tabel bi_golongan_debitur --}}
                                <option value="800">800 - Pemerintah Pusat</option>
                                <option value="805">805 - Pemerintah Daerah</option>
                            </select>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">GOLONGAN DEBITUR (SLIK)</span>
                            <select class="form-control input-sm form-border text-uppercase bi_gol_debitur_slik" name="bi_gol_debitur_slik" id="" required>
                                <option value="">--Pilih--</option>
                                {{-- Ambil data pada tabel bi_golongan_debitur_slik --}}
                                <option value="0010">0010 - Kantor Perbendaharaan dan Kas Negara (KPKN)</option>
                                <option value="0020">0020 - Departemen Keuangan</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-sm btn-primary" style="margin-top:10px;width:100%">SIMPAN</button>
                </div>
            </form>

        </div>
    </div>
@endsection

@push('myscript')
<script src="{{ asset('assets/js/myscript/kualitatif.js') }}"></script>

<script>
    $(function () {
        //Initialize Select2 Elements
      $('.bi_sifat_kode').select2()
      $('.bi_penggunaan_kode').select2()
      $('.bi_gol_penjamin_kode').select2()
      $('.bi_sumber_pelunasan_kode').select2()
      $('.bi_jenis_usaha_kode').select2()
      $('.bi_sek_ekonomi_kode').select2()
      $('.bi_sek_ekonomi_slik').select2()
      $('.bi_gol_debitur_kode').select2()
      $('.bi_gol_debitur_slik').select2()
    })
</script>
@endpush
