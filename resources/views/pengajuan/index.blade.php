@extends('theme.app')
@section('title', 'Pengajuan Kredit')
@yield('jquery')
@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-user"></i>
                            <h3 class="box-title">DATA PENGAJUAN KREDIT</h3>

                            <div class="box-tools">
                                <form {{ route('user.index') }}" method="GET">
                                    <div class="input-group input-group-sm hidden-xs" style="width: 170px;">
                                        <input type="text" class="form-control pull-right" name="name" id="name"
                                            value="{{ request('name') }}" placeholder="Search">

                                        <div class="input-group-btn">
                                            <button type="submit" class="btn btn-default"><i
                                                    class="fa fa-search"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="box-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="bg-blue">
                                        <th class="text-center" width="3%">NO</th>
                                        <th class="text-center">INFORMASI NASABAH</th>
                                        <th class="text-center" width="40%">ALAMAT</th>
                                        <th class="text-center" width="17%">PENGAJUAN</th>
                                        <th class="text-center" width="10%">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $no = 1;
                                    @endphp
                                    @forelse ($data as $item)
                                        <tr>
                                            <td class="text-center" style="vertical-align: middle;">
                                                {{ $loop->iteration + $data->firstItem() - 1 }}
                                            </td>

                                            <td style="vertical-align: middle;">
                                                <b>KODE :</b> {{ $item->kode }} [ {{ $item->kategori }} ] <br>
                                                <b>NAMA :</b> {{ strtoupper($item->nama) }} <br>
                                                <b>TANGGAL :</b>
                                                {{ \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') }}
                                            </td>

                                            @if (is_null($item->alamat))
                                                <td class="text-center" style="vertical-align: middle;">-</td>
                                            @else
                                                <td class="text-uppercase" style="vertical-align: middle;">
                                                    {{ $item->alamat }} <br>
                                                    <b>Desa: </b>{{ $item->kelurahan }} | <b>Kecamatan:
                                                    </b>{{ $item->kecamatan }}
                                                </td>
                                            @endif

                                            @php
                                                $item->plafon = number_format($item->plafon, 0, ',', '.');
                                            @endphp
                                            <td style="vertical-align: middle;">
                                                <b>KANTOR :</b> {{ $item->kantor }} <br>
                                                <b>{{ $item->produk_kode }} - JK : </b>{{ $item->jk }} BULAN <br>
                                                <b>PLAFON :</b> {{ $item->plafon }}
                                            </td>

                                            <td class="text-center" style="vertical-align: middle;">
                                                    @if ($item->status == 'Lengkapi Data')
                                                        <a href="{{ route('nasabah.edit', ['nasabah' => $item->kd]) }}">
                                                            <span class="btn bg-red"
                                                                style="width: 120px;hight:100%;">{{ $item->status }}</span>
                                                        </a>
                                                    @elseif ($item->status == 'Minta Otorisasi')
                                                        <a href="{{ route('nasabah.edit', ['nasabah' => $item->kd]) }}">
                                                            <span class="btn bg-yellow"
                                                                style="width: 120px;hight:100%;">{{ $item->status }}</span>
                                                        </a>
                                                    @else
                                                        <a href="{{ route('nasabah.edit', ['nasabah' => $item->kd]) }}">
                                                            <span class="btn bg-green"
                                                                style="width: 120px;hight:100%;">{{ $item->status }}</span>
                                                        </a>
                                                    @endif
                                            </td>
                                        </tr>
                                        @php
                                            $no++;
                                        @endphp
                                    @empty
                                        <tr>
                                            <td class="text-center" colspan="7">TIDAK ADA DATA</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="box-footer clearfix">
                            @can('tambah pengajuan kredit')
                                <button data-toggle="modal" data-target="#modal-tambah"
                                    class="btn btn-primary btn-sm pull-left">TAMBAH</button>
                            @endcan

                            {{ $data->links('vendor.pagination.adminlte') }}
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="modal fade" id="modal-tambah">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-blue">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">TAMBAH PENGAJUAN</h4>
                </div>
                <form action="{{ route('nasabah.store') }}" method="POST">
                    @csrf

                    {{-- Input user $ Identitas --}}
                    <input type="text" value="{{ $auth }}" name="input_user" hidden>
                    <input type="text" name="identitas" value="1" hidden>

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>KATEGORI</label>
                                    <select class="form-control" name="kategori" required>
                                        <option value="BARU" {{ old('kategori') == 'BARU' ? 'selected' : '' }}>BARU
                                        </option>
                                        <option value="RELOAN" {{ old('kategori') == 'RELOAN' ? 'selected' : '' }}>RELOAN
                                        </option>
                                        <option value="RSC" {{ old('kategori') == 'RSC' ? 'selected' : '' }}>RSC
                                        </option>
                                    </select>
                                </div>

                                <div class="form-group" style="margin-top:-10px;">
                                    <label>NO IDENTITAS</label>
                                    <input class="form-control" type="text" name="no_identitas" id="no_identitas"
                                        minlength="16" maxlength="16" value="{{ old('no_identitas') }}"
                                        placeholder="ENTRI" required>
                                </div>

                                <div class="form-group" style="margin-top:-10px;">
                                    <label>NAMA LENGKAP</label>
                                    <input class="form-control text-uppercase" type="text" name="nama_nasabah"
                                        id="nama_nasabah" placeholder="ENTRI" value="{{ old('nama_nasabah') }}"
                                        required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>TANGGAL LAHIR</label>
                                    <input type="text" class="form-control" name="tanggal_lahir" id="datemask"
                                        value="{{ old('tanggal_lahir') }}" data-inputmask="'alias': 'yyyy-mm-dd'"
                                        data-mask placeholder="YYYY-MM-DD">
                                </div>

                                <div class="form-group" style="margin-top:-10px;">
                                    <label>PENGAJUAN PLAFON</label>
                                    <input class="form-control" type="text" name="plafon" id="plafond"
                                        placeholder="ENTRI" value="{{ old('plafon') }}" required>
                                </div>

                                <div class="form-group" style="margin-top:-10px;">
                                    <label>JANGKA WAKTU</label>
                                    <input class="form-control" type="number" name="jangka_waktu"
                                        value="{{ old('jangka_waktu') }}" placeholder="ENTRI" required>
                                </div>
                            </div>
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

    <div class="modal fade" id="modal-cetak">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-red">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">CETAK BERKAS</h4>
                </div>
                <form action="{{ route('nasabah.store') }}" method="POST">
                    @csrf

                    {{-- Input user $ Identitas --}}
                    <input type="text" value="{{ $auth }}" name="input_user" hidden>
                    <input type="text" name="identitas" value="1" hidden>

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <p>DATA PENGAJUAN BELUM DIOTORISASI</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="margin-top: -10px;">
                        <button type="button" class="btn btn-danger" style="width: 100%;"
                            data-dismiss="modal">TUTUP</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('myscript')
    <script src="{{ asset('assets/js/myscript/batal.js') }}"></script>
    <script>
        //Datemask yyyy/mm/dd
        $('#datemask').inputmask('yyyy-mm-dd', {
            'placeholder': 'yyyy-mm-dd'
        })

        //Format rupiah
        var rupiah = document.getElementById('plafond');
        rupiah.addEventListener('keyup', function(e) {
            rupiah.value = formatRupiah(this.value, 'Rp. ');
        });

        function formatRupiah(angka, prefix) {
            var number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);


            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
        }
    </script>
@endpush
