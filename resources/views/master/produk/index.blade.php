@extends('templates.app')
@section('title', 'Data Produk')

@section('content')

    @php
        use Carbon\Carbon;
        $year = Carbon::now()->year;
    @endphp

    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">

                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="container-xl">
                                <div class="row g-2 align-items-center">
                                    <div class="col">
                                        <!-- Page pre-title -->
                                        <div class="page-pretitle">
                                            Master
                                        </div>
                                        <h2 class="page-title">
                                            Data Produk
                                        </h2>
                                    </div>
                                    <!-- Page title actions -->
                                    <div class="col-auto ms-auto d-print-none">
                                        <div class="btn-list">
                                            <a href="#" class="btn btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#modal-tambah">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="icon icon-tabler icon-tabler-plus" width="24" height="24"
                                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <path d="M12 5l0 14"></path>
                                                    <path d="M5 12l14 0"></path>
                                                </svg>
                                                Tambah
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body border-bottom py-3" style="margin-top:-7px;">

                            <form action="{{ route('produk.index') }}" method="GET">
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" name="name" id="name"
                                        placeholder="Kode Produk" value="{{ Request('name') }}">
                                    <button class="btn" type="submit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-filter"
                                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path
                                                d="M4 4h16v2.172a2 2 0 0 1 -.586 1.414l-4.414 4.414v7l-6 2v-8.5l-4.48 -4.928a2 2 0 0 1 -.52 -1.345v-2.227z">
                                            </path>
                                        </svg>
                                        Filter
                                    </button>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-bordered table-vcenter">
                                    <thead>
                                        <tr>
                                            <th class="text-center" width="3%">No</th>
                                            <th class="text-center" width="7%">Kode</th>
                                            <th class="text-center">Nama Produk</th>
                                            <th class="text-center" width="5%">Rate</th>
                                            <th class="text-center" width="5%">Total</th>
                                            <th class="text-center" width="5%">{{ $year }}</th>
                                            <th class="text-center">Created At</th>
                                            <th class="text-center">Updated At</th>
                                            <th class="text-center" width="5%">Ubah</th>
                                            <th class="text-center" width="5%">Hapus</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($produk as $data)
                                            <tr>
                                                <td class="text-center">{{ $loop->iteration + $produk->firstItem() - 1 }}
                                                </td>
                                                <td class="text-center">{{ $data->kode_produk }}</td>
                                                <td>{{ $data->nama_produk }}</td>
                                                <td class="text-center">{{ $data->rate }}%</td>
                                                <td class="text-center">{{ $data->jumlah_pengajuan }}</td>
                                                <td class="text-center">{{ $data->reset_nomer }}</td>
                                                <td class="text-center">{{ $data->created_at }}</td>
                                                <td class="text-center">{{ $data->updated_at }}</td>
                                                <td class="text-center">
                                                    <a href="#" data-bs-toggle="modal" data-bs-target="#modal-edit"
                                                        data-id="{{ $data->kode_produk }}">
                                                        <span class="badge bg-warning">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                class="icon icon-tabler icon-tabler-edit" width="24"
                                                                height="24" viewBox="0 0 24 24" stroke-width="2"
                                                                stroke="currentColor" fill="none" stroke-linecap="round"
                                                                stroke-linejoin="round">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none">
                                                                </path>
                                                                <path
                                                                    d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1">
                                                                </path>
                                                                <path
                                                                    d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z">
                                                                </path>
                                                                <path d="M16 5l3 3"></path>
                                                            </svg>
                                                        </span>
                                                    </a>
                                                </td>
                                                <td class="text-center">
                                                    <form
                                                        action="{{ route('produk.destroy', ['produk' => $data->kode_produk]) }}"
                                                        method="POST">
                                                        @method('delete')
                                                        @csrf
                                                        <button type="submit"
                                                            style="border: none; background: transparent;">
                                                            <span class=" badge bg-danger">
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    class="icon icon-tabler icon-tabler-trash"
                                                                    width="24" height="24" viewBox="0 0 24 24"
                                                                    stroke-width="2" stroke="currentColor" fill="none"
                                                                    stroke-linecap="round" stroke-linejoin="round">
                                                                    <path stroke="none" d="M0 0h24v24H0z"
                                                                        fill="none"></path>
                                                                    <path d="M4 7l16 0"></path>
                                                                    <path d="M10 11l0 6"></path>
                                                                    <path d="M14 11l0 6"></path>
                                                                    <path
                                                                        d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12">
                                                                    </path>
                                                                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3">
                                                                    </path>
                                                                </svg>
                                                            </span>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <p></p>

                                {{ $produk->links('vendor.pagination.bootstrap-5') }}

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>


    <div class="modal modal-blur fade" id="modal-tambah" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xs modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('produk.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">

                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label class="form-label">Kode Produk</label>
                                    <input type="text" class="form-control" name="kode_produk" id="kode_produk">
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label class="form-label">Nama Produk</label>
                                    <input type="text" class="form-control" name="nama_produk" id="nama_produk">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">Batal</a>
                        <button type="submit" class="btn btn-primary text-white ms-auto">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal modal-blur fade" id="modal-edit" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="" method="POST" id="formEdit">
                    @csrf
                    <div class="modal-body">
                        <div class="row">

                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label class="form-label">Kode Produk</label>
                                    <input type="text" class="form-control" name="kode_produk" id="kode_pro">
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label class="form-label">Nama Produk</label>
                                    <input type="text" class="form-control" name="nama_produk" id="nama_pro">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">Batal</a>
                        <button type="submit" class="btn btn-primary text-white ms-auto">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function() {
            $("#modal-edit").on("show.bs.modal", function(event) {
                var button = $(event.relatedTarget); // Tombol yang membuka modal
                var id = button.data("id"); // Ambil data-id dari tombol

                // Kirim permintaan AJAX ke route yang mengambil data berdasarkan ID
                $.ajax({
                    url: "/admin/produk/" + id + "/edit",
                    type: "GET",
                    dataType: "json",
                    success: function(response) {
                        // Isi modal dengan data yang diterima
                        var da = JSON.stringify(response)
                        var data = JSON.parse(da)
                        var hasil = data[0]
                        var kode = hasil.kode_produk
                        var nama = hasil.nama_produk

                        $("#kode_pro").val(kode);
                        $("#nama_pro").val(nama);

                    },
                    error: function(xhr, status, error) {
                        // Tindakan jika terjadi kesalahan dalam permintaan AJAX
                        console.error("Error:", xhr.responseText);
                    },
                });
            });

            //Simpan
            $('#formEdit').submit(function(e) {
                e.preventDefault();
                var kode = $('#kode_pro').val();
                var nama = $('#nama_pro').val();

                // Kirim permintaan AJAX ke route yang mengambil data berdasarkan ID
                $.ajax({
                    url: "/admin/produk/" + kode,
                    method: "put",
                    dataType: "json",
                    data: {
                        kode_produk: kode,
                        nama_produk: nama,
                        rate: 0,
                        jumlah_pengajuan: 0,
                        _token: "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        // Tangani respon sukses dari server
                        if (response.status === "success") {
                            Swal.fire({
                                title: 'Succes',
                                text: 'Data berhasil diupdate',
                                icon: 'success', // success, error, warning, info, question
                                confirmButtonText: 'OK'
                            }).then(function() {
                                $("#modal-edit").hide();
                                location.reload();
                            });
                        } else if (response.status === "error") {
                            // Jika error, tampilkan SweetAlert error
                            Swal.fire({
                                icon: "error",
                                title: "Data anda gagal diupdate",
                                text: response.message
                            });
                        }
                        console.log(response)
                    },
                    error: function(xhr, status, error) {
                        // Tangani kesalahan pada AJAX request (jika terjadi)
                        // Tampilkan SweetAlert error
                        Swal.fire({
                            icon: "error",
                            title: "Kesalahan!",
                            text: "Terjadi kesalahan saat mengirim permintaan ke server."
                        });
                    }
                });
            })

        });
    </script>
@endsection
