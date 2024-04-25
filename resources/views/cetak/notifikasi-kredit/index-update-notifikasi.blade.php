@extends('theme.app')
@section('title', 'Notifikasi Kredit')

@section('content')
    <div class="content-wrapper">

        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-bell"></i>
                            <h3 class="box-title">UPDATE NOTIFIKASI KREDIT</h3>

                            <div class="box-tools">
                                <form id="searchForm">
                                    <div class="input-group input-group-sm hidden-xs" style="width: 305px;">
                                        <input type="text" class="form-control text-uppercase pull-right"
                                            style="width: 180px;font-size:11.4px;" name="keyword" id="keywords"
                                            value="{{ request('keyword') }}" placeholder="Kode Pengajuan">

                                        <div class="input-group-btn">
                                            <button type="button" class="btn bg-blue" id="searchButton">
                                                <i class="fa fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="box-body" style="overflow: auto;white-space: nowrap;width: 100%;">
                            <table class="table table-bordered" style="font-size:12px;" id="data_table">
                                <thead>
                                    <tr class="bg-blue">
                                        <th class="text-center" width="3%">NO</th>
                                        <th class="text-center">TANGGAL CREATE</th>
                                        <th class="text-center">TANGGAL UPDATE</th>
                                        <th class="text-center">KODE</th>
                                        <th class="text-center">NAMA NASABAH</th>
                                        <th class="text-center">ALAMAT</th>
                                        <th class="text-center">WIL</th>
                                        <th class="text-center">PLAFON</th>
                                        <th class="text-center">NO. NOTIFIKASI</th>
                                        <th class="text-center">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <marquee behavior="scroll" direction="left">
                        <b>Update notifikasi digunakan jika tanggal telah expired (kadaluarsa).</b>
                    </marquee>
                    <ol>
                        <li>Masukan kode pengajuan lalu klik tombol
                            &nbsp;&nbsp;<button type="button" class="btn bg-blue btn-sm">
                                <i class="fa fa-search"></i></button>.
                        </li>
                        <li>Setelah data tampil, klik tombol &nbsp;&nbsp;
                            <button class="btn btn-success btn-sm">
                                <i class="fa fa-pencil-square-o" aria-hidden="true"></i></button>&nbsp;&nbsp; untuk
                            melakukan perubahan tanggal notifikasi.
                        </li>
                    </ol>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('myscript')
    <script>
        //-------------------------------------------------------------------
        // Search
        function search() {
            const keyword = $('#keywords').val()
            $.ajax({
                type: 'GET',
                url: "{{ route('get.update_notifikasi') }}",
                dataType: 'json',
                data: {
                    data: keyword
                },
                success: function(response) {

                    if (response.length === 0) {
                        Swal.fire({
                            title: "(-_-)",
                            text: "Data tidak ditemukan",
                            icon: ''
                        })
                    } else {
                        updateTable(response);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            })
        }

        function handleKeywordKeyPress(e) {
            if (e.which === 13) {
                e.preventDefault();
                search();
            }
        }

        $('#searchButton').click(search)
        $('#keyword').keypress(search);
        //-------------------------------------------------------------------

        //-------------------------------------------------------------------
        // Update Data Table
        function updateTable(data) {
            var tableBody = $('#data_table tbody');
            tableBody.empty();

            data.forEach(function(item, index) {
                if (item.update == null) {
                    item.update = ""
                } else {
                    item.update = item.update.split(" ")
                    item.update = item.update[0]
                }

                item.input = item.input.split(" ")
                item.input = item.input[0]

                var row = '<tr>' +
                    '<td class="text-center" style="vertical-align: middle;">' + (index + 1) + '</td>' +
                    '<td class="text-center" style="vertical-align: middle;">' + item.input + '</td>' +
                    '<td class="text-center" style="vertical-align: middle;">' + item.update + '</td>' +
                    '<td class="text-center" style="vertical-align: middle;">' + item.kode_pengajuan + '</td>' +
                    '<td class="text-center" style="vertical-align: middle;">' + item.nama_nasabah + '</td>' +
                    '<td class="text-center" style="vertical-align: middle;">' + item.alamat_ktp + '</td>' +
                    '<td class="text-center" style="vertical-align: middle;">' + item.kantor_kode + '</td>' +
                    '<td class="text-center" style="vertical-align: middle;">' + formatRupiah(item.plafon) +
                    '<td class="text-center" style="vertical-align: middle;">' + item.no_notifikasi + '</td>' +
                    '<td class="text-center" style="vertical-align: middle;">' +
                    '<button class="btn btn-success btn-sm" onclick="buttonUpdate(\'' + item.kode_pengajuan +
                    '\')">' +
                    '<i class="fa fa-pencil-square-o" aria-hidden="true"></i>' +
                    '</button>' + '</td>' +
                    '</tr>';
                tableBody.append(row);
            });
        }
        //-------------------------------------------------------------------

        //-------------------------------------------------------------------
        // Format Rupiah
        function formatRupiah(angka, prefix) {
            var number_string = angka.replace(/[^,\d]/g, "").toString(),
                split = number_string.split(","),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                separator = sisa ? "." : "";
                rupiah += separator + ribuan.join(".");
            }

            rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
            return prefix == undefined ? rupiah : rupiah ? "Rp. " + rupiah : "";
        }
        //-------------------------------------------------------------------

        //-------------------------------------------------------------------
        // Tombol Update
        function buttonUpdate(data) {
            const tokenElement = document.querySelector('meta[name="csrf-token"]')
            const token = tokenElement.getAttribute('content')
            Swal.fire({
                title: "Apakah anda yakin?",
                text: "akan update notifikasi",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, Update!",
                cancelButtonText: "Batal",
            }).then((update) => {
                if (update.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('update.update_notifikasi') }}",
                        dataType: 'json',
                        data: {
                            data: data,
                            _token: token,
                        },
                        success: function(response) {
                            if (response[0] == 'berhasil') {
                                Swal.fire({
                                    title: "Berhasil!",
                                    text: "Data telah diupdate!",
                                    icon: "success"
                                }).then((result) => {

                                    updateTable(response[1])
                                });
                            } else {
                                Swal.fire({
                                    title: "Gagal!",
                                    text: "Data gagal diupdate!",
                                    icon: "error"
                                })
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    })
                }
            });
        }
        //-------------------------------------------------------------------
    </script>
@endpush
