<style>
    th {
        text-align: center;
        vertical-align: middle;
    }
</style>
<table>
    <thead>
        <tr>
            <th rowspan="2">TANGGAL DAFTAR SI</th>
            <th rowspan="2">NO HP</th>
            <th rowspan="2">RESORT</th>
            <th rowspan="2">NO LOAN</th>
            <th rowspan="2">KETERANGAN</th>
            <th rowspan="2">TANGGAL TTD SI</th>
            <th rowspan="2">NAMA</th>
            <th rowspan="2">No TT</th>
            <th rowspan="2">NO. REKENING KEB HANA BANK</th>
            <th rowspan="2">NO HP</th>
            <th rowspan="2">NIK KTP</th>
            <th rowspan="2">PLAFON</th>
            <th rowspan="2">JKW (BLN)</th>
            <th rowspan="2">JUMLAH (Rp.)</th>
            <th colspan="2">WAKTU PELAKSANAAN
            </th>
        </tr>
        <tr>
            <th>DARI TANGGAL</th>
            <th>S/D TANGGAL</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($data as $item)
            <tr>
                <td></td>
                <td>{{ $item->no_telp }}</td>
                <td>{{ trim($item->nama_resort) }}</td>
                <td>{{ $item->no_loan }}</td>
                <td></td>
                <td></td>
                <td>{{ $item->nama_nasabah }}</td>
                <td></td>
                <td>{{ $item->no_rekening }}</td>
                <td></td>
                <td>{{ $item->no_identitas }}</td>
                <td>{{ $item->plafon }}</td>
                <td>{{ $item->jangka_waktu }}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        @empty
        @endforelse
    </tbody>
</table>
