@extends('staff.analisa.u-jasa.menu', [$data, 'pengajuan' => $data->kd_pengajuan])
@section('title', 'Analisa Usaha Jasa')

@section('content')
    <div class="tab-content">
        <div class="tab-pane active">

            <form action="{{ route('usahajasa.simpankeuangan', ['kode_usaha' => $jasa->kd_usaha]) }}" method="post">
                @csrf
                <div class="box-body" style="margin-top: -10px;font-size:12px;">

                    <div class="div-left">
                        {{-- <input type="text" value="{{ $jasa->kd_usaha }}" name="kode_usaha" hidden> --}}

                        <div>
                            <span class="fw-bold">KODE USAHA</span>
                            <input class="form-control input-sm form-border" type="text" name="kode_usaha"
                                value="{{ $jasa->kode_usaha }}" readonly>
                        </div>
                        <div style="margin-top: 5px;">
                            <span class="fw-bold">NAMA USAHA</span>
                            <input class="form-control input-sm form-border" type="text" name="nama_usaha" id=""
                                placeholder="NAMA USAHA" value="{{ $jasa->nama_usaha }}">
                        </div>

                        <div style="margin-top: 5px;">
                            <span class="fw-bold">LAMA USAHA</span>
                            <select class="form-control input-sm form-border" name="lama_usaha" id="">
                                @if (is_null($jasa->lama_usaha))
                                    <option value="">--PILIH--</option>
                                @else
                                    <option value="{{ $jasa->lama_usaha }}">{{ $jasa->lama_usaha }}</option>
                                @endif

                                <option value="1 TAHUN" {{ old('lama_usaha') == '1 Tahun' ? 'selected' : '' }}>1 TAHUN
                                </option>
                                <option value="2 TAHUN" {{ old('lama_usaha') == '2 Tahun' ? 'selected' : '' }}>2 TAHUN
                                </option>
                                <option value="3 TAHUN" {{ old('lama_usaha') == '3 Tahun' ? 'selected' : '' }}>3 TAHUN
                                </option>
                                <option value="4 TAHUN" {{ old('lama_usaha') == '4 Tahun' ? 'selected' : '' }}>4 TAHUN
                                </option>
                                <option value=">5 TAHUN" {{ old('lama_usaha') == '5 Tahun' ? 'selected' : '' }}>> 5 TAHUN
                                </option>
                            </select>
                        </div>
                        <div style="margin-top: 5px;">
                            <span class="fw-bold">ALAMAT USAHA</span>
                            <input class="form-control input-sm form-border" type="text" name="lokasi_usaha"
                                value="{{ $jasa->lokasi_usaha ?? null }}">
                        </div>

                        <div style="margin-top: 5px;">
                            <span class="fw-bold">PENDAPATAN USAHA</span>
                            <input class="form-control input-sm form-border" type="text" name="pendapatan"
                                id="pendapatan" placeholder="Rp. "
                                value="{{ 'Rp. ' . ' ' . number_format($jasa->pendapatan, 0, ',', '.') ?? 0 }}">
                        </div>
                    </div>

                    <div class="div-right">
                        <div>
                            <span class="fw-bold">PAJAK KENDARAAN</span>
                            <input class="form-control input-sm form-border" type="text" name="b_pajak" id="pajak"
                                placeholder="Rp."
                                value="{{ 'Rp. ' . ' ' . number_format($jasa->b_pajak, 0, ',', '.') ?? 0 }}">
                        </div>
                        <div style="margin-top: 5px;">
                            <span class="fw-bold">PENGELUARAN LAINNYA</span>
                            <input class="form-control input-sm form-border" type="text" name="b_lainnya" id="lainnya"
                                placeholder="Rp. "
                                value="{{ 'Rp. ' . ' ' . number_format($jasa->b_lainnya, 0, ',', '.') ?? 0 }}">
                        </div>

                        <div style="margin-top: 5px;">
                            <span class="fw-bold">TOTAL PENGHASILAN</span>
                            <input class="form-control input-sm form-border" type="text" name="totalpenghasilan"
                                id="tpenghasilan" value="Rp. " readonly>
                        </div>
                        <div style="margin-top: 5px;">
                            <span class="fw-bold">TOTAL PENGELUARAN</span>
                            <input class="form-control input-sm form-border" type="text" name="pengeluaran"
                                id="tpengeluaran"
                                value="{{ 'Rp. ' . ' ' . number_format($jasa->pengeluaran, 0, ',', '.') ?? 0 }}" readonly>
                        </div>

                        <div style="margin-top: 5px;">
                            <span class="fw-bold">HASIL USAHA BERSIH</span>
                            <input class="form-control input-sm form-border bg-blue" type="text"
                                value="{{ 'Rp. ' . ' ' . number_format($jasa->laba_bersih, 0, ',', '.') ?? 0 }}"
                                name="laba_bersih" id="laba">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-sm btn-primary" style="margin-top:10px;width:100%">SIMPAN</button>
                </div>
            </form>

        </div>
    </div>
@endsection

@push('myscript')
    <script src="{{ asset('assets/js/myscript/keuangan_jasa.js') }}"></script>
@endpush
