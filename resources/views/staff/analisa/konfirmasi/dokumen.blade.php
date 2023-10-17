{{-- @extends('staff.analisa.kualitatif.menu', [$data, 'pengajuan' => $data->kd_pengajuan]) --}}
@extends('staff.analisa.konfirmasi.menu')
@section('title', 'Konfirmasi Analisa')

@section('content')
    <div class="tab-content">
        <div class="tab-pane active">

            <form action="{{ route('analisa5c.simpancharacter') }}" method="post">
                @csrf
                <div class="box-body" style="margin-top: -10px;font-size:12px;">
                    <div style="overflow: auto; width: 100%; height: 435px;">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="bg-blue">
                                    <th class="text-center" rowspan="2" style="width: 10px;vertical-align:middle;">NO</th>
                                    <th class="text-center" rowspan="2" style="vertical-align:middle;">DAFTAR DOKUMEN</th>
                                    <th class="text-center" colspan="5">QC PETUGAS</th>
                                    <th class="text-center" rowspan="2" style="width: 150px;vertical-align:middle;">CATATAN</th>
                                </tr>
                                <tr class="bg-blue">
                                    <th class="text-center" style="width: 50px;">SAA</td>
                                    <th class="text-center" style="width: 50px;">KAA</td>
                                    <th class="text-center" style="width: 50px;">P.E</td>
                                    <th class="text-center" style="width: 50px;">REAL</td>
                                    <th class="text-center" style="width: 50px;">LEGAL</td>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Ambil data dari tabel data_dokumen --}}
                                <tr>
                                    <td class="text-center" style="vertical-align:middle;">1</td>
                                    <td class="text-uppercase" style="vertical-align:middle;">Perjanjian Kredit</td>
                                    <td class="text-center" style="vertical-align:middle;">
                                        <input type="checkbox" name="" id="">
                                    </td>
                                    <td class="text-center" style="vertical-align:middle;">
                                        <input type="checkbox" name="" id="">
                                    </td>
                                    <td class="text-center" style="vertical-align:middle;">
                                        <input type="checkbox" name="" id="">
                                    </td>
                                    <td class="text-center" style="vertical-align:middle;">
                                        <input type="checkbox" name="" id="">
                                    </td>
                                    <td class="text-center" style="vertical-align:middle;">
                                        <input type="checkbox" name="" id="">
                                    </td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <button type="submit" class="btn btn-sm btn-primary" style="margin-top:10px;width:100%">SIMPAN</button>
                </div>
            </form>

        </div>
    </div>
@endsection

@push('myscript')
<script src="{{ asset('assets/js/myscript/tambahan.js') }}"></script>
@endpush
