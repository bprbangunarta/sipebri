@extends('theme.app')
@section('title', 'Otorisasi Pengajuan')

@section('content')
<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-3">
                @include('theme.menu-pengajuan', ['nasabah' => $data->kd_pengajuan])
            </div>

            <div class="col-xs-9">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="#konfirmasi" data-toggle="tab">KONFIRMASI</a>
                        </li>
                    </ul>

                    <form action="{{ route('validasiotor', ['validasiotor' => $data->kd_pengajuan]) }}" method="post">
                        @csrf
                        <div class="tab-content">

                            <div class="tab-pane active" id="konfirmasi">
                                <div class="box-body" style="margin-top: -10px;font-size:12px;">

                                    <div class="alert alert-warning text-uppercase" style="margin-top:5px;">
                                        Apakah Anda yakin semua data sudah benar? jika iya silahkan lakukan otorisasi. Jika terjadi perubahan data<br> dikemudian hari maka perlu untuk melakukan proses otorisasi ulang.
                                    </div>
                                    
                                    <table class="table table-striped table-hover table-bordered table-condensed" style="margin-top:-10px;">
                                        <thead>
                                            <tr class="bg-blue">
                                                <th class="text-center" style="width: 30px">NO</th>
                                                <th class="text-center">INFORMASI</th>
                                                <th class="text-center" style="width: 100px">STATUS</th>
                                            </tr>
                                            <tr>
                                                <th class="text-center">1</th>
                                                <td>INFORMASI NASABAH</td>
                                                <input type="hidden" value="{{ $otorisasi->otornasabah }}" name="nasabah" >
                                                @if ($otorisasi->otornasabah == 'A')
                                                    <td class="text-center">
                                                        <i class="fa fa-check"></i>
                                                    </td>
                                                @else
                                                    <td class="text-center">
                                                        <i class="fa fa-close"></i>
                                                    </td>
                                                @endif
                                            </tr>

                                            <tr>
                                                <th class="text-center">2</th>
                                                <td>INFORMASI PENDAMPING</td>
                                                <input type="hidden" value="{{ $otorisasi->otorpendamping }}" name="pendamping">
                                                @if ($otorisasi->otorpendamping == 'A')
                                                    <td class="text-center">
                                                        <i class="fa fa-check"></i>
                                                    </td>
                                                @else
                                                    <td class="text-center">
                                                        <i class="fa fa-close"></i>
                                                    </td>
                                                @endif
                                            </tr>

                                            <tr>
                                                <th class="text-center">3</th>
                                                <td>INFORMASI PENGAJUAN</td>
                                                <input type="hidden" value="{{ $otorisasi->otorpengajuan }}" name="pengajuan">
                                                @if ($otorisasi->otorpengajuan == 'A')
                                                    <td class="text-center">
                                                        <i class="fa fa-check"></i>
                                                    </td>
                                                @else
                                                    <td class="text-center">
                                                        <i class="fa fa-close"></i>
                                                    </td>
                                                @endif
                                            </tr>

                                            <tr>
                                                <th class="text-center">4</th>
                                                <td>INFORMASI JAMINAN</td>
                                                <input type="hidden" value="{{ $otorisasi->otoragunan }}" name="agunan">
                                                @if ($otorisasi->otoragunan == 'A')
                                                    <td class="text-center">
                                                        <i class="fa fa-check"></i>
                                                    </td>
                                                @else
                                                    <td class="text-center">
                                                        <i class="fa fa-close"></i>
                                                    </td>
                                                @endif
                                            </tr>

                                            <tr>
                                                <th class="text-center">5</th>
                                                <td>INFORMASI SURVEYOR</td>
                                                <input type="hidden" value="{{ $otorisasi->otorsurvei }}" name="survei">
                                                @if ($otorisasi->otorsurvei == 'A')
                                                    <td class="text-center">
                                                        <i class="fa fa-check"></i>
                                                    </td>
                                                @else
                                                    <td class="text-center">
                                                        <i class="fa fa-close"></i>
                                                    </td>
                                                @endif
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            
                            <div class="box-body" style="margin-top:-20px;">
                                <button type="submit" class="btn btn-sm btn-primary" style="margin-top:10px;width:100%">KONFIRMASI</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
    </section>
</div>
@endsection

@push('myscript')
@endpush
