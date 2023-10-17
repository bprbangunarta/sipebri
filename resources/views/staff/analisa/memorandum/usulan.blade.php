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
                            <span class="fw-bold">MAX PLAFOND</span>
                            <input type="text" class="form-control text-uppercase" name="ket_kewajiban1" id="" value="RP." readonly>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">USULAN PLAFOND</span>
                            <select class="form-control input-sm form-border text-uppercase bi_penggunaan_kode" name="bi_penggunaan_kode" id="">
                                <option value="">--Pilih--</option>
                                {{-- Ambil data pada tabel bi_penggunaan_debitur --}}
                                <option value="10">10 - Modal Kerja</option>
                                <option value="20">20 - Investasi</option>
                            </select>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">JANGKA WAKTU</span>
                            <select class="form-control input-sm text-uppercase bi_gol_penjamin_kode" name="bi_gol_penjamin_kode" id="">
                                <option value="">--Pilih--</option>
                                {{-- Ambil data pada tabel bi_golongan_penjamin --}}
                                <option value="000">000 - Tanpa Penjamin</option>
                                <option value="880">880 - Asuransi Jiwa</option>
                            </select>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">REPAYMENT CAPACITY</span>
                            <input type="text" class="form-control text-uppercase" name="ket_kewajiban1" id="" value="00.00%" readonly>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">TAKSASI AGUNAN</span>
                            <input type="text" class="form-control text-uppercase" name="ket_kewajiban1" id="" value="00.00%" readonly>
                        </div>
                    </div>


                    <div class="div-right">
                        <div style="width: 100%;float:left;">
                            <span class="fw-bold">SEBELUM REALISASI</span>
                            <input type="text" class="form-control text-uppercase" name="ket_kewajiban1" id="" placeholder="ENTRI">
                        </div>

                        <div style="margin-top:5px;width: 100%;float:right;">
                            <span class="fw-bold">SYARAT TAMBAHAN</span>
                            <input type="text" class="form-control text-uppercase" name="ket_kewajiban1" id="" placeholder="ENTRI">
                        </div>

                        <div style="margin-top:5px;width: 100%;float:left;">
                            <span class="fw-bold">SYARAT LAINNYA</span>
                            <input type="text" class="form-control text-uppercase" name="ket_kewajiban1" id="" placeholder="ENTRI">
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
