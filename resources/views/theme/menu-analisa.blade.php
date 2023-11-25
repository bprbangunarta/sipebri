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
                    <i class="fa fa-gear"></i>
                    {{ $data->produk_kode . ' ' . '-' . ' ' . $data->metode_rps }}
                </a>
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
            {{-- @if ($data->produk_kode == 'KTA')
            @else
            @endif --}}
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
                class="{{ request()->is('themes/analisa/kualitatif/karakter*', 'themes/analisa/kualitatif/usaha*', 'themes/analisa/kualitatif/swot*') ? 'active' : '' }}">
                <a href="{{ route('kualitatif.karakter', ['pengajuan' => $pengajuan]) }}">
                    <i class="fa fa-folder-o"></i> Kualitatif
                </a>
            </li>

            <li
                class="{{ request()->is('themes/analisa/memorandum/kebutuhan', 'themes/analisa/memorandum/sandi', 'themes/analisa/memorandum/usulan') ? 'active' : '' }}">
                <a href="{{ route('memorandum.kebutuhan', ['pengajuan' => $pengajuan]) }}">
                    <i class="fa fa-folder-o"></i> Memorandum
                </a>
            </li>

            <li class="{{ request()->is('themes/analisa/administrasi') ? 'active' : '' }}">
                <a href="{{ route('administrasi.index', ['pengajuan' => $pengajuan]) }}">
                    <i class="fa fa-folder-o"></i> Administrasi
                </a>
            </li>

            <li class="{{ request()->is('themes/analisa/konfirmasi/analisa') ? 'active' : '' }}">
                <a href="{{ route('konfirmasi.analisa', ['pengajuan' => $pengajuan]) }}">
                    <i class="fa fa-folder-o"></i> Konfirmasi
                </a>
            </li>
        </ul>
    </div>
</div>
