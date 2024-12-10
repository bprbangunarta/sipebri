<table>
    <thead>
        <tr>
            <th>NO</th>
            <th>KODE PENGAJUAN</th>
            <th>NAMA</th>
            <th>ALAMAT</th>
            <th>PRODUK</th>
            <th>KANTOR</th>
            <th>PLAFON</th>
            <th>TANGGAL KIRIM</th>
            <th>TANGGAL TERIMA</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($data as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->kode_pengajuan }}</td>
                <td>{{ $item->nama_nasabah }}</td>
                <td>{{ $item->alamat_ktp }}</td>
                <td>{{ $item->produk_kode }}</td>
                <td>{{ $item->dari_kantor }}</td>
                <td>{{ $item->plafon }}</td>
                <td>{{ $item->tgl_kirim }}</td>
                <td>{{ $item->tgl_terima }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="10">Data tidak ditemukan.</td>
            </tr>
        @endforelse
    </tbody>
</table>
