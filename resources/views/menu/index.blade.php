@extends('theme.app')
@section('title', 'Ubah Data All')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-superpowers" aria-hidden="true"></i>
                            <h3 class="box-title">UBAH DATA ALL</h3>
                        </div>

                        <div id="formpassword" class="box-body"
                            style="overflow: auto;white-space: nowrap;width: 100%;margin-top:-10px;">

                            <form role="form" action="{{ route('ubah.data_tabel') }}" method="POST">
                                @csrf
                                <div class="box-body">

                                    <div class="form-group">
                                        <label>Nama Tabel <i>( data_pengajuan, data_nasabah )</i></label>
                                        <input type="text" class="form-control" name="table" id=""
                                            placeholder="Masukan Nama Tabel" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Nama Field Tabel <i>( kode_pengajuan, kode_nasabah )</i></label>
                                        <input type="text" class="form-control" name="field_table" id=""
                                            placeholder="Masukan Field Tabel" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Parameter Tabel <i>( Kode Pengajuan, ID )</i></label>
                                        <input type="text" class="form-control" name="parameter" id=""
                                            placeholder="Masukan Parameter Tabel" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Fungsional <i>( GET, FIRST, LATEST, UPDATE, DELETE )</i></label>
                                        <select type="text" class="form-control fungsional" style="width: 100%;"
                                            name="fungsional" id="fungsional" required>
                                            <option value="">---PILIH---</option>
                                            <option value="get">GET</option>
                                            <option value="first">FIRST</option>
                                            <option value="latest">LATEST</option>
                                            <option value="update">UPDATE</option>
                                            <option value="delete">DELETE</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Nama Filed Tabel Yang Akan Diubah <i>( plfon, metode_rps, tracking, dll
                                                )</i></label>
                                        <input type="text" class="form-control" name="change_value_field_table"
                                            id="" placeholder="Masukan Value Field Tabel">
                                    </div>
                                    <div class="form-group">
                                        <label>Value Field Tabel <i>( Char, String, Integer )</i></label>
                                        <input type="text" class="form-control" name="value_field_table" id=""
                                            placeholder="Masukan Value Field Tabel">
                                    </div>
                                </div>
                                <div class="box-footer">
                                    <button type="submit" class="btn bg-blue" style="width: 100%;">SIMPAN</button>
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
    <script>
        $('#fungsional').on('change', function() {
            console.log()
        })
    </script>
@endpush
