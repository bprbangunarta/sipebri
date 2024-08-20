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
        <h3 class="box-title">Menu Reschedulling</h3>

        <div class="box-tools">
            <button type="button" class="btn btn-box-tool" data-widget="collapse">
                <i class="fa fa-minus"></i>
            </button>
        </div>
    </div>

    <div class="box-body no-padding">
        <ul class="nav nav-pills nav-stacked">
            <li
                class="{{ request()->is('themes/rsc/data/kredit', 'themes/analisa/usaha/pertanian', 'themes/analisa/usaha/jasa', 'themes/analisa/usaha/lainnya', 'themes/analisa/identitas/usaha/perdagangan', 'themes/analisa/barang/usaha/perdagangan', 'themes/analisa/keuangan/usaha/perdagangan', 'themes/analisa/informasi/usaha/pertanian', 'themes/analisa/biaya/usaha/pertanian', 'themes/analisa/keuangan/usaha/pertanian', 'themes/analisa/keuangan/usaha/jasa', 'themes/analisa/identitas/usaha/lainnya', 'themes/analisa/identitas/usaha/lainnya', 'themes/analisa/keuangan/usaha/lainnya') ? 'active' : '' }}">
                <a
                    href="{{ route('rsc.data.kredit', ['kode' => $data->kode, 'rsc' => $data->rsc, 'status_rsc' => $data->status_rsc]) }}">
                    <i class="fa fa-folder-o"></i> Data Kredit
                </a>
            </li>

            <li
                class="{{ request()->is('themes/rsc/analisa/usaha/perdagangan*', 'themes/rsc/analisa/usaha/pertanian*', 'themes/rsc/analisa/usaha/jasa*', 'themes/rsc/analisa/usaha/lain*') ? 'active' : '' }}">
                <a
                    href="{{ route('rsc.usaha.perdagangan', ['kode' => $data->kode, 'rsc' => $data->rsc, 'status_rsc' => $data->status_rsc]) }}">
                    <i class="fa fa-folder-o"></i> Usaha
                </a>
            </li>

            <li class="{{ request()->is('themes/rsc/keuangan') ? 'active' : '' }}">
                <a
                    href="{{ route('rsc.keuangan', ['kode' => $data->kode, 'rsc' => $data->rsc, 'status_rsc' => $data->status_rsc]) }}">
                    <i class="fa fa-folder-o"></i> Keuangan
                </a>
            </li>

            {{-- <li class="{{ request()->is('themes/rsc/jaminan/kendaraan') ? 'active' : '' }}">
                <a
                    href="{{ route('rsc.jaminan.kendaraan', ['kode' => $data->kode, 'rsc' => $data->rsc, 'status_rsc' => $data->status_rsc]) }}">
                    <i class="fa fa-folder-o"></i> Jaminan
                </a>
            </li> --}}

            <li class="{{ request()->is('themes/rsc/data/pengusulan') ? 'active' : '' }}">
                <a
                    href="{{ route('rsc.data.pengusulan', ['kode' => $data->kode, 'rsc' => $data->rsc, 'status_rsc' => $data->status_rsc]) }}">
                    <i class="fa fa-folder-o"></i> Usulan Plafon
                </a>
            </li>

            <li class="{{ request()->is('themes/rsc/penilaian/debitur') ? 'active' : '' }}">
                <a
                    href="{{ route('rsc.penilaian.debitur', ['kode' => $data->kode, 'rsc' => $data->rsc, 'status_rsc' => $data->status_rsc]) }}">
                    <i class="fa fa-folder-o"></i> Penilaian Debitur
                </a>
            </li>

            <li class="{{ request()->is('themes/rsc/konfirmasi') ? 'active' : '' }}">
                <a
                    href="{{ route('rsc.konfirmasi', ['kode' => $data->kode, 'rsc' => $data->rsc, 'status_rsc' => $data->status_rsc]) }}">
                    <i class="fa fa-folder-o"></i> Konfirmasi
                </a>
            </li>

        </ul>
    </div>
</div>
