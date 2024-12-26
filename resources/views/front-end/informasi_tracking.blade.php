@extends('front-end.templates.menuTracking', ['kode' => $data->kode_pengajuan])
@section('title', 'Tracking Pengajuan Kredit')

@section('content')
    <div class="tab-content" style="height: 82vh; overflow-y: auto;">
        <p style="margin-left: 7px; text-align: justify;">
            Berikut adalah informasi dari calon debitur yang mengajukan kredit ke BPR Bangunarta:
        </p>

        <table style="margin-left: 7px;">
            <tbody>
                <tr style="line-height: 1.5;">
                    <td style="width: 6%" colspan="3"><b>DATA DIRI</b></td>
                </tr>
                <tr style="line-height: 1.5;">
                    <td style="width: 6%">Nama Lengkap</td>
                    <td style="width: 2%">:</td>
                    <td style="width: 20%">{{ $data->nama_nasabah }}</td>
                </tr>
                <tr style="line-height: 1.5;">
                    <td style="width: 6%">Tanggal Lahir</td>
                    <td style="width: 2%">:</td>
                    <td style="width: 20%">{{ $data->tgl_lahir }}</td>
                </tr>
                <tr style="line-height: 1.5;">
                    <td style="width: 6%; vertical-align: text-top;">Alamat</td>
                    <td style="width: 2%; vertical-align: text-top;">:</td>
                    <td style="width: 20%; text-align:justify;">{{ $data->alamat_ktp }}</td>
                </tr>
            </tbody>
        </table>
        <br>
        <table style="margin-left: 7px;">
            <tbody>
                <tr style="line-height: 1.5;">
                    <td style="width: 6%" colspan="3"><b>PENGAJUAN KREDIT</b> </td>
                </tr>
                <tr style="line-height: 1.5;">
                    <td style="width: 6%">Produk</td>
                    <td style="width: 2%">:</td>
                    <td style="width: 20%">{{ $data->produk_kode }} - {{ $data->nama_produk }}</td>
                </tr>
                <tr style="line-height: 1.5;">
                    <td style="width: 6%">Plafon</td>
                    <td style="width: 2%">:</td>
                    <td style="width: 20%">{{ number_format($data->plafon, '0', ',', '.') }}</td>
                </tr>
                <tr style="line-height: 1.5;">
                    <td style="width: 6%">Jangka Waktu</td>
                    <td style="width: 2%">:</td>
                    <td style="width: 20%">{{ $data->jangka_waktu }} Bulan</td>
                </tr>
            </tbody>
        </table>
        <br>
        <table style="margin-left: 7px;">
            <tbody>
                <tr style="line-height: 1.5;">
                    <td style="width: 6%" colspan="3"><b>INFORMASI BERKAS</b> </td>
                </tr>
                <tr style="line-height: 1.5;">
                    <td style="width: 6%">User Pengirim</td>
                    <td style="width: 2%">:</td>
                    <td style="width: 20%">{{ $data->user_pengirim }}</td>
                </tr>
                <tr style="line-height: 1.5;">
                    <td style="width: 6%">User Penerima</td>
                    <td style="width: 2%">:</td>
                    <td style="width: 20%">{{ $data->user_penerima }}</td>
                </tr>
                <tr style="line-height: 1.5;">
                    <td style="width: 6%">Dari Kantor</td>
                    <td style="width: 2%">:</td>
                    <td style="width: 20%">{{ $data->dari_kantor }}</td>
                </tr>
                <tr style="line-height: 1.5;">
                    <td style="width: 6%">Ke Kantor</td>
                    <td style="width: 2%">:</td>
                    <td style="width: 20%">{{ $data->ke_kantor }}</td>
                </tr>
                <tr style="line-height: 1.5;">
                    <td style="width: 6%">Tanggal Kirim</td>
                    <td style="width: 2%">:</td>
                    <td style="width: 20%">{{ $data->tgl_kirim }}</td>
                </tr>
                <tr style="line-height: 1.5;">
                    <td style="width: 6%">Tanggal Terima</td>
                    <td style="width: 2%">:</td>
                    <td style="width: 20%">{{ $data->tgl_terima }}</td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection
