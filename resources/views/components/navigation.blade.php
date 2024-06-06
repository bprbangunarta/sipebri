<aside class="main-sidebar">
    <section class="sidebar">
        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{ asset('theme/dist/img/user2-160x160.jpg') }}" class="img-square" style="border-radius: 3px;"
                    alt="User Image">
            </div>
            <div class="pull-left info">
                <p style="text-transform: uppercase;">{{ Auth::user()->username }}</p>
                <span class="label label-success">Verified</span>
            </div>
        </div>

        <form action="{{ route('laporan.tracking.pengajuan') }}" method="GET" class="sidebar-form">
            <div class="input-group">
                <input type="text" class="form-control text-uppercase pull-right"
                    style="width: 180px;font-size:11.4px;" name="keyword" id="keyword"
                    value="{{ request('keyword') }}" placeholder="Nama/ Kode/ Wil/ Produk">

                <span class="input-group-btn">
                    <button type="submit" class="btn btn-flat">
                        <i class="fa fa-search"></i>
                    </button>
                </span>
            </div>
        </form>

        <ul class="sidebar-menu" data-widget="tree">

            @can('master data')
                <li class="header">ADMINISTRATOR</li>
                <li
                    class="treeview {{ request()->is('admin/user', 'admin/role', 'admin/permission', 'perubahan/data', 'admin/data/nasabah', 'admin/data/nasabah/*/edit', 'admin/data/pendamping', 'admin/data/pendamping/*', 'admin/data/pendamping/*/edit', 'admin/data/pengajuan', 'admin/data/pengajuan/*', 'admin/data/jaminan', 'admin/data/jaminan/*', 'admin/data/jaminan/*/edit', 'admin/data/survei', 'admin/data/survei/*') ? 'active' : '' }}">
                    <a href="#">
                        <i class="fa fa-cube"></i>
                        <span>Data Master</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <li class="{{ request()->is('admin/user') ? 'active' : '' }}">
                            <a href="{{ route('user.index') }}" title=" Data User">
                                <i class="fa fa-circle-o"></i>
                                Data User
                            </a>
                        </li>

                        <li class="{{ request()->is('perubahan/data') ? 'active' : '' }}">
                            <a href="{{ route('ubah.data') }}" title="Menu Sakti">
                                <i class="fa fa-circle-o"></i>
                                Menu Sakti
                            </a>
                        </li>

                        <li
                            class="{{ request()->is('admin/data/jaminan', 'admin/data/jaminan/*', 'admin/data/jaminan/*/edit') ? 'active' : '' }}">
                            <a href="{{ route('admin.jaminan.index') }}" title="Data Jaminan">
                                <i class="fa fa-circle-o"></i>
                                Data Jaminan
                            </a>
                        </li>

                        <li class="{{ request()->is('admin/data/nasabah', 'admin/data/nasabah/*/edit') ? 'active' : '' }}">
                            <a href="{{ route('admin.nasabah.index') }}" title="Data Nasabah">
                                <i class="fa fa-circle-o"></i>
                                Data Nasabah
                            </a>
                        </li>

                        <li
                            class="{{ request()->is('admin/data/pendamping', 'admin/data/pendamping/*/edit', 'admin/data/pendamping/*') ? 'active' : '' }}">
                            <a href="{{ route('admin.pendamping.index') }}" title="Data Pendamping">
                                <i class="fa fa-circle-o"></i>
                                Data Pendamping
                            </a>
                        </li>

                        <li class="{{ request()->is('admin/data/pengajuan', 'admin/data/pengajuan/*') ? 'active' : '' }}">
                            <a href="{{ route('admin.pengajuan.index') }}" title="Data Pengajuan">
                                <i class="fa fa-circle-o"></i>
                                Data Pengajuan
                            </a>
                        </li>

                        <li class="{{ request()->is('admin/data/survei', 'admin/data/survei/*') ? 'active' : '' }}">
                            <a href="{{ route('admin.survei.index') }}" title="Data Survei">
                                <i class="fa fa-circle-o"></i>
                                Data Survei
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="treeview {{ request()->is('') ? 'active' : '' }}">
                    <a href="#">
                        <i class="fa fa-lock"></i>
                        <span>Role & Permission</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">

                        <li class="{{ request()->is('') ? 'active' : '' }}">
                            <a href="#" title="Data Role">
                                <i class="fa fa-circle-o"></i>
                                Data Role
                            </a>
                        </li>
                        <li class="{{ request()->is('') ? 'active' : '' }}">
                            <a href="#" title="Permission">
                                <i class="fa fa-circle-o"></i>
                                Permission
                            </a>
                        </li>
                        <li class="{{ request()->is('') ? 'active' : '' }}">
                            <a href="#" title="Permission">
                                <i class="fa fa-circle-o"></i>
                                Give Permission
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan

            <li class="header">MAIN MENU</li>

            @hasanyrole($roles)
                <li class="{{ request()->is('dashboard', 'themes/dashboard') ? 'active' : '' }}">
                    <a href="/dashboard" title="Dashboard">
                        <i class="fa fa-laptop"></i> <span>Dashboard</span>
                    </a>
                </li>
            @endhasanyrole

            <li
                class="treeview {{ request()->is('pengajuan', 'nasabah/edit', 'pendamping/edit', 'pengajuan/edit', 'pengajuan/agunan', 'survei/edit', 'konfirmasi/pengajuan', 'data/pengajuan', 'otor/pengajuan', 'tracking/pengajuan', 'otorisasi/pengajuan') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-users"></i>
                    <span>Pengajuan</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li class="{{ request()->is('data/pengajuan', 'tracking/pengajuan') ? 'active' : '' }}">
                        <a href="{{ route('pengajuan.data') }}" title="List Pengajuan">
                            <i class="fa fa-bars"></i>
                            List Pengajuan
                        </a>
                    </li>

                    <li
                        class="{{ request()->is('pengajuan', 'nasabah/edit', 'pendamping/edit', 'pengajuan/edit', 'pengajuan/agunan', 'survei/edit', 'konfirmasi/pengajuan') ? 'active' : '' }}">
                        <a @can('tambah pengajuan kredit') href="{{ route('pengajuan.index') }}" @endcan
                            title="Add Pengajuan" title="Input Pengajuan">
                            <i class="fa fa-plus"></i>
                            Add Pengajuan
                        </a>
                    </li>

                    <li
                        class="{{ request()->is('otor/pengajuan', 'nasabah/edit', 'pendamping/edit', 'pengajuan/edit', 'pengajuan/agunan', 'survei/edit', 'otorisasi/pengajuan') ? 'active' : '' }}">
                        <a @can('otorisasi pengajuan kredit') href="{{ route('otor.pengajuan') }}" @endcan
                            title="Otorisasi Pengajuan">
                            <i class="fa fa-check"></i>
                            Otor Pengajuan
                        </a>
                    </li>
                </ul>
            </li>

            <li
                class="treeview {{ request()->is('analisa/penjadwalan','themes/permohonan/analisa','themes/analisa/usaha/perdagangan','themes/analisa/usaha/pertanian','themes/analisa/usaha/jasa','themes/analisa/usaha/lainnya','themes/analisa/identitas/usaha/perdagangan','themes/analisa/barang/usaha/perdagangan','themes/analisa/keuangan/usaha/perdagangan','themes/analisa/informasi/usaha/pertanian','themes/analisa/biaya/usaha/pertanian','themes/analisa/keuangan/usaha/pertanian','themes/analisa/keuangan/usaha/jasa','themes/analisa/identitas/usaha/lainnya','themes/analisa/identitas/usaha/lainnya','themes/analisa/keuangan/usaha/lainnya','themes/analisa/keuangan','themes/analisa/kepemilikan','themes/analisa/jaminan/kendaraan','themes/analisa/jaminan/tanah','themes/analisa/jaminan/lainnya','themes/analisa/5c/character*','themes/analisa/5c/capacity*','themes/analisa/5c/capital*','themes/analisa/5c/collateral*','themes/analisa/5c/condition*','themes/analisa/kualitatif/karakter*','themes/analisa/kualitatif/usaha*','themes/analisa/memorandum/kebutuhan','themes/analisa/memorandum/sandi','themes/analisa/memorandum/usulan','themes/analisa/administrasi','themes/analisa/konfirmasi/analisa','themes/komite/kredit','themes/komite/kredit/survei/analisa')? 'active': '' }}">
                <a href="#">
                    <i class="fa fa-suitcase"></i>
                    <span>Analisa Kredit</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">

                    <li class="{{ request()->is('analisa/penjadwalan') ? 'active' : '' }}">
                        <a @can('penjadwalan survey') href="{{ route('analisa.penjadwalan') }}" @endcan
                            title="Penjadwalan Survey">
                            <i class="fa fa-calendar"></i>
                            Penjadwalan
                        </a>
                    </li>

                    <li
                        class="{{ request()->is('themes/permohonan/analisa','themes/analisa/usaha/perdagangan','themes/analisa/usaha/pertanian','themes/analisa/usaha/jasa','themes/analisa/usaha/lainnya','themes/analisa/identitas/usaha/perdagangan','themes/analisa/barang/usaha/perdagangan','themes/analisa/keuangan/usaha/perdagangan','themes/analisa/informasi/usaha/pertanian','themes/analisa/biaya/usaha/pertanian','themes/analisa/keuangan/usaha/pertanian','themes/analisa/keuangan/usaha/jasa','themes/analisa/identitas/usaha/lainnya','themes/analisa/identitas/usaha/lainnya','themes/analisa/keuangan/usaha/lainnya','themes/analisa/keuangan','themes/analisa/kepemilikan','themes/analisa/jaminan/kendaraan','themes/analisa/jaminan/tanah','themes/analisa/jaminan/lainnya','themes/analisa/5c/character*','themes/analisa/5c/capacity*','themes/analisa/5c/capital*','themes/analisa/5c/collateral*','themes/analisa/5c/condition*','themes/analisa/kualitatif/karakter*','themes/analisa/kualitatif/usaha*','themes/analisa/memorandum/kebutuhan','themes/analisa/memorandum/sandi','themes/analisa/memorandum/usulan','themes/analisa/administrasi','themes/analisa/konfirmasi/analisa')? 'active': '' }}">
                        <a @can('input analisa') href="{{ route('permohonan.analisa') }}" @endcan
                            title="Input Analisa">
                            <i class="fa fa-edit"></i>
                            Input Analisa
                        </a>
                    </li>

                    <li class="{{ request()->is('themes/komite/kredit/survei/analisa') ? 'active' : '' }}">
                        <a @can('survey dan analisa') href="{{ route('survei.analisa') }}" @endcan
                            title="Survey dan Analisa">
                            <i class="fa fa-hourglass-start"></i>
                            Kontrol Survey
                        </a>
                    </li>

                    <li class="{{ request()->is('themes/komite/kredit') ? 'active' : '' }}">
                        <a @can('komite kredit') href="{{ route('komite.kredit') }}" @endcan title="Komite Kredit">
                            <i class="fa fa-file-text-o"></i>
                            Input Persetujuan
                        </a>
                    </li>
                </ul>
            </li>

            <li
                class="treeview {{ request()->is('themes/notifikasi/kredit', 'themes/notifikasi/kredit/update', 'themes/notifikasi/perjanjian/kredit', 'otor/perjanjian/kredit', 'themes/notifikasi/realisasi/kredit', 'themes/penolakan/pengajuan', 'themes/data/perjanjian/kredit', 'themes/data/batal/perjanjian/kredit', 'themes/denah/lokasi') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-file-text"></i>
                    <span>Administratif</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li class="{{ request()->is('themes/notifikasi/kredit') ? 'active' : '' }}">
                        <a @can('generate notifikasi') href="{{ route('notifikasi_kredit') }}" @endcan
                            title="Notifikasi Kredit">
                            <i class="fa fa-bell"></i>
                            Notifikasi Kredit
                        </a>
                    </li>

                    <li class="{{ request()->is('themes/notifikasi/kredit/update') ? 'active' : '' }}">
                        <a @can('generate notifikasi') href="{{ route('index.update_notifikasi') }}" @endcan
                            title="Notifikasi Kredit">
                            <i class="fa fa-bell"></i>
                            Update Notifikasi Kredit
                        </a>
                    </li>

                    <li class="{{ request()->is('themes/penolakan/pengajuan') ? 'active' : '' }}">
                        <a @can('input penolakan') href="{{ route('penolakan.pengajuan') }}" @endcan
                            title="Input Penolakan">
                            <i class="fa fa-ban"></i>
                            Surat Penolakan
                        </a>
                    </li>

                    <li class="{{ request()->is('themes/notifikasi/perjanjian/kredit') ? 'active' : '' }}">
                        <a @can('generate pk') href="{{ route('perjanjian.kredit') }}" @endcan
                            title="Add Perjanjian Kredit">
                            <i class="fa fa-exclamation-circle"></i>
                            Add Perjanjian Kredit
                        </a>
                    </li>

                    <li class="{{ request()->is('otor/perjanjian/kredit') ? 'active' : '' }}">
                        <a @can('otor pk') href="{{ route('otor.perjanjian_kredit') }}" @endcan
                            title="Otor Perjanjian Kredit">
                            <i class="fa fa-check"></i>
                            Otor Perjanjian Kredit
                        </a>
                    </li>

                    <li class="{{ request()->is('themes/data/perjanjian/kredit') ? 'active' : '' }}">
                        <a @can('cetak perjanjian kredit') href="{{ route('data.perjanjian_kredit') }}" @endcan
                            title="Data Perjanjian Kredit">
                            <i class="fa fa-file" aria-hidden="true"></i>
                            Data Perjanjian Kredit
                        </a>
                    </li>

                    <li class="{{ request()->is('themes/data/batal/perjanjian/kredit') ? 'active' : '' }}">
                        <a @can('input penolakan') href="{{ route('data.batal_perjanjian_kredit') }}" @endcan
                            title="Data Perjanjian Kredit">
                            <i class="fa fa-ban" aria-hidden="true"></i>
                            Data Pembatalan Kredit
                        </a>
                    </li>

                    <li class="{{ request()->is('themes/notifikasi/realisasi/kredit') ? 'active' : '' }}">
                        <a @can('realisasi kredit') href="{{ route('realisasi.kredit') }}" @endcan
                            title="Realisasi Kredit">
                            <i class="fa fa-flag"></i>
                            Realisasi Kredit
                        </a>
                    </li>

                    {{-- <li class="{{ request()->is('themes/denah/lokasi') ? 'active' : '' }}">
                        <a @can('survey dan analisa') href="{{ route('denah.lokasi') }}" @endcan
                            title="Check Kendaraan & Lokasi">
                            <i class="fa fa-map" aria-hidden="true"></i>
                            Denah Lokasi
                        </a>
                    </li> --}}
                </ul>
            </li>

            <li
                class="treeview {{ request()->is('cetak/pengajuan', 'cetak/pengajuan/detail', 'themes/cetak/analisa/kredit', 'themes/fiducia', 'themes/cetak/penolakan/kredit', 'themes/persetujuan/kredit', 'cetak/notifikasi-kredit', 'cetak/perjanjian-kredit', 'themes/analisa/check/kelengkapan') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-print"></i>
                    <span>Cetak Berkas</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">

                    <li class="{{ request()->is('cetak/pengajuan', 'cetak/pengajuan/detail') ? 'active' : '' }}">
                        <a @can('cetak pengajuan kredit') href="{{ route('cetak.pengajuan.index') }}" @endcan
                            title="Cetak Pengajuan">
                            <i class="fa fa-users"></i>
                            Pengajuan
                        </a>
                    </li>

                    <li class="{{ request()->is('themes/cetak/analisa/kredit') ? 'active' : '' }}">
                        <a @can('cetak analisa kredit') href="{{ route('analisa.kredit') }}" @endcan
                            title="Cetak Analisa Kredit">
                            <i class="fa fa-suitcase"></i>
                            Analisa Kredit
                        </a>
                    </li>

                    <li class="{{ request()->is('themes/analisa/check/kelengkapan') ? 'active' : '' }}">
                        <a @can('survey dan analisa') href="{{ route('index.check_kelengkapan') }}" @endcan
                            title="Check Kelengkapan">
                            <i class="fa fa-check-circle-o" aria-hidden="true"></i>
                            Check List Kelengkapan
                        </a>
                    </li>

                    <li class="{{ request()->is('themes/cetak/penolakan/kredit') ? 'active' : '' }}">
                        <a @can('cetak penolakan kredit') href="{{ route('data_penolakan.kredit') }}" @endcan
                            title="Cetak Penolakan Kredit">
                            <i class="fa fa-ban"></i>
                            Penolakan Kredit
                        </a>
                    </li>

                    <li class="{{ request()->is('themes/persetujuan/kredit') ? 'active' : '' }}">
                        <a @can('cetak persetujuan kredit') href="{{ route('persetujuan.kredit') }}" @endcan
                            title="Cetak Persetujuan Kredit">
                            <i class="fa fa-file-text-o"></i>
                            Persetujuan Kredit
                        </a>
                    </li>

                    <li class="{{ request()->is('cetak/notifikasi-kredit') ? 'active' : '' }}">
                        <a @can('cetak notifikasi kredit') href="{{ route('cetak.notifikasi.index') }}" @endcan
                            title="Cetak Notifikasi Kredit">
                            <i class="fa fa-bell-o"></i>
                            Notifikasi Kredit
                        </a>
                    </li>

                    <li class="{{ request()->is('themes/fiducia') ? 'active' : '' }}">
                        <a @can('cetak penjaminan fiducia') href="{{ route('fiducia') }}" @endcan
                            title="Cetak Penjaminan Fiducia">
                            <i class="fa fa-truck"></i>
                            Penjaminan Fiducia
                        </a>
                    </li>

                    <li class="{{ request()->is('cetak/perjanjian-kredit') ? 'active' : '' }}">
                        <a @can('cetak perjanjian kredit') href="{{ route('cetak.perjanjian.index') }}" @endcan
                            title="Cetak Perjanjian Kredit">
                            <i class="fa fa-exclamation-circle"></i>
                            Perjanjian Kredit
                        </a>
                    </li>
                </ul>
            </li>

            <li
                class="treeview {{ request()->is('laporan/fasilitas', 'laporan/realisasi', 'laporan/realisasi/kredit', 'laporan/penolakan', 'laporan/pendaftaran', 'laporan/pendaftaran/kredit', 'laporan/survei', 'laporan/siap-realisasi', 'laporan/siap-realisasi/kredit', 'filter/laporan/fasilitas', 'laporan/pengajuan/disetujui', 'laporan/pencairan', 'laporan/sebelum/survey', 'laporan/sesudah/survey', 'laporan/tracking/pengajuan') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-folder-open"></i>
                    <span>Laporan</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li
                        class="{{ request()->is('laporan/pendaftaran', 'laporan/pendaftaran/kredit') ? 'active' : '' }}">
                        <a href="/laporan/pendaftaran">
                            <i class="fa fa-users"></i>
                            Pendaftaran Kredit
                        </a>
                    </li>

                    <li class="{{ request()->is('laporan/sebelum/survey') ? 'active' : '' }}">
                        <a href="{{ route('laporan.sebelum.survey') }}">
                            <i class="fa fa-hourglass-start"></i>
                            Sebelum Survey
                        </a>
                    </li>

                    <li class="{{ request()->is('laporan/sesudah/survey') ? 'active' : '' }}">
                        <a href="{{ route('laporan.sesudah.survey') }}">
                            <i class="fa fa-hourglass-end"></i>
                            Sesudah Survey
                        </a>
                    </li>

                    <li class="{{ request()->is('laporan/penolakan') ? 'active' : '' }}">
                        <a href="{{ route('laporan.penolakan') }}">
                            <i class="fa fa-ban"></i>
                            Pengajuan Ditolak
                        </a>
                    </li>

                    <li class="{{ request()->is('laporan/pengajuan/disetujui') ? 'active' : '' }}">
                        <a href="{{ route('pengajuan.disetujui') }}">
                            <i class="fa fa-check"></i>
                            Pengajuan Disetujui
                        </a>
                    </li>

                    <li
                        class="{{ request()->is('laporan/siap-realisasi', 'laporan/siap-realisasi/kredit') ? 'active' : '' }}">
                        <a href="{{ route('laporan.siap-realisasi') }}">
                            <i class="fa fa-bullhorn"></i>
                            Data Siap Realisasi
                        </a>
                    </li>

                    <li class="{{ request()->is('laporan/pencairan', 'filter/laporan/pencairan') ? 'active' : '' }}">
                        <a href="{{ route('laporan.pencairan') }}">
                            <i class="fa fa-flag"></i>
                            Pencairan Kredit
                        </a>
                    </li>

                    <li class="{{ request()->is('laporan/tracking/pengajuan') ? 'active' : '' }}">
                        <a href="{{ route('laporan.tracking.pengajuan') }}">
                            <i class="fa fa-truck"></i>
                            Tracking Pengajuan
                        </a>
                    </li>

                    {{-- <li class="{{ request()->is('laporan/tracking/pengajuan') ? 'active' : '' }}">
                        <a href="/laporan/survei">
                            <i class="fa fa-hourglass-start"></i>
                            Survey dan Analisa
                        </a>
                    </li> --}}
                </ul>
            </li>

            <li
                class="treeview {{ request()->is('themes/rsc/index', 'themes/rsc/analisa', 'themes/rsc/data/kredit', 'themes/rsc/penilaian/debitur', 'themes/rsc/analisa/usaha/perdagangan', 'themes/rsc/analisa/usaha/perdagangan/identitas', 'themes/rsc/analisa/usaha/perdagangan/barang', 'themes/rsc/analisa/usaha/perdagangan/keuangan', 'themes/rsc/analisa/usaha/pertanian', 'themes/rsc/analisa/usaha/pertanian/informasi', 'themes/rsc/analisa/usaha/pertanian/biaya', 'themes/rsc/analisa/usaha/pertanian/keuangan', 'themes/rsc/analisa/usaha/jasa', 'themes/rsc/analisa/usaha/jasa/keuangan', 'themes/rsc/analisa/usaha/lain', 'themes/rsc/analisa/usaha/lain/identitas', 'themes/rsc/analisa/usaha/lain/bahan', 'themes/rsc/analisa/usaha/lain/keuangan', 'themes/rsc/keuangan', 'themes/rsc/data/pengusulan', 'themes/rsc/konfirmasi', 'themes/rsc/persetujuan', 'themes/rsc/persetujuan/informasi', 'themes/rsc/persetujuan/catatan', 'themes/rsc/persetujuan/index', 'themes/rsc/penjadwalan', 'themes/rsc/penjadwalan/tambah')? 'active': '' }}">
                <a href="#">
                    <i class="fa fa-medkit" aria-hidden="true"></i>
                    <span>Reschedulling</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">

                    {{-- <li
                        class="{{ request()->is('themes/rsc/index', 'themes/rsc/data/kredit', 'themes/rsc/penilaian/debitur', 'themes/rsc/analisa/usaha/perdagangan', 'themes/rsc/analisa/usaha/perdagangan/identitas', 'themes/rsc/analisa/usaha/perdagangan/barang', 'themes/rsc/analisa/usaha/perdagangan/keuangan', 'themes/rsc/analisa/usaha/pertanian', 'themes/rsc/analisa/usaha/pertanian/informasi', 'themes/rsc/analisa/usaha/pertanian/biaya', 'themes/rsc/analisa/usaha/pertanian/keuangan', 'themes/rsc/analisa/usaha/jasa', 'themes/rsc/analisa/usaha/jasa/keuangan', 'themes/rsc/analisa/usaha/lain', 'themes/rsc/analisa/usaha/lain/identitas', 'themes/rsc/analisa/usaha/lain/bahan', 'themes/rsc/analisa/usaha/lain/keuangan', 'themes/rsc/keuangan', 'themes/rsc/data/pengusulan', 'themes/rsc/konfirmasi') ? 'active' : '' }}">
                        <a href="{{ route('rsc.index') }}" title="Add RSC">
                            <i class="fa fa-plus-circle" aria-hidden="true"></i>
                            Add RSC
                        </a>
                    </li> --}}
                    <li class="{{ request()->is('themes/rsc/index') ? 'active' : '' }}">
                        <a @can('tambah pengajuan kredit') href="{{ route('rsc.index') }}" @endcan title="Add RSC">
                            <i class="fa fa-plus-circle" aria-hidden="true"></i>
                            Add RSC
                        </a>
                    </li>
                    <li
                        class="{{ request()->is('themes/rsc/penjadwalan', 'themes/rsc/penjadwalan/tambah') ? 'active' : '' }}">
                        <a @can('penjadwalan survey') href="{{ route('rsc.penjadwalan') }}" @endcan title="Add RSC">
                            <i class="fa fa-calendar" aria-hidden="true"></i>
                            Penjadwalan RSC
                        </a>
                    </li>
                    <li
                        class="{{ request()->is('themes/rsc/analisa', 'themes/rsc/data/kredit', 'themes/rsc/penilaian/debitur', 'themes/rsc/analisa/usaha/perdagangan', 'themes/rsc/analisa/usaha/perdagangan/identitas', 'themes/rsc/analisa/usaha/perdagangan/barang', 'themes/rsc/analisa/usaha/perdagangan/keuangan', 'themes/rsc/analisa/usaha/pertanian', 'themes/rsc/analisa/usaha/pertanian/informasi', 'themes/rsc/analisa/usaha/pertanian/biaya', 'themes/rsc/analisa/usaha/pertanian/keuangan', 'themes/rsc/analisa/usaha/jasa', 'themes/rsc/analisa/usaha/jasa/keuangan', 'themes/rsc/analisa/usaha/lain', 'themes/rsc/analisa/usaha/lain/identitas', 'themes/rsc/analisa/usaha/lain/bahan', 'themes/rsc/analisa/usaha/lain/keuangan', 'themes/rsc/keuangan', 'themes/rsc/data/pengusulan', 'themes/rsc/konfirmasi') ? 'active' : '' }}">
                        <a href="{{ route('rsc.index.analisa') }}" title="Add RSC">
                            <i class="fa fa-edit"></i>
                            Analisa RSC
                        </a>
                    </li>
                    <li
                        class="{{ request()->is('themes/rsc/persetujuan', 'themes/rsc/persetujuan/informasi', 'themes/rsc/persetujuan/catatan', 'themes/rsc/persetujuan/index') ? 'active' : '' }}">
                        <a @can('komite kredit') href="{{ route('rsc.persetujuan.index') }}" @endcan title="Add RSC">
                            <i class="fa fa-file-text-o"></i>
                            Input Persetujuan RSC
                        </a>
                    </li>
                    <li class="#">
                        <a href="" title="Add RSC">
                            <i class="fa fa-suitcase"></i>
                            Add Notifikasi
                        </a>
                    </li>
                    <li class="#">
                        <a href="" title="Add RSC">
                            <i class="fa fa-suitcase"></i>
                            Add Perjanjian Kredit
                        </a>
                    </li>
                    <li class="#">
                        <a href="" title="Add RSC">
                            <i class="fa fa-suitcase"></i>
                            Cetak Analisa RSC
                        </a>
                    </li>
                    <li class="#">
                        <a href="" title="Add RSC">
                            <i class="fa fa-suitcase"></i>
                            Cetak Notifikasi RSC
                        </a>
                    </li>
                    <li class="#">
                        <a href="" title="Add RSC">
                            <i class="fa fa-exclamation-circle"></i>
                            Cetak Perjanjian Kredit RSC
                        </a>
                    </li>
                </ul>
            </li>

            <li
                class="treeview {{ request()->is('droping/cif', 'droping/agunan', 'droping/kredit') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-download"></i>
                    <span>Dropping</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li class="{{ request()->is('droping/cif') ? 'active' : '' }}">
                        <a href="{{ route('dropping.cif') }}" title="Pembukaan CIF">
                            <i class="fa fa-user"></i>
                            Pembukaan CIF
                        </a>
                    </li>

                    <li class="{{ request()->is('droping/agunan') ? 'active' : '' }}">
                        <a href="{{ route('dropping.agunan') }}" title="Agunan Kredit">
                            <i class="fa fa-bank"></i>
                            Agunan Kredit
                        </a>
                    </li>

                    <li class="{{ request()->is('droping/kredit') ? 'active' : '' }}">
                        <a href="{{ route('dropping.kredit') }}" title="Pengajuan Kredit">
                            <i class="fa fa-money"></i>
                            Pengajuan Kredit
                        </a>
                    </li>
                </ul>
            </li>

            <li class="header">TOOLS</li>
            <li class="{{ request()->is('cif') ? 'active' : '' }}">
                <a href="{{ route('cif.index') }}" title="Pengecekan CIF">
                    <i class="fa fa-user"></i>
                    Pengecekan CIF
                </a>
            </li>
            <li class="{{ request()->is('perhitungan/flat', 'perhitungan/efektif_musiman') ? 'active' : '' }}">
                <a href="{{ route('flat') }}" title="Perhitungan Kredit">
                    <i class="fa fa-calculator"></i>
                    Simulasi Kredit
                </a>
            </li>
            <li class="{{ request()->is('perhitungan/simulasi') ? 'active' : '' }}">
                <a href="{{ route('simulasi_ajk') }}" title="Perhitungan Asuransi">
                    <i class="fa fa-calculator"></i>
                    Simulasi Asuransi
                </a>
            </li>
        </ul>
    </section>
</aside>
