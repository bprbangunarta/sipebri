@extends('staff.analisa.u-pertanian.menu', [$data, 'kode_usaha' => $pertanian->kd_usaha, 'pengajuan' => $data->kd_pengajuan])
@section('title', 'Analisa Usaha Pertanian')

@section('content')
    <div class="tab-content">
        <div class="tab-pane active">

            <form action="{{ route('pertanian.simpankeuangan', ['kode_usaha' => $pertanian->kd_usaha]) }}" method="post">
                @csrf
                <div class="box-body" style="margin-top: -10px;font-size:12px;">

                    <div class="div-left">
                        <div style="width: 49.5%;float:left;">
                            <span class="fw-bold">PENDAPATAN HASIL PANEN</span>
                            <input class="form-control input-sm form-border" type="text" placeholder="Rp."
                                name="hasil_panen" id="hasil_panen"
                                value="{{ 'Rp. ' . ' ' . number_format($kalkulasi['pendapatan'], 0, ',', '.') ?? 0 }}"
                                readonly>
                        </div>
                        <div style="width: 49.5%;float:right;">
                            <span class="fw-bold">PENGELUARAN BIAYA USAHA</span>
                            <input class="form-control input-sm form-border" type="text" name="total_biaya"
                                placeholder="Rp." id="total_biaya"
                                value="{{ 'Rp. ' . ' ' . number_format($kalkulasi['pengeluaran'], 0, ',', '.') ?? 0 }}"
                                readonly>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">PENAMBAHAN HASIL USAHA</span>
                            <input class="form-control input-sm form-border" type="text" name="penambahan"
                                placeholder="Rp." id="penambahan"
                                value="{{ 'Rp. ' . ' ' . number_format($kalkulasi['penambahan'], 0, ',', '.') ?? 0 }}">
                        </div>
                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">PINJAMAN BANK LAIN</span>
                            <input class="form-control input-sm form-border" type="text" placeholder="Rp."
                                name="pinjaman_bank" id="pinjaman_bank"
                                value="{{ 'Rp. ' . ' ' . number_format($kalkulasi['pinjaman_bank'], 0, ',', '.') ?? 0 }}"
                                readonly>
                        </div>
                    </div>

                    <div class="div-right">
                        <div style="width: 49.5%;float:left;">
                            <span class="fw-bold">HASIL BERSIH USAHA</span>
                            <input class="form-control input-sm form-border" type="text"
                                value="{{ 'Rp. ' . ' ' . number_format($kalkulasi['laba_bersih'], 0, ',', '.') ?? 0 }}"
                                name="laba_bersih" id="laba_bersih" readonly>
                        </div>
                        <div style="width: 49.5%;float:right;">
                            <span class="fw-bold">AMBIL 70%</span>
                            <input class="form-control input-sm form-border" type="text"
                                value="{{ 'Rp. ' . ' ' . number_format($kalkulasi['ambil'], 0, ',', '.') ?? 0 }}"
                                name="ambil_70" id="ambil_70" readonly>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">ANGSURAN POKOK</span>
                            <input class="form-control input-sm form-border" type="text"
                                value="{{ 'Rp. ' . ' ' . number_format($kalkulasi['saving'], 0, ',', '.') ?? 0 }}"
                                name="saving_pokok" id="saving_pokok" readonly>
                        </div>
                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">PENDAPATAN PERBULAN</span>
                            <input value="{{ $kalkulasi['laba_perbulan'] }}" id="perbulan" hidden>
                            <input class="form-control input-sm form-border bg-blue" type="text"
                                value="{{ 'Rp. ' . ' ' . number_format($kalkulasi['laba_perbulan'], 0, ',', '.') ?? 0 }}"
                                name="laba_perbulan" id="lb_perbulan" readonly>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-sm btn-primary" style="margin-top:10px;width:100%">SIMPAN</button>
                </div>
            </form>


        </div>
    </div>
@endsection

@push('myscript')
    <script src="{{ asset('assets/js/myscript/input_penambahan_pertanian.js') }}"></script>
@endpush
