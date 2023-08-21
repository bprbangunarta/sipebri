@extends('templates.app')
@section('title', 'Otorisasi Pengajuan')

@section('content')
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
                                            Pendaftaran
                                        </div>
                                        <h2 class="page-title">
                                            Otorisasi Pengajuan
                                        </h2>
                                    </div>
                                    <!-- Page title actions -->
                                    <div class="col-auto ms-auto d-print-none">
                                        <div class="btn-list">
                                            <a href="#" class="btn btn-primary">
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
                                    <div class="col-3 d-none d-md-block border-end">
                                        <div class="card-body">
                                            @include('templates.menu-pendaftaran', [
                                                'nasabah' => $data->kode_nasabah,
                                            ])

                                        </div>
                                    </div>

                                    <div class="col d-flex flex-column">
                                        <form
                                            action="{{ route('validasiotor', ['validasiotor' => $otorisasi->kode_nasabah]) }}"
                                            method="post">
                                            @csrf
                                            <div class="card-body">
                                                <div class="card mb-3">
                                                    <div class="card-stamp">
                                                        <div class="card-stamp-icon bg-yellow">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon"
                                                                width="24" height="24" viewBox="0 0 24 24"
                                                                stroke-width="2" stroke="currentColor" fill="none"
                                                                stroke-linecap="round" stroke-linejoin="round">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none">
                                                                </path>
                                                                <path
                                                                    d="M10 5a2 2 0 0 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6">
                                                                </path>
                                                                <path d="M9 17v1a3 3 0 0 0 6 0v-1"></path>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <h3 class="card-title">Otorisasi Pengajuan</h3>
                                                        <p class="text-muted">
                                                            Apakah Anda yakin semua data sudah benar? jika iya silahkan
                                                            lakukan otorisasi. Jika terjadi perubahan data<br>
                                                            dikemudian hari maka perlu untuk melakukan proses otorisasi
                                                            ulang.
                                                        </p>
                                                    </div>
                                                </div>

                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-vcenter">
                                                        <thead>
                                                            <tr>
                                                                <th class="text-center">Informasi</th>
                                                                <th class="text-center" width="10%">Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>Pengisian Data Nasabah</td>
                                                                <input type="text" value="{{ $otorisasi->nasabah }}"
                                                                    name="nasabah" hidden>
                                                                @if ($otorisasi->nasabah == 1)
                                                                    <td class="text-center">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            class="icon text-green" width="24"
                                                                            height="24" viewBox="0 0 24 24"
                                                                            stroke-width="2" stroke="currentColor"
                                                                            fill="none" stroke-linecap="round"
                                                                            stroke-linejoin="round">
                                                                            <path stroke="none" d="M0 0h24v24H0z"
                                                                                fill="none">
                                                                            </path>
                                                                            <path d="M5 12l5 5l10 -10"></path>
                                                                        </svg>
                                                                    </td>
                                                                @else
                                                                    <td class="text-center">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            class="icon text-red" width="24"
                                                                            height="24" viewBox="0 0 24 24"
                                                                            stroke-width="2" stroke="currentColor"
                                                                            fill="none" stroke-linecap="round"
                                                                            stroke-linejoin="round">
                                                                            <path stroke="none" d="M0 0h24v24H0z"
                                                                                fill="none">
                                                                            </path>
                                                                            <path d="M18 6l-12 12"></path>
                                                                            <path d="M6 6l12 12"></path>
                                                                        </svg>
                                                                    </td>
                                                                @endif
                                                            </tr>
                                                            <tr>
                                                                <td>Pengisian Data Pendamping</td>
                                                                <input type="text" value="{{ $otorisasi->pendamping }}"
                                                                    name="pendamping" hidden>
                                                                @if ($otorisasi->pendamping == 1)
                                                                    <td class="text-center">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            class="icon text-green" width="24"
                                                                            height="24" viewBox="0 0 24 24"
                                                                            stroke-width="2" stroke="currentColor"
                                                                            fill="none" stroke-linecap="round"
                                                                            stroke-linejoin="round">
                                                                            <path stroke="none" d="M0 0h24v24H0z"
                                                                                fill="none">
                                                                            </path>
                                                                            <path d="M5 12l5 5l10 -10"></path>
                                                                        </svg>
                                                                    </td>
                                                                @else
                                                                    <td class="text-center">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            class="icon text-red" width="24"
                                                                            height="24" viewBox="0 0 24 24"
                                                                            stroke-width="2" stroke="currentColor"
                                                                            fill="none" stroke-linecap="round"
                                                                            stroke-linejoin="round">
                                                                            <path stroke="none" d="M0 0h24v24H0z"
                                                                                fill="none">
                                                                            </path>
                                                                            <path d="M18 6l-12 12"></path>
                                                                            <path d="M6 6l12 12"></path>
                                                                        </svg>
                                                                    </td>
                                                                @endif
                                                            </tr>
                                                            <tr>
                                                                <td>Pengisian Data Pengajuan</td>
                                                                <input type="text" value="{{ $otorisasi->pengajuan }}"
                                                                    name="pengajuan" hidden>
                                                                @if ($otorisasi->pengajuan == 1)
                                                                    <td class="text-center">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            class="icon text-green" width="24"
                                                                            height="24" viewBox="0 0 24 24"
                                                                            stroke-width="2" stroke="currentColor"
                                                                            fill="none" stroke-linecap="round"
                                                                            stroke-linejoin="round">
                                                                            <path stroke="none" d="M0 0h24v24H0z"
                                                                                fill="none">
                                                                            </path>
                                                                            <path d="M5 12l5 5l10 -10"></path>
                                                                        </svg>
                                                                    </td>
                                                                @else
                                                                    <td class="text-center">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            class="icon text-red" width="24"
                                                                            height="24" viewBox="0 0 24 24"
                                                                            stroke-width="2" stroke="currentColor"
                                                                            fill="none" stroke-linecap="round"
                                                                            stroke-linejoin="round">
                                                                            <path stroke="none" d="M0 0h24v24H0z"
                                                                                fill="none">
                                                                            </path>
                                                                            <path d="M18 6l-12 12"></path>
                                                                            <path d="M6 6l12 12"></path>
                                                                        </svg>
                                                                    </td>
                                                                @endif
                                                            </tr>
                                                            <tr>
                                                                <td>Pengisian Data Agunan</td>
                                                                <td class="text-center">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        class="icon text-green" width="24"
                                                                        height="24" viewBox="0 0 24 24"
                                                                        stroke-width="2" stroke="currentColor"
                                                                        fill="none" stroke-linecap="round"
                                                                        stroke-linejoin="round">
                                                                        <path stroke="none" d="M0 0h24v24H0z"
                                                                            fill="none">
                                                                        </path>
                                                                        <path d="M5 12l5 5l10 -10"></path>
                                                                    </svg>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Pengisian Data Survayor</td>
                                                                <input type="text" value="{{ $otorisasi->survei }}"
                                                                    name="survei" hidden>
                                                                @if ($otorisasi->survei == 1)
                                                                    <td class="text-center">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            class="icon text-green" width="24"
                                                                            height="24" viewBox="0 0 24 24"
                                                                            stroke-width="2" stroke="currentColor"
                                                                            fill="none" stroke-linecap="round"
                                                                            stroke-linejoin="round">
                                                                            <path stroke="none" d="M0 0h24v24H0z"
                                                                                fill="none">
                                                                            </path>
                                                                            <path d="M5 12l5 5l10 -10"></path>
                                                                        </svg>
                                                                    </td>
                                                                @else
                                                                    <td class="text-center">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            class="icon text-red" width="24"
                                                                            height="24" viewBox="0 0 24 24"
                                                                            stroke-width="2" stroke="currentColor"
                                                                            fill="none" stroke-linecap="round"
                                                                            stroke-linejoin="round">
                                                                            <path stroke="none" d="M0 0h24v24H0z"
                                                                                fill="none">
                                                                            </path>
                                                                            <path d="M18 6l-12 12"></path>
                                                                            <path d="M6 6l12 12"></path>
                                                                        </svg>
                                                                    </td>
                                                                @endif
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="card-footer bg-transparent mt-auto">
                                                <div class="btn-list justify-content-end">
                                                    <button type="submit" class="btn btn-primary">
                                                        Otorisasi
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
@endsection
