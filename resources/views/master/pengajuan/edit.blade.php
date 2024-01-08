@extends('theme.app')
@section('title', 'Edit Pengajuan')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border" style="border-bottom: 1px solid #3C8DBC;">
                            <i class="fa fa-user"></i>
                            <h3 class="box-title">DATA PENGAJUAN</h3>
                        </div>

                        <form action="{{ route('admin.pengajuan.update', ['data' => $data->kode_pengajuan]) }}"
                            method="POST">
                            @csrf
                            @method('PUT')
                            <div class="box-body" data-select2-id="13">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group" style="margin-top:-5px;">
                                            <label>KODE PENGAJUAN</label>
                                            <input type="text" class="form-control" name="" id=""
                                                value="{{ $data->kode_pengajuan }}">
                                        </div>

                                        <div class="form-group" style="margin-top:-5px;">
                                            <label>STATUS</label>
                                            <select type="text" class="form-control" name="status" required>
                                                <option value="Lengkapi Data"
                                                    {{ $data->status == 'Lengkapi Data' || old('Lengkapi Data') == 'Lengkapi Data' ? 'selected' : '' }}>
                                                    Lengkapi Data</option>
                                                <option value="Minta Otorisasi"
                                                    {{ $data->status == 'Minta Otorisasi' || old('Minta Otorisasi') == 'Minta Otorisasi' ? 'selected' : '' }}>
                                                    Minta Otorisasi</option>
                                                <option value="Sudah Otorisasi"
                                                    {{ $data->status == 'Sudah Otorisasi' || old('Sudah Otorisasi') == 'Sudah Otorisasi' ? 'selected' : '' }}>
                                                    Sudah Otorisasi</option>
                                                <option value="Batal"
                                                    {{ $data->status == 'Batal' || old('Batal') == 'Batal' ? 'selected' : '' }}>
                                                    Batal</option>
                                                <option value="Disetujui"
                                                    {{ $data->status == 'Disetujui' || old('Disetujui') == 'Disetujui' ? 'selected' : '' }}>
                                                    Disetujui</option>
                                                <option value="Ditolak"
                                                    {{ $data->status == 'Ditolak' || old('Ditolak') == 'Ditolak' ? 'selected' : '' }}>
                                                    Ditolak</option>
                                                <option value="Dibatalkan"
                                                    {{ $data->status == 'Dibatalkan' || old('Dibatalkan') == 'Dibatalkan' ? 'selected' : '' }}>
                                                    Dibatalkan</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group" style="margin-top:-5px;">
                                            <label>KATEGORI</label>
                                            <select type="text" class="form-control" name="kategori" required>
                                                <option value="RELOAN"
                                                    {{ $data->kategori == 'RELOAN' || old('RELOAN') == 'RELOAN' ? 'selected' : '' }}>
                                                    RELOAN</option>
                                                <option value="BARU"
                                                    {{ $data->kategori == 'BARU' || old('BARU') == 'BARU' ? 'selected' : '' }}>
                                                    BARU</option>
                                                <option value="RSC"
                                                    {{ $data->kategori == 'RSC' || old('RSC') == 'RSC' ? 'selected' : '' }}>
                                                    RSC</option>

                                            </select>
                                        </div>

                                        <div class="form-group" style="margin-top:-5px;">
                                            <label>ON CURRENT</label>
                                            <input type="text" class="form-control" name="on_current" id=""
                                                value="{{ $data->on_current }}">
                                        </div>

                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group" style="margin-top:-5px;">
                                            <label>TRACKING</label>
                                            <select type="text" class="form-control" name="tracking" required>
                                                <option value="Verifikasi Data"
                                                    {{ $data->tracking == 'Verifikasi Data' || old('Verifikasi Data') == 'Verifikasi Data' ? 'selected' : '' }}>
                                                    Verifikasi Data</option>
                                                <option value="Penjadwalan"
                                                    {{ $data->tracking == 'Penjadwalan' || old('Penjadwalan') == 'Penjadwalan' ? 'selected' : '' }}>
                                                    Penjadwalan</option>
                                                <option value="Proses Survei"
                                                    {{ $data->tracking == 'Proses Survei' || old('Proses Survei') == 'Proses Survei' ? 'selected' : '' }}>
                                                    Proses Survei</option>
                                                <option value="Proses Analisa"
                                                    {{ $data->tracking == 'Proses Analisa' || old('Proses Analisa') == 'Proses Analisa' ? 'selected' : '' }}>
                                                    Proses Analisa</option>
                                                <option value="Persetujuan Komite"
                                                    {{ $data->tracking == 'Persetujuan Komite' || old('Persetujuan Komite') == 'Persetujuan Komite' ? 'selected' : '' }}>
                                                    Persetujuan Komite</option>
                                                <option value="Naik Kasi"
                                                    {{ $data->tracking == 'Naik Kasi' || old('Naik Kasi') == 'Naik Kasi' ? 'selected' : '' }}>
                                                    Naik Kasi</option>
                                                <option value="Naik Komite I"
                                                    {{ $data->tracking == 'Naik Komite I' || old('Naik Komite I') == 'Naik Komite I' ? 'selected' : '' }}>
                                                    Naik Komite I</option>
                                                <option value="Naik Komite II"
                                                    {{ $data->tracking == 'Naik Komite II' || old('Naik Komite II') == 'Naik Komite II' ? 'selected' : '' }}>
                                                    Naik Komite II</option>
                                                <option value="Realisasi"
                                                    {{ $data->tracking == 'Realisasi' || old('Realisasi') == 'Realisasi' ? 'selected' : '' }}>
                                                    Realisasi</option>
                                                <option value="Selesai"
                                                    {{ $data->tracking == 'Selesai' || old('Selesai') == 'Selesai' ? 'selected' : '' }}>
                                                    Selesai</option>
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
