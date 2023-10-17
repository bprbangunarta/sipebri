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
                <a href="#"><i class="fa fa-user"></i> {{ $data->nama_nasabah }}</a>
            </li>
            <li>
                <a href="#">
                    <i class="fa fa-bitcoin"></i>
                    {{ 'Rp.' . ' ' . number_format($data->plafon, 0, ',', '.') . ' ' . '(' . $data->jangka_waktu . ' ' . 'BULAN)' }}
                </a>
            </li>
        </ul>
    </div>
</div>

<div class="box box-solid">
    <div class="box-header with-border">
        <h3 class="box-title">Menu Analisa</h3>

        <div class="box-tools">
            <button type="button" class="btn btn-box-tool" data-widget="collapse">
                <i class="fa fa-minus"></i>
            </button>
        </div>
    </div>

    <div class="box-body no-padding">
        <ul class="nav nav-pills nav-stacked">
            <li
                class="{{ request()->is('themes/analisa/usaha/perdagangan', 'themes/analisa/usaha/pertanian', 'themes/analisa/usaha/jasa', 'themes/analisa/usaha/lainnya', 'themes/analisa/identitas/usaha/perdagangan', 'themes/analisa/barang/usaha/perdagangan', 'themes/analisa/keuangan/usaha/perdagangan', 'themes/analisa/informasi/usaha/pertanian', 'themes/analisa/biaya/usaha/pertanian', 'themes/analisa/keuangan/usaha/pertanian', 'themes/analisa/keuangan/usaha/jasa', 'themes/analisa/identitas/usaha/lainnya', 'themes/analisa/identitas/usaha/lainnya', 'themes/analisa/keuangan/usaha/lainnya') ? 'active' : '' }}">
                <a href="{{ route('perdagangan.in', ['pengajuan' => $pengajuan]) }}">
                    <i class="fa fa-folder-o"></i> Usaha
                </a>
            </li>

            <li class="{{ request()->is('themes/analisa/keuangan') ? 'active' : '' }}">
                <a href="{{ route('keuangan.index', ['pengajuan' => $pengajuan]) }}">
                    <i class="fa fa-folder-o"></i> Keuangan
                </a>
            </li>

            <li class="{{ request()->is('themes/analisa/kepemilikan') ? 'active' : '' }}">
                <a href="{{ route('kepemilikan.index', ['pengajuan' => $pengajuan]) }}">
                    <i class="fa fa-folder-o"></i> Kepemilikan
                </a>
            </li>

            <li
                class="{{ request()->is('themes/analisa/jaminan/kendaraan', 'themes/analisa/jaminan/tanah', 'themes/analisa/jaminan/lainnya') ? 'active' : '' }}">
                <a href="{{ route('taksasi.kendaraan', ['pengajuan' => $pengajuan]) }}">
                    <i class="fa fa-folder-o"></i> Jaminan
                </a>
            </li>

            <li
                class="{{ request()->is('themes/analisa/5c/character*', 'themes/analisa/5c/capacity*', 'themes/analisa/5c/capital*', 'themes/analisa/5c/collateral*', 'themes/analisa/5c/condition*') ? 'active' : '' }}">
                <a href="{{ route('analisa5c.character', ['pengajuan' => $pengajuan]) }}">
                    <i class="fa fa-folder-o"></i> Analisa 5C
                </a>
            </li>

            <li
                class="{{ request()->is('themes/analisa/kualitatif/karakter*', 'themes/analisa/kualitatif/usaha*') ? 'active' : '' }}">
                <a href="{{ route('kualitatif.karakter', ['pengajuan' => $pengajuan]) }}">
                    <i class="fa fa-folder-o"></i> Kualitatif
                </a>
            </li>

            <li class="{{ request()->is('themes/analisa/momorandum') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-folder-o"></i> Memorandum
                </a>
            </li>

            <li class="{{ request()->is('themes/analisa/administrasi') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-folder-o"></i> Administrasi
                </a>
            </li>

            <li class="{{ request()->is('themes/analisa/tambahan') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-folder-o"></i> Tambahan
                </a>
            </li>
        </ul>
    </div>
</div>
