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
                <a href="#"><i class="fa fa-user"></i> ZULFADLI RIZAL</a>
            </li>
            <li>
                <a href="#">
                    <i class="fa fa-bitcoin"></i> RP. 2.500.000.000 (24 BULAN)
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
            <li class="{{ request()->is('theme/analisa/usaha/perdagangan', 'theme/analisa/usaha/pertanian', 'theme/analisa/usaha/jasa', 'theme/analisa/usaha/lainnya', 'theme/analisa/identitas/usaha/perdagangan', 'theme/analisa/barang/usaha/perdagangan', 'theme/analisa/keuangan/usaha/perdagangan', 'theme/analisa/informasi/usaha/pertanian', 'theme/analisa/biaya/usaha/pertanian', 'theme/analisa/keuangan/usaha/pertanian', 'theme/analisa/keuangan/usaha/jasa', 'theme/analisa/identitas/usaha/lainnya', 'theme/analisa/identitas/usaha/lainnya', 'theme/analisa/keuangan/usaha/lainnya') ? 'active' : '' }}">
                <a href="/theme/analisa/usaha/perdagangan">
                    <i class="fa fa-folder-o"></i> Usaha
                </a>
            </li>

            <li class="{{ request()->is('theme/analisa/keuangan') ? 'active' : '' }}">
                <a href="/theme/analisa/keuangan">
                    <i class="fa fa-folder-o"></i> Keuangan
                </a>
            </li>
            
            <li {{ request()->is('theme/analisa/kepemilikan') ? 'active' : '' }}>
                <a href="/theme/analisa/kepemilikan">
                    <i class="fa fa-folder-o"></i> Kepemilikan
                </a>
            </li>

            <li {{ request()->is('theme/analisa/keuangan') ? 'jaminan' : '' }}>
                <a href="#">
                    <i class="fa fa-folder-o"></i> Jaminan
                </a>
            </li>

            <li {{ request()->is('theme/analisa/5c') ? 'active' : '' }}>
                <a href="#">
                    <i class="fa fa-folder-o"></i> Analisa 5C
                </a>
            </li>

            <li {{ request()->is('theme/analisa/kualitatif') ? 'active' : '' }}>
                <a href="#">
                    <i class="fa fa-folder-o"></i> Kualitatif
                </a>
            </li>
            
            <li {{ request()->is('theme/analisa/momorandum') ? 'active' : '' }}>
                <a href="#">
                    <i class="fa fa-folder-o"></i> Memorandum
                </a>
            </li>

            <li {{ request()->is('theme/analisa/administrasi') ? 'active' : '' }}>
            <a href="#">
                <i class="fa fa-folder-o"></i> Administrasi
                </a>
            </li>

            <li {{ request()->is('theme/analisa/tambahan') ? 'active' : '' }}>
                <a href="#">
                    <i class="fa fa-folder-o"></i> Tambahan
                </a>
            </li>
        </ul>
    </div>
</div>