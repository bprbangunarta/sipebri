<table>
    <thead>
        <tr>
            <th>NO</th>
            <th>CALON NASABAH</th>
            <th>ALAMAT</th>
            <th>NO HP</th>
            <th>PETUGAS</th>
            <th>PROSPEK 1 VIA</th>
            <th>PROSPEK 2 VIA</th>
            <th>PROSPEK 3 VIA</th>
            <th>TGL PROSPEK 1</th>
            <th>TGL PROSPEK 2</th>
            <th>TGL PROSPEK 3</th>
            <th>KETERANGAN 1</th>
            <th>KETERANGAN 2</th>
            <th>KETERANGAN 3</th>
            <th>TGL CLOSING</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($data as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->calon_nasabah }}</td>
                <td>{{ $item->alamat }}</td>
                <td>{{ $item->no_hp }}</td>
                <td>{{ $item->nama_user }}</td>
                <td>{{ $item->prosfek1_via }}</td>
                <td>{{ $item->prosfek2_via }}</td>
                <td>{{ $item->prosfek3_via }}</td>
                <td>{{ $item->tgl_prosfek1 }}</td>
                <td>{{ $item->tgl_prosfek2 }}</td>
                <td>{{ $item->tgl_prosfek3 }}</td>
                <td>{{ $item->ket1 }}</td>
                <td>{{ $item->ket2 }}</td>
                <td>{{ $item->ket3 }}</td>
                <td>{{ $item->tgl_closing }}</td>
            </tr>
        @empty
        @endforelse
    </tbody>
</table>
