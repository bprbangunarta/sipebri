@extends('theme.app')
@section('title', 'Tracking Pengajuan')

@section('content')
<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <i class="fa fa-hourglass-2"></i>
                        <h3 class="box-title">TRACKING PENGAJUAN</h3>
                    </div>

                    <div class="box-body">
                        {{-- <div class="col-md-6">
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="bg-blue">
                                        <th class="text-center">INFORMASI NASABAH</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td> <b>KODE : </b> 00339939 [ BARU ]</td>
                                    </tr>
                                    <tr>
                                        <td> <b>NAMA : </b> NELI YOBAN NIASIH</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b>KANTOR</b> : PGD <br>
                                            <b>KTA - JK</b> : 3 BULAN <br>
                                            <b>PLAFON</b> : 3.000.000
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b>ALAMAT</b> <br>
                                            DUSUN CIHANJA RT/RW 09/03 SAWANGAN CIPEUNDEUY SUBANG <br>
                                            DESA: SAWANGAN | KECAMATAN: CIPEUNDEUY
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div> --}}

                        <div class="col-md-12">
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="bg-blue">
                                        <th class="text-center" colspan="3">PROSES PENGAJUAN KREDIT</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center" width="16%">TRACKING</th>
                                        <th class="text-center" width="14%">TANGGAL</th>
                                        <th class="text-center">DETAIL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Pemeriksaan Dokumen</td>
                                        <td class="text-center">2023-11-04 17:38:30</td>
                                        <td>
                                            Proses verifikasi dan peninjauan dokumen untuk keperluan identifikasi atau keabsahan.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Proses Survey</td>
                                        <td class="text-center">2023-11-04 17:38:30</td>
                                        <td>Pengumpulan data untuk analisis dan pemahaman terhadap nasabah atau calon debitur.</td>
                                    </tr>
                                    <tr>
                                        <td>Analisa Kredit</td>
                                        <td class="text-center">2023-11-04 17:38:30</td>
                                        <td>Penilaian risiko, kelayakan dan kemampuan keuangan untuk keputusan persetujuan kredit.</td>
                                    </tr>
                                    <tr>
                                        <td>Keputusan Komite</td>
                                        <td class="text-center" style="vertical-align: middle;">2023-11-04 17:38:30</td>
                                        <td>
                                            Berdasarkan hasil pertimbangkan komite permohonan kredit Anda 
                                            <span class="label label-success">Disetujui</span>
                                            <span class="label label-danger">Ditolak</span>
                                            <span class="label label-danger">Dibatalkan</span></td>
                                    </tr>
                                    <tr>
                                        <td>Akad Kredit</td>
                                        <td class="text-center">2023-11-04 17:38:30</td>
                                        <td>
                                            Perjanjian hukum dengan peminjam yang mengatur syarat, jumlah, dan jangka waktu pinjaman.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Pencairan Dana</td>
                                        <td class="text-center">2023-11-04 17:38:30</td>
                                        <td>
                                            Proses penyaluran dana kepada peminjam setelah persetujuan dan persyaratan terpenuhi.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Selesai</td>
                                        <td class="text-center">2023-11-04 17:38:30</td>
                                        <td>
                                            Proses pemberian kredit selesai.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>
@endsection