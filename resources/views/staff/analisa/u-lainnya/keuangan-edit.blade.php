@extends('staff.analisa.u-lainnya.menu', [$data, 'pengajuan' => $data->kd_pengajuan, 'kode_usaha' => $lain->kd_usaha])
@section('title', 'Analisa Usaha Lainnya')

@section('content')
    <div class="tab-content">
        <div class="tab-pane active">

            <form action="{{ route('lain.updatekeuangan', ['kode_usaha' => $lain->kd_usaha]) }}" id="keuangan_lainnya"
                method="post">
                @method('put')
                @csrf
                <div class="box-body" style="margin-top: -10px;font-size:12px;">

                    <div class="div-left">
                        @foreach ($pendapatan as $item)
                            <div style="width: 49.5%; float: left;">
                                <span class="fw-bold">NAMA PENDAPATAN</span>
                                <input type="text" name="kode{{ $loop->iteration }}" value="{{ $item->kode_lain }}"
                                    hidden>
                                <input class="form-control input-sm form-border" type="text" placeholder="ENTRI"
                                    name="nama{{ $loop->iteration }}" value="{{ $item->penjualan }}">
                            </div>
                            <div style="width: 49.5%; float: right;">
                                <span class="fw-bold">NOMINAL PENDAPATAN</span>
                                <input class="form-control input-sm form-border" type="text" placeholder="Rp."
                                    name="nominal{{ $loop->iteration }}" id="nominal{{ $loop->iteration }}"
                                    value="{{ 'Rp. ' . ' ' . number_format($item->nominal, 0, ',', '.') }}">
                            </div>
                        @endforeach

                        @foreach ($biaya as $item)
                            <div style="width: 49.5%;float:left;">
                                <span class="fw-bold text-red">PENGELUARAN UNTUK</span>
                                <input type="text" name="kod{{ $loop->iteration }}" value="{{ $item->kode_lain }}"
                                    hidden>
                                <input class="form-control input-sm form-border" type="text" placeholder="ENTRI"
                                    name="nampe{{ $loop->iteration }}" value="{{ $item->pengeluaran }}">
                            </div>
                            <div style="width: 49.5%;float:right;">
                                <span class="fw-bold text-red">NOMINAL PENGELUARAN</span>
                                <input class="form-control input-sm form-border" type="text" placeholder="Rp."
                                    name="pengeluaran{{ $loop->iteration }}" id="pengeluaran{{ $loop->iteration }}"
                                    value="{{ 'Rp. ' . ' ' . number_format($item->nominal, 0, ',', '.') }}">
                            </div>
                        @endforeach
                    </div>

                    <div class="div-right">
                        <div>
                            <span class="fw-bold">PENDAPATAN USAHA</span>
                            <input class="form-control input-sm form-border" type="text"
                                value="{{ 'Rp. ' . ' ' . number_format($lain->pendapatan, 0, ',', '.') }}"
                                name="pendapatan" id="penus" readonly>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">BIAYA OPERASIONAL</span>
                            <input class="form-control input-sm form-border" type="text"
                                value="{{ 'Rp. ' . ' ' . number_format($lain->pengeluaran, 0, ',', '.') }}"
                                name="pengeluaran" id="biayaop" readonly>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">BIAYA BAHAN BAKU</span>
                            <input class="form-control input-sm form-border" type="text"
                                value="{{ 'Rp. ' . ' ' . number_format($lain->biaya_bahan, 0, ',', '.') }}"
                                name="bahan_baku" id="bahan_baku" readonly>
                        </div>

                        <div style="margin-top:5px;width: 100%;float:left;">
                            <span class="fw-bold">PROYEKSI PENAMBAHAN</span>
                            <input class="form-control input-sm form-border" type="text" placeholder="Rp."
                                name="proyeksi" value="{{ 'Rp. ' . ' ' . number_format($lain->proyeksi, 0, ',', '.') }}"
                                id="pph">
                        </div>

                        <div style="margin-top:5px;width: 100%;float:left;">
                            <span class="fw-bold">HASIL USAHA BERSIH</span>
                            <input class="form-control input-sm form-border bg-blue" type="text"
                                value="{{ 'Rp. ' . ' ' . number_format($lain->laba_bersih, 0, ',', '.') }}"
                                name="laba_bersih" id="hasilbersih">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-sm btn-primary" style="margin-top:10px;width:100%"
                        id="submit-keuangan-lainnya">SIMPAN</button>
                </div>
            </form>

        </div>
    </div>

@endsection

@push('myscript')
    <script src="{{ asset('assets/js/myscript/keuangan_lain.js') }}"></script>
    <script>
        //Hold Submit Ketika diklik 2X
        $('#keuangan_lainnya').submit(function(event) {
            var submitButton = $('#submit-keuangan-lainnya');
            submitButton.prop('disabled', true);
        });
    </script>
@endpush
