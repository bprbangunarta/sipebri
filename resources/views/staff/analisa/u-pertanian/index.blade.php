@extends('staff.analisa.usaha', [$data, 'pengajuan' => $data->kd_pengajuan])
@section('title', 'Analisa Usaha Lainnya')

@section('content')
    <div class="tab-content">
        <div class="tab-pane active">
            <table class="table table-striped table-hover table-bordered table-condensed">
                <thead>
                    <tr>
                        <th class="text-center">Nama Usaha</th>
                        <th class="text-center">Pendapatan</th>
                        <th class="text-center">Pengeluaran</th>
                        <th class="text-center">Laba Bersih</th>
                        <th class="text-center" style="width: 78px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pertanian as $item)
                        <tr>
                            <td style="vertical-align: middle;text-transform:uppercase;">SAWAH PADI KETAN</td>
                            <td style="vertical-align: middle;">Rp. 66.000.000</td>
                            <td style="vertical-align: middle;">Rp. 5.400.000</td>
                            <td style="vertical-align: middle;">Rp. 60.600.000</td>
                            <td class="text-center">
                                <a href="/theme/analisa/informasi/usaha/pertanian/" class="btn btn-sm btn-warning"
                                    style="float: left" title="Input Analisa">
                                    <i class="fa fa-file-text-o"></i></a>

                                <form action="#" method="POST">
                                    @csrf
                                    @method('delete')
                                    <button type="button" class="btn btn-sm btn-danger" style="float: right"
                                        title="Hapus Usaha">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <a data-toggle="modal" data-target="#modal-tambah" class="btn btn-sm btn-primary"
                style="margin-top:-15px;">TAMBAH</a>
        </div>
    </div>

    <div class="modal fade" id="modal-tambah">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-blue">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">TAMBAH USAHA</h4>
                </div>
                <form action="#" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama Usaha</label>
                            <input type="text" class="form-control" name="nama_usaha" id="nama_usaha"
                                placeholder="Nama Usaha" value="{{ old('nama_usaha') }}" required>
                        </div>
                    </div>
                    <div class="modal-footer" style="margin-top: -10px;">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">BATAL</button>
                        <button type="submit" class="btn btn-primary">SIMPAN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('myscript')
    <script src="{{ asset('assets/js/myscript/delete.js') }}"></script>
@endpush
