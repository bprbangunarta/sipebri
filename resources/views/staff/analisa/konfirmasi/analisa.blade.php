@extends('staff.analisa.konfirmasi.menu', [$data, 'pengajuan' => $data->kd_pengajuan])
@section('title', 'Konfirmasi Analisa')

@section('content')
    <div class="tab-content">
        <div class="tab-pane active">

            <form action="{{ route('konfirmasi.ubah_analisa', ['pengajuan' => $data->kd_pengajuan]) }}" method="post">
                @csrf
                <div class="box-body" style="margin-top: -10px;">

                    <input type="text" name="" id="" value="KEPUTUSAN KOMITE" hidden>
                    <div class="alert alert-warning">
                        APAKAH ANDA YAKIN PENGISIAN ANALISA PEMBERIAN KREDIT SUDAH BENAR? <br>
                        JIKA <b>IYA</b>, TEKAN TOMBOL KONFIRMASI DIBAWAH INI!
                    </div>


                    <button type="submit" class="btn btn-sm btn-primary"
                        style="margin-top:10px;width:100%">KONFIRMASI</button>
                </div>
            </form>

        </div>
    </div>
@endsection

@push('myscript')
    <script src="{{ asset('assets/js/myscript/tambahan.js') }}"></script>
@endpush
