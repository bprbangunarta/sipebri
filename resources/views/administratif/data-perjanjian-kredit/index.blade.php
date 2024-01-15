@extends('theme.app')
@section('title', 'Data Perjanjian Kredit')

@section('content')
    <div class="content-wrapper">

        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-handshake-o" aria-hidden="true"></i>
                            <h3 class="box-title">DATA PERJANJIAN KREDIT</h3>

                            <div class="box-tools">
                                <form action="#" method="GET">
                                    <div class="input-group input-group-sm hidden-xs" style="width: 305px;">
                                        <input type="text" class="form-control text-uppercase pull-right"
                                            style="width: 170px;" name="keyword" id="keyword"
                                            value="{{ request('keyword') }}" placeholder="Nama/ Kode/ Wilayah">

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
                            <table class="table table-bordered text-uppercase" style="font-size: 12px;">
                                <thead>
                                    <tr class="bg-blue">
                                        <th class="text-center" width="3%">#</th>
                                        <th class="text-center" width="8%">TANGGAL</th>
                                        <th class="text-center" width="8%">KODE</th>
                                        <th class="text-center" width="7%">NO. SPK</th>
                                        <th class="text-center">NAMA NASABAH</th>
                                        <th class="text-center" width="35%">ALAMAT</th>
                                        <th class="text-center" width="5%">WIL</th>
                                        <th class="text-center" width="8%">PLAFON</th>
                                        <th class="text-center" width="5%">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($data as $item)
                                        <tr class="text-uppercase">
                                            <td class="text-center" style="vertical-align: middle;">
                                                {{ $loop->iteration + $data->firstItem() - 1 }}
                                            </td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                {{ \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') }} <br>
                                            </td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                {{ $item->kode_pengajuan }}</td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                {{ $item->no_spk }}
                                            </td>
                                            <td style="vertical-align: middle;">{{ $item->nama_nasabah }}</td>
                                            <td style="vertical-align: middle;">{{ $item->alamat_ktp }}</td>
                                            <td class="text-center" style="vertical-align: middle;">{{ $item->kode_kantor }}
                                            </td>
                                            <td class="text-right" style="vertical-align: middle;">
                                                {{ number_format($item->plafon, 0, ',', '.') }}</td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                <form id="batalForm"
                                                    action="{{ route('data.batal_perjanjian_kredit', ['kode' => $item->kd_pengajuan]) }}"
                                                    method="POST" style="display:inline-block;">
                                                    @csrf
                                                    <button type="submit" class="btn-circle btn-sm bg-red konfirmasibatal"
                                                        style="cursor: pointer; border:none; height:5%; display:flex;"
                                                        title="Batal PK">
                                                        <i class="fa fa-times-circle" aria-hidden="true"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>

                                    @empty
                                        <tr>
                                            <td class="text-center" colspan="9">TIDAK ADA DATA</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="box-footer clearfix">
                            <div class="pull-left hidden-xs">
                                <button class="btn btn-default btn-sm">
                                    Showing {{ $data->firstItem() }} to {{ $data->lastItem() }} of {{ $data->total() }}
                                    entries
                                </button>
                            </div>

                            {{-- Showing {{ $data->firstItem() }} to {{ $data->lastItem() }} of {{ $data->total() }}
                            entries --}}
                            {{ $data->withQueryString()->onEachSide(0)->links('vendor.pagination.adminlte') }}
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="modal fade" id="notifikasi">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-blue">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">INFORMATION</h4>
                </div>
                <div class="modal-body">
                    <P style="font-size: 16px; text-align: justify;">
                        Halaman ini berfungsi sebagai fasilitas untuk melakukan <b>Pembatalan Perjanjian Kredit</b>,
                        meskipun data
                        telah mencapai tahap Dropping. Untuk memastikan data telah dibatalkan, Anda dapat melakukan
                        pencarian melalui halaman Add Perjanjian Kredit.
                    </P>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary bg-blue" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('myscript')
    <script>
        $(document).ready(function() {

            var currentURL = window.location.href;
            var url = new URL(currentURL);
            var hasQueryString = url.search !== '';

            if (!hasQueryString) {
                $('#notifikasi').modal('show');
            }

            $(".konfirmasibatal").click(function(event) {
                event.preventDefault();
                var form = $(this).closest("form");

                Swal.fire({
                    title: "Apakah anda yakin?",
                    text: "Ingin Membatalkan Perjanjian Kredit",
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Ya, Batl!",
                    cancelButtonText: "Batal",
                }).then((willDelete) => {
                    if (willDelete.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
