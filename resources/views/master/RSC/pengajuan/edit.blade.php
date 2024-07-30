@extends('theme.app')
@section('title', 'Edit Pengajuan RSC')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border" style="border-bottom: 1px solid #3C8DBC;">
                            <i class="fa fa-user"></i>
                            <h3 class="box-title">DATA PENGAJUAN RSC</h3>
                        </div>

                        <form action="{{ route('admin.rsc.pengajuan.update') }}" method="POST">
                            @csrf
                            @method('POST')
                            <div class="box-body" data-select2-id="13">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group" style="margin-top:-5px;">
                                            <label>KODE PENGAJUAN</label>
                                            <input type="text" class="form-control" name="pengajuan_kode" id=""
                                                value="{{ $data->pengajuan_kode }}" readonly>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group" style="margin-top:-5px;">
                                            <label>KODE RSC</label>
                                            <input type="text" class="form-control" name="kode_rsc" id=""
                                                value="{{ $data->kode_rsc }}" readonly>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group" style="margin-top:-5px;">
                                            <label>TRACKING</label>
                                            <select type="text" class="form-control tracking" name="status" required>
                                                <option value="Batal"
                                                    {{ $data->status == 'Batal' || old('Batal') == 'Batal' ? 'selected' : '' }}>
                                                    Batal</option>
                                                <option value="Ditolak"
                                                    {{ $data->status == 'Ditolak' || old('Ditolak') == 'Ditolak' ? 'selected' : '' }}>
                                                    Ditolak</option>
                                                <option value="Dibatalkan"
                                                    {{ $data->status == 'Dibatalkan' || old('Dibatalkan') == 'Dibatalkan' ? 'selected' : '' }}>
                                                    Dibatalkan</option>
                                                <option value="Penjadwalan"
                                                    {{ $data->status == 'Penjadwalan' || old('Penjadwalan') == 'Penjadwalan' ? 'selected' : '' }}>
                                                    Penjadwalan</option>
                                                <option value="Proses Survei"
                                                    {{ $data->status == 'Proses Survei' || old('Proses Survei') == 'Proses Survei' ? 'selected' : '' }}>
                                                    Proses Survei</option>
                                                <option value="Proses Analisa"
                                                    {{ $data->status == 'Proses Analisa' || old('Proses Analisa') == 'Proses Analisa' ? 'selected' : '' }}>
                                                    Proses Analisa</option>
                                                <option value="Persetujuan Komite"
                                                    {{ $data->status == 'Persetujuan Komite' || old('Persetujuan Komite') == 'Persetujuan Komite' ? 'selected' : '' }}>
                                                    Persetujuan Komite</option>
                                                <option value="Naik Kasi"
                                                    {{ $data->status == 'Naik Kasi' || old('Naik Kasi') == 'Naik Kasi' ? 'selected' : '' }}>
                                                    Naik Kasi</option>
                                                <option value="Naik Komite I"
                                                    {{ $data->status == 'Naik Komite I' || old('Naik Komite I') == 'Naik Komite I' ? 'selected' : '' }}>
                                                    Naik Komite I</option>
                                                <option value="Naik Komite II"
                                                    {{ $data->status == 'Naik Komite II' || old('Naik Komite II') == 'Naik Komite II' ? 'selected' : '' }}>
                                                    Naik Komite II</option>
                                                <option value="Notifikasi"
                                                    {{ $data->status == 'Notifikasi' || old('Notifikasi') == 'Notifikasi' ? 'selected' : '' }}>
                                                    Notifikasi</option>
                                                <option value="Perjanjian Kredit"
                                                    {{ $data->status == 'Perjanjian Kredit' || old('Perjanjian Kredit') == 'Perjanjian Kredit' ? 'selected' : '' }}>
                                                    Perjanjian Kredit</option>
                                            </select>
                                        </div>

                                    </div>
                                </div>
                            </div>


                            <div class="box-footer" style="margin-top: -10px;">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> SIMPAN
                                </button>

                                <a href="{{ route('admin.pengajuan.index') }}" class="btn btn-default pull-right">
                                    <i class="fa fa-arrow-left"></i> KEMBALI
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
@push('myscript')
    <script>
        $('.tracking').select2()
    </script>
@endpush
