@extends('templates.app')
@section('title', 'Penjadwalan')

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
                                            Analisa
                                        </div>
                                        <h2 class="page-title">
                                            Proses Analisa
                                        </h2>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body border-bottom py-3" style="margin-top:-7px;">

                            <form action="/contoh" method="GET">
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" name="" id=""
                                        placeholder="Cari Data">
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
                                <table class="table table-bordered table-vcenter fs-5">
                                    <thead>
                                        <tr>
                                            <th class="text-center" width="3%">No</th>
                                            <th class="text-center" width="11%">Kode Debitur</th>
                                            <th class="text-center">Nama Debitur</th>
                                            <th class="text-center">Alamat</th>
                                            <th class="text-center">Wilayah</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center" width="10%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @forelse ($data as $item)
                                            @php
                                                $no = 1;
                                            @endphp
                                            <tr>
                                                <td class="text-center">1</td>
                                                <td class="text-center">{{ $item->kode_nasabah }}</td>
                                                <td>{{ $item->nama_nasabah }}</td>
                                                <td>
                                                    <b>Desa</b> : {{ $item->kelurahan }} <br>
                                                    <b>Kecamatan</b> : {{ $item->kecamatan }}
                                                </td>
                                                <td>{{ $item->nama_kantor }}</td>
                                                <td class="text-center">
                                                    <span class="badge bg-warning-lt">Proses Survey</span>
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('tambah.index', ['pengajuan' => $item->kd_pengajuan]) }}"
                                                        title="Proses Analisa" style="text-decoration: none;">
                                                        <span class="badge bg-warning">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                class="icon icon-tabler icon-tabler-clipboard-list"
                                                                width="24" height="24" viewBox="0 0 24 24"
                                                                stroke-width="2" stroke="currentColor" fill="none"
                                                                stroke-linecap="round" stroke-linejoin="round">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none">
                                                                </path>
                                                                <path
                                                                    d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2">
                                                                </path>
                                                                <path
                                                                    d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z">
                                                                </path>
                                                                <path d="M9 12l.01 0"></path>
                                                                <path d="M13 12l2 0"></path>
                                                                <path d="M9 16l.01 0"></path>
                                                                <path d="M13 16l2 0"></path>
                                                            </svg>
                                                        </span>
                                                    </a>
                                                    <a href="{{ route('analisa5c.analisa', ['pengajuan' => $item->kd_pengajuan]) }}"
                                                        title="Print Data">
                                                        <span class="badge bg-info">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                class="icon icon-tabler icon-tabler-printer" width="24"
                                                                height="24" viewBox="0 0 24 24" stroke-width="2"
                                                                stroke="currentColor" fill="none" stroke-linecap="round"
                                                                stroke-linejoin="round">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none">
                                                                </path>
                                                                <path
                                                                    d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2">
                                                                </path>
                                                                <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4">
                                                                </path>
                                                                <path
                                                                    d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z">
                                                                </path>
                                                            </svg>
                                                        </span>
                                                    </a>
                                                </td>
                                                <input type="text" hidden value="{{ $no++ }}" readonly>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td class="text-center" colspan="7">Tidak ada permohonan analisa.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                <p></p>

                                {{ $data->links('vendor.pagination.bootstrap-5') }}

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
