@extends('front-end.templates.app')
@section('title', 'Tracking Pengajuan Kredit')

@section('content')
<div class="content-wrapper">
    <div class="container">
      <section class="content-header">
        <h1>
          TRACKING PENGAJUAN
        </h1>
        <ol class="breadcrumb">
          <li>
            <a href="/"><i class="fa fa-dashboard"></i> Home</a>
          </li>
          <li class="active">Tracking Pengajuan</li>
        </ol>
      </section>

        <section class="content">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <form action="#" method="POST">
                        @csrf
                        <div style="width: 100%;">
                            <input type="text" class="form-control text-center text-uppercase" style="width: 100%;border:1px solid #0173B7;" name="kode" placeholder="Kode Tracking">
                        </div>
    
                        <div style="width: 100%;margin-top:5px;">
                            <button class="btn bg-blue" style="width: 100%;">SUBMIT</button>
                        </div>
                    </form>
                </div>

                <div class="box-body">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td>
                                    <b>Pemeriksaan Dokumen</b> <br>
                                    2023-11-27 15:10:39 <br>
                                    Proses verifikasi dan peninjauan dokumen untuk keperluan identifikasi atau keabsahan.
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <b>Proses Survey</b> <br>
                                    2023-11-27 15:10:39 <br>
                                    Pengumpulan data untuk analisis dan pemahaman terhadap nasabah atau calon debitur.
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <b>Analisa Kredit</b> <br>
                                    2023-11-27 15:10:39 <br>
                                    Penilaian risiko, kelayakan dan kemampuan keuangan untuk keputusan persetujuan kredit.
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <b>Keputusan Komite</b> <br>
                                    2023-11-27 15:10:39 <br>
                                    Berdasarkan hasil pertimbangkan komite permohonan kredit Anda
                                    <span class="label label-success">Disetujui</span>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <b>Akad Kredit</b> <br>
                                    2023-11-27 15:10:39 <br>
                                    Perjanjian hukum dengan peminjam yang mengatur syarat, jumlah, dan jangka waktu pinjaman.
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <b>Pencairan Dana</b> <br>
                                    2023-11-27 15:10:39 <br>
                                    Proses penyaluran dana kepada peminjam setelah persetujuan dan persyaratan terpenuhi.
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <b>Selesai</b> <br>
                                    2023-11-27 15:10:39 <br>
                                    Proses pemberian kredit selesai.
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
