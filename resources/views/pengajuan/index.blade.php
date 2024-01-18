@extends('theme.app')
@section('title', 'Add Pengajuan')
@yield('jquery')
@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-plus"></i>
                            <h3 class="box-title">ADD PENGAJUAN KREDIT</h3>

                            <div class="box-tools">
                                <form action="{{ route('pengajuan.index') }}" method="GET">
                                    <div class="input-group input-group-sm hidden-xs" style="width: 305px;">
                                        <input type="text" class="form-control text-uppercase pull-right"
                                            style="width: 180px;font-size:11.4px;" name="keyword" id="keyword"
                                            value="{{ request('keyword') }}" placeholder="Nama/ Kode/ Wilayah/ Produk">

                                        <div class="input-group-btn">
                                            <button type="submit" class="btn bg-blue">
                                                <i class="fa fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="box-body" style="overflow: auto;white-space: nowrap;width: 100%;">
                            <table class="table table-bordered" style="font-size:12px;">
                                <thead>
                                    <tr class="bg-blue">
                                        <th class="text-center" width="3%">NO</th>
                                        <th class="text-center">TANGGAL</th>
                                        <th class="text-center">KODE</th>
                                        <th class="text-center">NAMA NASABAH</th>
                                        <th class="text-center">ALAMAT</th>
                                        <th class="text-center">WIL</th>
                                        <th class="text-center">PLAFON</th>
                                        <th class="text-center" width="10%">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $no = 1;
                                    @endphp
                                    @forelse ($data as $item)
                                        <tr>
                                            <td class="text-center">
                                                {{ $loop->iteration + $data->firstItem() - 1 }}
                                            </td>
                                            <td class="text-center">
                                                {{ \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') }}
                                            </td>
                                            <td class="text-center">{{ $item->kode }}</td>
                                            <td>{{ strtoupper($item->nama) }}</td>
                                            @if (is_null($item->alamat))
                                                <td class="text-center">-</td>
                                            @else
                                                <td class="text-uppercase">
                                                    {{ $item->alamat }}
                                                </td>
                                            @endif

                                            <td class="text-center">
                                                {{ $item->kantor }}
                                            </td>

                                            @php
                                                $item->plafon = number_format($item->plafon, 0, ',', '.');
                                            @endphp
                                            <td class="text-right">
                                                {{ $item->plafon }}
                                            </td>
                                            <td class="text-center">
                                                @if ($item->status == 'Lengkapi Data')
                                                    <a href="{{ route('nasabah.edit', ['nasabah' => $item->kd]) }}"
                                                        class="btn-circle btn-sm bg-yellow" title="Lengkapi Data">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                @elseif ($item->status == 'Minta Otorisasi')
                                                    <a href="{{ route('nasabah.edit', ['nasabah' => $item->kd]) }}"
                                                        class="btn-circle btn-sm bg-yellow" title="Minta Otorisasi">
                                                        <i class="fa fa-check-circle"></i>
                                                    </a>
                                                @else
                                                    <a href="{{ route('nasabah.edit', ['nasabah' => $item->kd]) }}"
                                                        class="btn-circle btn-sm bg-green" title="Input Pengajuan">
                                                        <i class="fa fa-check-circle"></i>
                                                    </a>
                                                @endif

                                                &nbsp;
                                                <a data-toggle="modal" data-target="#info-{{ $item->kode }}"
                                                    class="btn-circle btn-sm bg-blue" title="Informasi">
                                                    <i class="fa fa-eye"></i>
                                                </a>

                                                &nbsp;
                                                <a href="{{ route('batal.destroy', ['pengajuan' => $item->kd]) }}"
                                                    data-toggle="modal" data-target="#hapus"
                                                    class="btn-circle btn-sm bg-red confirmdelete" title="Hapus"
                                                    style="cursor: pointer;">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </td>

                                            {{-- MODAL INFO --}}
                                            <div class="modal fade" id="info-{{ $item->kode }}">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-blue">
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                aria-label="Close">
                                                                <span aria-hidden="true">&times;</span></button>
                                                            <h4 class="modal-title">INFORMASI PENGAJUAN</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>NAMA NASABAH</label>
                                                                        <input type="text" class="form-control"
                                                                            value="{{ $item->nama }} - {{ $item->kategori }}">
                                                                    </div>

                                                                    <div class="form-group" style="margin-top:-10px;">
                                                                        <label>PRODUK KREDIT</label>
                                                                        <input type="text" class="form-control"
                                                                            value="{{ $item->produk_kode }} - {{ $item->nama_produk }}">
                                                                    </div>

                                                                    <div class="form-group" style="margin-top:-10px;">
                                                                        <label>PLAFON KREDIT</label>
                                                                        <input type="text" class="form-control"
                                                                            value="{{ $item->plafon }}">
                                                                    </div>

                                                                    <div class="form-group" style="margin-top:-10px;">
                                                                        <label>JANGKA WAKTU</label>
                                                                        <input type="text" class="form-control"
                                                                            value="{{ $item->jk }} BULAN - {{ $item->metode_rps }}">
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>DESA KECAMATAN</label>
                                                                        <input type="text" class="form-control"
                                                                            value="{{ $item->kelurahan }} - {{ $item->kecamatan }}">
                                                                    </div>

                                                                    <div class="form-group" style="margin-top:-10px;">
                                                                        <label>SURVEYOR</label>
                                                                        <input type="text" class="form-control"
                                                                            value="{{ $item->surveyor }}">
                                                                    </div>

                                                                    <div class="form-group" style="margin-top:-10px;">
                                                                        <label>TRACKING</label>
                                                                        <input type="text"
                                                                            class="form-control text-uppercase"
                                                                            value="{{ $item->tracking }}">
                                                                    </div>

                                                                    <div class="form-group" style="margin-top:-10px;">
                                                                        <label>PERSETUJUAN</label>
                                                                        @if ($item->status == 'Disetujui' || $item->status == 'Ditolak' || $item->status == 'Dibatalkan')
                                                                            <input type="text"
                                                                                class="form-control text-uppercase"
                                                                                value="{{ $item->status }}">
                                                                        @else
                                                                            <input type="text"
                                                                                class="form-control text-uppercase"
                                                                                value="BELUM ADA PERSETUJUAN">
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="modal-footer" style="margin-top: -10px;">
                                                            <button type="submit" class="btn bg-blue"
                                                                data-dismiss="modal" style="width: 100%;">TUTUP</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- END MODAL INFO --}}
                                        </tr>
                                        @php
                                            $no++;
                                        @endphp
                                    @empty
                                        <tr>
                                            <td class="text-center" colspan="8">TIDAK ADA DATA</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="box-footer clearfix">
                            <div class="pull-left hidden-xs">
                                <button data-toggle="modal" data-target="#modal-tambah" class="btn bg-blue btn-sm">
                                    <i class="fa fa-plus"></i>&nbsp; TAMBAH
                                </button>
                                <button class="btn btn-default btn-sm">
                                    Showing {{ $data->firstItem() }} to {{ $data->lastItem() }} of {{ $data->total() }}
                                    entries
                                </button>
                            </div>

                            {{ $data->withQueryString()->onEachSide(0)->links('vendor.pagination.adminlte') }}
                        </div>

                    </div>

                    <div class="alert alert-danger alert-dismissible">
                        <h1 class="text-center">
                            Kategori <b>BARU</b> untuk kondisi pengajuan <b>BARU</b> & <b>TOPUP</b> <br>
                            Kategori <b>RELOAN</b> untuk penyelamatan kredit dan <b>KUP, KKO</b><br>
                        </h1>
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
                <form action="{{ route('nasabah.store') }}" id="form-pengajuan" method="POST">
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
                        <button type="submit" class="btn btn-primary" id="submit-pengajuan">SIMPAN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('myscript')
    <script src="{{ asset('assets/js/myscript/batal.js') }}"></script>
    <script src="{{ asset('assets/js/myscript/cek_nasabah.js') }}"></script>

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

        //Hold Submit Ketika diklik 2X
        $('#form-pengajuan').submit(function(event) {
            var submitButton = $('#submit-pengajuan');
            submitButton.prop('disabled', true);

        });

        $(".confirmdelete").click(function(event) {
            event.preventDefault();

            var kd = $(this).data('kd');
            var deleteUrl = $(this).attr('href'); // Mendapatkan URL dari href

            Swal.fire({
                title: "Apakah anda yakin?",
                text: "Yakin, Ingin menghapus data",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, Hapus!",
                cancelButtonText: "Batal",
            }).then((willDelete) => {
                if (willDelete.isConfirmed) {
                    $.ajax({
                        url: deleteUrl,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.message == 'Data berhasil dihapus') {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Data berhasil dihapus.',
                                    icon: 'success',
                                    showConfirmButton: false,
                                    timer: 2000
                                }).then((result) => {
                                    if (result.isConfirmed || result.dismiss ===
                                        'timer') {
                                        location.reload();
                                    }
                                });

                            } else if (response.message == 'Permintaan  anda ditolak') {
                                Swal.fire({
                                    title: 'Ditolak!',
                                    text: 'PErmintaan anda ditolak.',
                                    icon: 'warning',
                                    showConfirmButton: false,
                                    timer: 2000
                                });
                            } else {
                                Swal.fire({
                                    title: 'Gagal!!!',
                                    text: 'Data gagal dihapus.',
                                    icon: 'danger',
                                    showConfirmButton: false,
                                    timer: 2000
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error:", xhr.responseText);
                        }
                    });
                }
            });
        });
    </script>
@endpush
