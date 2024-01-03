@extends('theme.app')
@section('title', 'Data Nasabah')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border" style="border-bottom: 1px solid #3C8DBC;">
                            <i class="fa fa-user"></i>
                            <h3 class="box-title">DATA NASABAH</h3>

                            <div class="box-tools">
                                <form action="{{ route('admin.nasabah.index') }}" method="GET">
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
                                        <th class="text-center">KODE</th>
                                        <th class="text-center">NAMA NASABAH</th>
                                        <th class="text-center">ALAMAT</th>
                                        <th class="text-center">NO. KTP</th>
                                        <th class="text-center">NO. CIF</th>
                                        <th class="text-center">INPUT</th>
                                        <th class="text-center">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $no = 1;
                                    @endphp
                                    @forelse ($data as $item)
                                        <tr class="text-uppercase">
                                            <td class="text-center">
                                                {{ $loop->iteration + $data->firstItem() - 1 }}
                                            </td>
                                            <td class="text-center">{{ $item->kode_nasabah }}</td>
                                            <td>{{ $item->nama_nasabah }}</td>
                                            <td>{{ $item->alamat_ktp }}</td>
                                            <td class="text-center">{{ $item->no_identitas }}</td>
                                            <td class="text-center">{{ $item->no_cif }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.nasabah.edit', $item->id) }}"
                                                    class="btn-circle btn-sm bg-yellow">
                                                    <i class="fa fa-edit"></i>
                                                </a>

                                                &nbsp;
                                                <form id="deleteForm" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')

                                                    <a href="#" class="btn-circle btn-sm bg-red deleteButton"
                                                        data-id="{{ $item->id }}">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </form>
                                            </td>
                                        </tr>
                                        @php
                                            $no++;
                                        @endphp
                                    @empty
                                        <tr>
                                            <td class="text-center" colspan="10">TIDAK ADA DATA</td>
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

                            {{ $data->withQueryString()->onEachSide(0)->links('vendor.pagination.adminlte') }}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('myscript')
    <script>
        $(".deleteButton").click(function(e) {
            e.preventDefault();

            var id = $(this).data("id");

            Swal.fire({
                title: "Apakah Anda yakin?",
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, hapus!"
            }).then((result) => {
                if (result.isConfirmed) {
                    console.log("Deleting ID:", id);

                    $("#deleteForm").attr("action", "{{ url('admin/data/nasabah') }}/" + id);

                    $("#deleteForm").submit();
                }
            });
        });
    </script>
@endpush
