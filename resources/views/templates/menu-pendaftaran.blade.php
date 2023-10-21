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
        Surveyor</a>

    @can('edit pengajuan kredit')
        <a href="{{ route('pengajuan.konfirmasi', ['nasabah' => $nasabah]) }}"
            class="list-group-item list-group-item-action d-flex align-items-center {{ request()->is('konfirmasi/pengajuan') ? 'active' : '' }}">Konfirmasi
            Data</a>
    @endcan

    @can('pengajuan otorisasi')
        <a href="{{ route('pengajuan.otorisasi', ['nasabah' => $nasabah]) }}"
            class="list-group-item list-group-item-action d-flex align-items-center {{ request()->is('otorisasi/pengajuan') ? 'active' : '' }}">Otorisasi
            Data</a>
    @endcan
</div>
