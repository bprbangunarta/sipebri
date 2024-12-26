<table>
    <thead>
        <tr>
            <th rowspan="2">NO</th>
            <th rowspan="2">NAMA CALON DEBITUR</th>
            <th rowspan="2">ALAMAT</th>
            <th rowspan="2">KECAMATAN</th>
            <th rowspan="2">KELURAHAN</th>
            <th rowspan="2">KOTA</th>
            <th rowspan="2">PRODUK</th>
            <th rowspan="2">KANTOR</th>
            <th rowspan="2">TANGGAL DAFTAR</th>
            <th colspan="{{ count($users) }}">NAMA PETUGAS</th>
        </tr>
        <tr>
            @foreach ($users as $user)
                <th>{{ $user->nama_user }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @forelse ($data as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->nama_nasabah }}</td>
                <td>{{ $item->alamat_ktp }}</td>
                <td>{{ $item->kecamatan }}</td>
                <td>{{ $item->kelurahan }}</td>
                <td>{{ $item->kota }}</td>
                <td>{{ $item->produk_kode }}</td>
                <td>{{ $item->kantor_kode }}</td>
                <td>{{ $item->tanggal }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="{{ 6 + count($users) }}">Data tidak ditemukan.</td>
            </tr>
        @endforelse
    </tbody>
</table>
