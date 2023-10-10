@extends('templates.app')
@section('title', 'Memorandum')
@yield('jquery')
@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class=" card-header">

                            <div iv class="container-xl">
                                <div div class="row g-2 align-items-center">

                                    @include('templates.header-analisa', [$data])

                                    <div class="col-auto ms-auto d-print-none">
                                        <div class="btn-list">
                                            <a href="{{ route('jaminan.index', ['pengajuan' => $data->kd_pengajuan]) }}"
                                                class="btn btn-primary">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="icon icon-tabler icon-tabler-arrow-left" width="24"
                                                    height="24" viewBox="0 0 24 24" stroke-width="2"
                                                    stroke="currentColor" fill="none" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <path d="M5 12l14 0"></path>
                                                    <path d="M5 12l6 6"></path>
                                                    <path d="M5 12l6 -6"></path>
                                                </svg>
                                                Kembali
                                            </a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="card-body border-bottom py-3">
                            <div class="card">
                                <div class="row g-0">

                                    @include('templates.menu-analisa', [
                                        'pengajuan' => $data->kd_pengajuan,
                                    ])

                                    <div class="col d-flex flex-column">
                                        <div class="card-body">
                                            <form action="#" method="post">
                                                @csrf
                                                <table class="table table-bordered table-vcenter fs-5">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" colspan="6">Rekomendasi</th>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-center">Max Plafond</th>
                                                            <th class="text-center">Usulan Plafond</th>
                                                            <th class="text-center">Jangka Waktu</th>
                                                            <th class="text-center">R. Capacity</th>
                                                            <th class="text-center">T. Jaminan</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <input type="text" class="form-control" name="max_plafon"
                                                                    id="maxp"
                                                                    value="{{ 'Rp. ' . ' ' . number_format($memorandum['max_plafon'], 0, ',', '.') ?? 00 }}"
                                                                    readonly>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control"
                                                                    name="usulan_plafon" id="usualanp">
                                                            </td>
                                                            <td>
                                                                <input type="number" class="form-control text-center"
                                                                    name="jangka_waktu"
                                                                    value="{{ $memorandum['jangka_waktu'] }}">
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control text-center"
                                                                    name="rc"
                                                                    value="{{ $memorandum['rc'] . ' ' . '%' }}">
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control"
                                                                    name="total_jaminan"
                                                                    value="{{ 'Rp. ' . ' ' . number_format($memorandum['total_taksasi'], 0, ',', '.') ?? 00 }}">
                                                            </td>
                                                        </tr>
                                                        <thead>
                                                            <th class="text-center" width="25%">Sebelum Realisasi</th>
                                                            <td colspan="4" class="text-center">
                                                                <input type="text" class="form-control" name=""
                                                                    id="">
                                                            </td>
                                                        </thead>
                                                        <thead>
                                                            <th class="text-center" width="25%">Syarat Tambahan</th>
                                                            <td colspan="4" class="text-center">
                                                                <input type="text" class="form-control" name=""
                                                                    id="">
                                                            </td>
                                                        </thead>
                                                        <thead>
                                                            <th class="text-center" width="25%">Syarat Lainnya</th>
                                                            <td colspan="4" class="text-center">
                                                                <input type="text" class="form-control" name=""
                                                                    id="">
                                                            </td>
                                                        </thead>
                                                    </tbody>
                                                </table>
                                                <div class="card-footer bg-transparent mt-auto">
                                                    <div class="btn-list justify-content-end">
                                                        <button href="#" class="btn btn-primary">
                                                            Simpan
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        var usualanp = document.getElementById("usualanp");
        if (usualanp) {
            usualanp.addEventListener("keyup", function(e) {
                usualanp.value = formatRupiah(this.value, "Rp. ");
            });
        }

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

        //validasi agar usulan plafon tidak lebih besar dari max plafon
        $(document).ready(function() {
            $('#usualanp').on('input', function() {
                var usulanplafon = $('#usualanp').val()
                var maxp = $('#maxp').val()
                var rmaxp = parseFloat(maxp.replace(/[^\d]/g, "")) || 0;
                var rusulanplafon = parseFloat(usulanplafon.replace(/[^\d]/g, "")) || 0;

                if (rusulanplafon > rmaxp) {
                    $('#usualanp').val(maxp)
                }
            })
        })
    </script>
@endsection
