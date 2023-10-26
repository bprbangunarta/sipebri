@extends('theme.app')
@section('title', 'Edit Penolakan')

@section('content')
    <div class="content-wrapper">

        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-warning">
                        <div class="box-header with-border">
                            <i class="fa fa-file-text-o"></i>
                            <h3 class="box-title">FORM EDIT PENOLAKAN</h3>
                        </div>
                        <div class="box-body">
                            <form action="{{ route('penolakan.simpan') }}" method="post">
                                @csrf
                                <div class="form-group">
                                    <label>PILIH ALASAN PENOLAKAN</label>
                                    <input type="text" name="kd_pengajuan" value="{{ $data }}" hidden>
                                    <select class="form-control select2" name="alasan" style="width: 100%;">
                                        <option value="">--PILIH--</option>
                                        <option>SLIK BERMASALAH</option>
                                        <option>KARAKTER KURANG BAIK</option>
                                    </select>
                                </div>

                                {{-- <div class="row" style="margin-top: -5px;">
                                    <div class="col-lg-3">
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <input type="checkbox">
                                            </span>
                                            <input type="text" class="form-control" value="BERMASALAH SLIK">
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <input type="checkbox">
                                            </span>
                                            <input type="text" class="form-control" value="PARAMETER LAIN">
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <input type="checkbox">
                                            </span>
                                            <input type="text" class="form-control" value="PARAMETER LAIN">
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <input type="checkbox">
                                            </span>
                                            <input type="text" class="form-control" value="PARAMETER LAIN">
                                        </div>
                                    </div>
                                </div> --}}

                                <div class="form-group" style="margin-top: 10px;">
                                    <textarea class="textarea" name="keterangan" placeholder="Place some text here"
                                        style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"></textarea>
                                </div>

                                <div style="margin-top: -10px;">
                                    <button type="submit" class="btn btn-warning">SIMPAN</button>
                                    <a href="{{ route('penolakan.pengajuan', []) }}"
                                        class="btn btn-default pull-right">BATAL</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>
@endsection

@push('myscript')
    <script src="{{ asset('theme/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js') }}"></script>
    <script>
        $(function() {
            $('.textarea').wysihtml5()

            //Initialize Select2 Elements
            $('.select2').select2()

            //Flat red color scheme for iCheck
            $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
                checkboxClass: 'icheckbox_flat-blue',
                radioClass: 'iradio_flat-blue'
            })

        })
    </script>
@endpush
