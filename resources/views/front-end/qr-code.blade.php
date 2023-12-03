@extends('front-end.templates.app')
@section('title', 'Verifikasi')

@section('content')
    <div class="content-wrapper">
        <div class="container">
            <section class="content-header">
                <h1>
                    VERFIKASI
                </h1>
                <ol class="breadcrumb">
                    <li>
                        <a href="/"><i class="fa fa-dashboard"></i> Home</a>
                    </li>
                    <li class="active">Verfikasi</li>
                </ol>
            </section>

            <section class="content">

                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Data Verifikasi</h3>
                    </div>
                    <div class="box-body">

                        <table class="table text-justify">
                            <thead>
                                <tr class="bg-blue">
                                    <th class="text-center" colspan="2">VERFIKASI QR-CODE</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td colspan="2">
                                        Dengan ini, kami dengan senang hati memberitahukan bahwa permohonan kredit yang
                                        diajukan oleh <b>[ {{ $data->nama_nasabah }} ]</b> dengan kode pengajuan <b>[
                                            {{ $data->kode_pengajuan }} ]</b> telah disetujui oleh :

                                        <p></p>

                                        <div>
                                            <b>Jabatan</b> : {{ $data->role_name }} <br>
                                            <b>Nama Lengkap</b> : {{ $data->nama_user }}
                                        </div>

                                        <p></p>

                                        Kami telah meninjau dengan seksama informasi yang disediakan dalam aplikasi kredit
                                        tersebut dan menyimpulkan bahwa peminjam memenuhi persyaratan yang ditetapkan.

                                        <p></p>

                                        <div>
                                            <b>Produk</b> : {{ $data->kode_produk }} - {{ $data->nama_produk }} <br>
                                            <b>Plafon</b> :
                                            {{ 'Rp. ' . ' ' . number_format($data->usulan_plafon, 0, ',', '.') }}
                                            <br>
                                            <b>Jangka Waktu</b> : {{ $data->jangka_waktu }} Bulan <br>
                                            <b>Suku Bunga</b> : {{ $data->suku_bunga }}% <br>
                                            <b>Metode RPS</b> : {{ $data->metode_rps }} <br>
                                            <b>B. Provisi</b> : {{ $data->b_provisi }}% <br>
                                            <b>B. Administrasi</b> : {{ $data->b_admin }}% <br>
                                            <b>Repayment Capacity</b> : {{ $data->rc }}%
                                        </div>

                                        <p></p>

                                        Kredit ini diberikan untuk tujuan yang telah dijelaskan dalam aplikasi, dan kami
                                        mengharapkan bahwa peminjam akan menggunakan dana dengan bertanggung jawab sesuai
                                        dengan ketentuan yang telah disepakati.

                                    </td>
                                </tr>

                                <tr>
                                    <td class="text-center" colspan="2">
                                        Pamanukan, {{ $data->tanggal_notifikasi }}
                                        <br>
                                        <img src="{{ asset('storage/image/tanda_tangan/' . $data->ttd) }}" width="100"
                                            height="100">
                                        <br>
                                        <b>{{ $data->nama_user }}</b>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                </div>

            </section>
        </div>
    </div>
@endsection
