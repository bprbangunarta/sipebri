<div class="list-group list-group-transparent">
    <a href="{{ route('nasabah.edit', ['nasabah' => $nasabah]) }}"
        class="list-group-item list-group-item-action d-flex align-items-center {{ request()->is('nasabah/edit') ? 'active' : '' }}">Data
        Nasabah</a>
    <a href="{{ route('pendamping.edit', ['nasabah' => $nasabah]) }}"
        class="list-group-item list-group-item-action d-flex align-items-center {{ request()->is('pendamping/edit') ? 'active' : '' }}">Data
        Pendamping</a>
    <a href="{{ route('pengajuan.edit', ['nasabah' => $nasabah]) }}"
        class="list-group-item list-group-item-action d-flex align-items-center {{ request()->is('pengajuan/edit') ? 'active' : '' }}">Data
        Pengajuan</a>
    <a href="{{ route('pengajuan.agunan', ['nasabah' => $nasabah]) }}"
        class="list-group-item list-group-item-action d-flex align-items-center {{ request()->is('pengajuan/agunan') ? 'active' : '' }}">Data
        Agunan</a>
    <a href="{{ route('survei.edit', ['nasabah' => $nasabah]) }}"
        class="list-group-item list-group-item-action d-flex align-items-center {{ request()->is('survei/edit') ? 'active' : '' }}">Data
        Survayor</a>
    <a href="/pendaftaran/data/konfirmasi"
        class="list-group-item list-group-item-action d-flex align-items-center {{ request()->is('pengajuan/konfirmasi') ? 'active' : '' }}">Konfirmasi
        Data</a>
</div>
