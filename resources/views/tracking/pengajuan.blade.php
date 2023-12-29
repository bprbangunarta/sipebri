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

                            <div class="box-tools">
                                <form action="{{ route('pengajuan.data') }}" method="GET">
                                    <div class="input-group input-group-sm hidden-xs" style="width: 305px;">
                                        <div class="input-group-btn">
                                            <a href="{{ route('pengajuan.data') }}"  class="btn bg-blue btn-sm pull-right">
                                                <i class="fa fa-arrow-left"></i>&nbsp; KEMBALI
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="box-body" style="overflow: auto;white-space: nowrap;width: 100%;">
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
                                        <td class="text-center">{{ $data->pemeriksaan_dokumen }}</td>
                                        <td>
                                            @if (!is_null($data->pemeriksaan_dokumen))
                                                Proses verifikasi dan peninjauan dokumen untuk keperluan identifikasi
                                                atau
                                                keabsahan.
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Proses Survey</td>
                                        <td class="text-center">{{ $data->proses_survey }}</td>
                                        <td>
                                            @if (!is_null($data->proses_survey))
                                                Pengumpulan data untuk analisis dan pemahaman terhadap nasabah atau
                                                calon
                                                debitur.
                                            @endif

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Analisa Kredit</td>
                                        <td class="text-center">{{ $data->analisa_kredit }}</td>
                                        <td>
                                            @if (!is_null($data->analisa_kredit))
                                                Penilaian risiko, kelayakan dan kemampuan keuangan untuk keputusan
                                                persetujuan kredit.
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Keputusan Komite</td>
                                        <td class="text-center" style="vertical-align: middle;">
                                            {{ $data->keputusan_komite }}</td>
                                        <td>
                                            @if (!is_null($data->keputusan_komite))
                                                Berdasarkan hasil pertimbangkan komite permohonan kredit Anda
                                                @if ($data->status == 'Disetujui')
                                                    <span class="label label-success">Disetujui</span>
                                                @endif
                                                @if ($data->status == 'Ditolak')
                                                    <span class="label label-danger">Ditolak</span>
                                                @endif
                                                @if ($data->status == 'Dibatalkan')
                                                    <span class="label label-danger">Dibatalkan</span>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Akad Kredit</td>
                                        <td class="text-center">{{ $data->akad_kredit }}</td>
                                        <td>
                                            @if (!is_null($data->akad_kredit))
                                                Perjanjian hukum dengan peminjam yang mengatur syarat, jumlah, dan
                                                jangka
                                                waktu pinjaman.
                                            @endif

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Pencairan Dana</td>
                                        <td class="text-center">{{ $data->pencairan_dana }}</td>
                                        <td>
                                            @if (!is_null($data->pencairan_dana))
                                                Proses penyaluran dana kepada peminjam setelah persetujuan dan
                                                persyaratan
                                                terpenuhi.
                                            @endif

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Selesai</td>
                                        <td class="text-center">{{ $data->selesai }}</td>
                                        <td>
                                            @if (!is_null($data->selesai))
                                                Proses pemberian kredit selesai.
                                            @endif

                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
