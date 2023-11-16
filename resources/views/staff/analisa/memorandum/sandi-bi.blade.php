@extends('staff.analisa.memorandum.menu', [$data, 'pengajuan' => $data->kd_pengajuan])
@section('title', 'Analisa Memorandum')

@section('content')
    <div class="tab-content">
        <div class="tab-pane active">

            <form action="{{ route('memorandum.simpansandi', ['pengajuan' => $data->kd_pengajuan]) }}" method="post">
                @csrf
                <div class="box-body" style="margin-top: -10px;font-size:12px;">

                    <div class="div-left">
                        <div style="width: 100%;float:left;">
                            <span class="fw-bold">SIFAT</span>
                            <select class="form-control input-sm form-border text-uppercase bi_sifat_kode"
                                name="bi_sifat_kode" id="" required>
                                <option value="">--Pilih--</option>
                                {{-- Ambil data pada tabel bi_sifat --}}
                                @foreach ($sifat as $item)
                                    <option value="{{ $item->sandi }}">
                                        {{ $item->sandi . ' ' . '-' . ' ' . $item->keterangan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div style="margin-top:5px;width: 100%;float:left;">
                            <span class="fw-bold">SEKTOR EKONOMI</span>
                            <select class="form-control input-sm form-border text-uppercase bi_sek_ekonomi_kode"
                                name="bi_sek_ekonomi_kode" id="" required>
                                <option value="">--Pilih--</option>
                                {{-- Ambil data pada tabel bi_sektor_ekonomi --}}
                                @foreach ($sektor as $item)
                                    <option value="{{ $item->sandi }}">
                                        {{ $item->sandi . ' ' . '-' . ' ' . $item->keterangan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div style="margin-top:5px;width: 100%;float:right;">
                            <span class="fw-bold">SEKTOR EKONOMI (SLIK)</span>
                            <select class="form-control input-sm form-border text-uppercase bi_sek_ekonomi_slik"
                                name="bi_sek_ekonomi_slik" id="" required>
                                <option value="">--Pilih--</option>
                                {{-- Ambil data pada tabel bi_sektor_ekonimi_slik --}}
                                @foreach ($slik as $item)
                                    <option value="{{ $item->sandi }}">
                                        {{ $item->sandi . ' ' . '-' . ' ' . $item->keterangan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>                        

                        <input type="hidden" class="form-control text-uppercase" name="ket_kewajiban1" id="" placeholder="RP.">
                        <input type="hidden" class="form-control text-uppercase" name="ket_kewajiban2" id="" placeholder="00.00%">
                    </div>


                    <div class="div-right">
                        <div style="width: 49.5%;float:left;">
                            <span class="fw-bold">PENGGUNAAN DEBITUR</span>
                            <select class="form-control input-sm form-border text-uppercase bi_penggunaan_kode"
                                name="bi_penggunaan_kode" id="" required>
                                <option value="">--Pilih--</option>
                                {{-- Ambil data pada tabel bi_penggunaan_debitur --}}
                                @foreach ($debitur as $item)
                                    <option value="{{ $item->sandi }}">
                                        {{ $item->sandi . ' ' . '-' . ' ' . $item->keterangan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div style="width: 49.5%;float:right;">
                            <span class="fw-bold">GOLONGAN PENJAMIN</span>
                            <select class="form-control input-sm text-uppercase bi_gol_penjamin_kode"
                                name="bi_gol_penjamin_kode" id="" required>
                                <option value="">--Pilih--</option>
                                {{-- Ambil data pada tabel bi_golongan_penjamin --}}
                                @foreach ($golongan as $item)
                                    <option value="{{ $item->sandi }}">
                                        {{ $item->sandi . ' ' . '-' . ' ' . $item->keterangan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">SUMBER DANA PELUNASAN</span>
                            <select class="form-control input-sm form-border text-uppercase bi_sumber_pelunasan_kode"
                                name="bi_sumber_pelunasan_kode" id="" required>
                                <option value="">--Pilih--</option>
                                {{-- Ambil data pada tabel bi_sumber_dana_pelunasan --}}
                                @foreach ($sumber as $item)
                                    <option value="{{ $item->sandi }}">
                                        {{ $item->sandi . ' ' . '-' . ' ' . $item->keterangan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">JENIS USAHA</span>
                            <select class="form-control input-sm form-border text-uppercase bi_jenis_usaha_kode"
                                name="bi_jenis_usaha_kode" id="" required>
                                <option value="">--Pilih--</option>
                                {{-- Ambil data pada tabel bi_jenis_usaha --}}
                                @foreach ($jenis as $item)
                                    <option value="{{ $item->sandi }}">
                                        {{ $item->sandi . ' ' . '-' . ' ' . $item->keterangan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:left;">
                            <span class="fw-bold">GOLONGAN DEBITUR</span>
                            <select class="form-control input-sm form-border text-uppercase bi_gol_debitur_kode"
                                name="bi_gol_debitur_kode" id="" required>
                                <option value="">--Pilih--</option>
                                {{-- Ambil data pada tabel bi_golongan_debitur --}}
                                @foreach ($golongandebitur as $item)
                                    <option value="{{ $item->sandi }}">
                                        {{ $item->sandi . ' ' . '-' . ' ' . $item->keterangan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div style="margin-top:5px;width: 49.5%;float:right;">
                            <span class="fw-bold">GOLONGAN DEBITUR (SLIK)</span>
                            <select class="form-control input-sm form-border text-uppercase bi_gol_debitur_slik"
                                name="bi_gol_debitur_slik" id="" required>
                                <option value="">--Pilih--</option>
                                {{-- Ambil data pada tabel bi_golongan_debitur_slik --}}
                                @foreach ($golongandebiturslik as $item)
                                    <option value="{{ $item->sandi }}">
                                        {{ $item->sandi . ' ' . '-' . ' ' . $item->keterangan }}
                                    </option>
                                @endforeach
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
        $(function() {
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
