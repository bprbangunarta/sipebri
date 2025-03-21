<style>
    th {
        text-align: center;
        vertical-align: middle;
    }
</style>
<table>
    <thead>
        <tr>
            <th>Loan</th>
            <th>No Rek</th>
            <th>Resort</th>
            <th>Nama Debitur</th>
            <th>Plafon</th>
            <th>Nominal SI</th>
            <th>No HP</th>
            <th>Tgl Realisasi</th>
            <th>Tgl SI</th>
            <th>Tenor</th>
            <th>Deviasi SI (Tgl Sekarang)</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($data as $item)
            <tr>
                <td>{{ $item->no_loan }}</td>
                <td>{{ $item->norek }}</td>
                <td>{{ $item->nama_resort }}</td>
                <td>{{ $item->nama_nasabah }}</td>
                <td>{{ $item->plafon }}</td>
                <td>{{ $item->nominal_si }}</td>
                <td>{{ $item->no_telp }}</td>
                <td>{{ $item->tgl_realisasi }}</td>
                <td>{{ $item->tgl_si }}</td>
                <td>{{ $item->jangka_waktu }}</td>
                <td>{{ $item->deviasiSi }}</td>
            </tr>
        @empty
        @endforelse
    </tbody>
</table>
