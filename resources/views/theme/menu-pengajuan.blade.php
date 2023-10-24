<div class="box box-solid">
    <div class="box-header with-border">
        <h3 class="box-title">Info Nasabah</h3>

        <div class="box-tools">
            <button type="button" class="btn btn-box-tool" data-widget="collapse">
                <i class="fa fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="box-body no-padding">
        <ul class="nav nav-pills nav-stacked">
            <li>
                <a href="#"><i class="fa fa-user"></i>{{ $data->nama_nasabah }}</a>
            </li>
            <li>
                <a href="#">
                    <i class="fa fa-bitcoin"></i>
                    {{ $data->plafon = 'Rp. ' . number_format($data->plafon, 0, ',', '.') }} ({{ $data->jangka_waktu }} BULAN)
                </a>
            </li>
        </ul>
    </div>
</div>

<div class="box box-solid">
    <div class="box-header with-border">
        <h3 class="box-title">Menu Pengajuan</h3>

        <div class="box-tools">
            <button type="button" class="btn btn-box-tool" data-widget="collapse">
                <i class="fa fa-minus"></i>
            </button>
        </div>
    </div>

    <div class="box-body no-padding">
        <ul class="nav nav-pills nav-stacked">
            <li class="{{ request()->is('nasabah/edit') ? 'active' : '' }}">
                <a href="{{ route('nasabah.edit', ['nasabah' => $nasabah]) }}">
                    <i class="fa fa-folder-o"></i> Data Nasabah
                </a>
            </li>

            <li class="{{ request()->is('pendamping/edit') ? 'active' : '' }}">
                <a href="{{ route('pendamping.edit', ['nasabah' => $nasabah]) }}">
                    <i class="fa fa-folder-o"></i> Data Pendamping
                </a>
            </li>

            <li class="{{ request()->is('pengajuan/edit') ? 'active' : '' }}">
                <a href="{{ route('pengajuan.edit', ['nasabah' => $nasabah]) }}">
                    <i class="fa fa-folder-o"></i> Data Pengajuan
                </a>
            </li>

            <li class="{{ request()->is('pengajuan/agunan') ? 'active' : '' }}">
                <a href="{{ route('pengajuan.agunan', ['nasabah' => $nasabah]) }}">
                    <i class="fa fa-folder-o"></i> Data Jaminan
                </a>
            </li>

            <li class="{{ request()->is('survei/edit') ? 'active' : '' }}">
                <a href="{{ route('survei.edit', ['nasabah' => $nasabah]) }}">
                    <i class="fa fa-folder-o"></i> Data Surveyor
                </a>
            </li>

            @can('edit pengajuan kredit')
            <li class="{{ request()->is('konfirmasi/pengajuan') ? 'active' : '' }}">
                <a href="{{ route('pengajuan.konfirmasi', ['nasabah' => $nasabah]) }}">
                    <i class="fa fa-folder-o"></i> Konfirmasi Data
                </a>
            </li>
            @endcan

            @can('otorisasi pengajuan kredit')
            <li class="{{ request()->is('otorisasi/pengajuan') ? 'active' : '' }}">
                <a href="{{ route('pengajuan.otorisasi', ['nasabah' => $nasabah]) }}">
                    <i class="fa fa-folder-o"></i> Otorisasi Data
                </a>
            </li>
            @endcan
        </ul>
    </div>
</div>
