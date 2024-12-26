@extends('front-end.templates.menuTracking', ['kode' => $data->kode_pengajuan])
@section('title', 'Tracking Pengajuan Kredit')

@section('content')
    <div class="tab-content" style="height: 82vh; overflow-y: auto;">
        <div class="tab-pane active">
            <table class="table table-bordered">
                <thead>
                    <th class="text-center">TRACKING PENGAJUAN KREDIT</th>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <b>Pemeriksaan Dokumen</b> <br>
                            {{ $data->pemeriksaan_dokumen ?? '-' }} <br>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <b>Proses Survey</b> <br>
                            {{ $data->proses_survey ?? '-' }} <br>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <b>Analisa Kredit</b> <br>
                            {{ $data->analisa_kredit ?? '-' }} <br>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <b>Keputusan Komite</b> <br>
                            {{ $data->keputusan_komite && in_array($data->status, ['Disetujui', 'Ditolak', 'Dibatalkan']) ? $data->keputusan_komite : '-' }}
                            <br>
                            @if ($data->status == 'Disetujui')
                                <span class="label label-success">{{ $data->status }}</span>
                            @elseif(in_array($data->status, ['Ditolak', 'Dibatalkan']))
                                <span class="label label-danger">{{ $data->status }}</span>
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <b>Akad Kredit</b> <br>
                            {{ $data->akad_kredit ?? '-' }} <br>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
