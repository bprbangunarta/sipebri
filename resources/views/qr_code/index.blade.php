@extends('qr_code.theme')
@section('title', 'View QRCode')

@section('content')
    <div class="content-wrapper" width='100%'>
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-calendar"></i>
                            <h3 class="box-title">VIEW QR</h3>
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered text-uppercase">
                                <thead>
                                    <tr class="bg-blue">
                                        <th class="text-center" width='30%'>TANDA TANGAN</th>
                                        <th class="text-center">DESKRIPSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>-</td>
                                        <td>-</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>
@endsection
