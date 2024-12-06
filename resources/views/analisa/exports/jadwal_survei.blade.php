<table>
    <thead>
        <tr>
            <th>NO</th>
            <th>NAMA CALON DEBITUR</th>
            <th>ALAMAT</th>
            <th>PRODUK</th>
            <th>KANTOR</th>
            <th>TANGGAL DAFTAR</th>
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
                <td>{{ $item->produk_kode }}</td>
                <td>{{ $item->kantor_kode }}</td>
                <td>{{ $item->tanggal }}</td>
                @foreach ($users as $user)
                    <td>
                        @if ($user->nama_user === $item->nama_user)
                            ☑
                        @else
                        @endif
                    </td>
                @endforeach
            </tr>
        @empty
            <tr>
                <td colspan="{{ 6 + count($users) }}">Data tidak ditemukan.</td>
            </tr>
        @endforelse
    </tbody>
</table>
