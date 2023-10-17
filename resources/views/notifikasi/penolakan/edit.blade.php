@extends('theme.app')
@section('title', 'Edit Penolakan')

@section('content')
<div class="content-wrapper">

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <i class="fa fa-file-text-o"></i>
                        <h3 class="box-title">FORM TAMBAH PENOLAKAN</h3>
                    </div>
                    <div class="box-body">
                        <form action="">
                            <textarea class="textarea" placeholder="Place some text here" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"></textarea>

                            <button type="submit" class="btn btn-primary">SIMPAN</button>
                            <A href="{{ route('penolakan.pengajuan') }}" class="btn btn-default pull-right">BATAL</A>
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
  $(function () {
    $('.textarea').wysihtml5()
  })
</script>
@endpush