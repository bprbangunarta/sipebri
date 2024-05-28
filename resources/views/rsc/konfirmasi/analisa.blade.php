@extends('rsc.konfirmasi.menu', [$data])
@section('title', 'Konfirmasi Analisa RSC')

@section('content')
    <div class="tab-content">
        <div class="tab-pane active">

            <form action="{{ route('rsc.konfirmasi.update', ['rsc' => $data->rsc]) }}" method="post">
                @method('post')
                @csrf
                <div class="box-body" style="margin-top: -10px;">

                    <input type="text" name="" id="" value="KEPUTUSAN KOMITE" hidden>
                    <div class="alert alert-warning">
                        APAKAH ANDA YAKIN PENGISIAN ANALISA RSC SUDAH BENAR? <br>
                        JIKA <b>IYA</b>, TEKAN TOMBOL KONFIRMASI DIBAWAH INI!
                    </div>


                    <button type="submit" class="btn btn-sm btn-primary"
                        style="margin-top:10px;width:100%">KONFIRMASI</button>
                </div>
            </form>
        </div>

        <p class="text-red" style="margin-top:10px;margin-left:10px;">
            *Perubahan analisa <b>RSC</b> tanpa persetujuan ulang, tidak perlu melakukan konfirmasi.<br>
        </p>
    </div>
@endsection

@push('myscript')
    <script src="{{ asset('assets/js/myscript/tambahan.js') }}"></script>
@endpush
