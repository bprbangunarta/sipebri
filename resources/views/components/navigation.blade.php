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

        <form action="{{ route('pengajuan.data') }}" method="GET" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="name" class="form-control" value="{{ Request('name') }}"
                    placeholder="SEARCH">
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-flat">
                        <i class="fa fa-search"></i>
                    </button>
                </span>
            </div>
        </form>

        <ul class="sidebar-menu" data-widget="tree">
            <li class="header">MAIN MENU</li>

            @hasanyrole($roles)
                <li class="{{ request()->is('dashboard', 'themes/dashboard') ? 'active' : '' }}">
                    <a href="/dashboard" title="Dashboard">
                        <i class="fa fa-laptop"></i> <span>Dashboard</span>
                    </a>
                </li>
            @endhasanyrole

            {{-- MENU ADMIN --}}
            @can('master data')
                <li class="treeview {{ request()->is('admin/user', 'admin/role', 'admin/permission') ? 'active' : '' }}">
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
                        <li class="{{ request()->is('admin/role') ? 'active' : '' }}">
                            <a href="{{ route('role.index') }}" title="Data Role">
                                <i class="fa fa-circle-o"></i>
                                Data Role
                            </a>
                        </li>
                        <li class="{{ request()->is('admin/permission') ? 'active' : '' }}">
                            <a href="{{ route('permission.index') }}" title="Permission">
                                <i class="fa fa-circle-o"></i>
                                Permission
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan

            <li
                class="treeview {{ request()->is('pengajuan', 'nasabah/edit', 'pendamping/edit', 'pengajuan/edit', 'pengajuan/agunan', 'survei/edit', 'konfirmasi/pengajuan', 'data/pengajuan', 'otor/pengajuan') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-users"></i>
                    <span>Pengajuan</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">

                    <li
                        class="{{ request()->is('pengajuan', 'nasabah/edit', 'pendamping/edit', 'pengajuan/edit', 'pengajuan/agunan', 'survei/edit', 'konfirmasi/pengajuan') ? 'active' : '' }}">
                        <a href="{{ route('pengajuan.index') }}" title="Permission" title="Input Pengajuan">
                            <i class="fa fa-plus"></i>
                            Add Pengajuan
                        </a>
                    </li>

                    <li class="{{ request()->is('data/pengajuan', 'tracking/pengajuan') ? 'active' : '' }}">
                        <a href="{{ route('pengajuan.data') }}" title="Data Pengajuan">
                            <i class="fa fa-bars"></i>
                            List Pengajuan
                        </a>
                    </li>

                    <li
                        class="{{ request()->is('otor/pengajuan', 'nasabah/edit', 'pendamping/edit', 'pengajuan/edit', 'pengajuan/agunan', 'survei/edit', 'konfirmasi/pengajuan') ? 'active' : '' }}">
                        <a href="{{ route('otor.pengajuan') }}" title="Otorisasi Pengajuan">
                            <i class="fa fa-check"></i>
                            Otor Pengajuan
                        </a>
                    </li>


                </ul>
            </li>

            <li
                class="treeview {{ request()->is('analisa/penjadwalan','themes/permohonan/analisa','themes/analisa/usaha/perdagangan','themes/analisa/usaha/pertanian','themes/analisa/usaha/jasa','themes/analisa/usaha/lainnya','themes/analisa/identitas/usaha/perdagangan','themes/analisa/barang/usaha/perdagangan','themes/analisa/keuangan/usaha/perdagangan','themes/analisa/informasi/usaha/pertanian','themes/analisa/biaya/usaha/pertanian','themes/analisa/keuangan/usaha/pertanian','themes/analisa/keuangan/usaha/jasa','themes/analisa/identitas/usaha/lainnya','themes/analisa/identitas/usaha/lainnya','themes/analisa/keuangan/usaha/lainnya','themes/analisa/keuangan','themes/analisa/kepemilikan','themes/analisa/jaminan/kendaraan','themes/analisa/jaminan/tanah','themes/analisa/jaminan/lainnya','themes/analisa/5c/character*','themes/analisa/5c/capacity*','themes/analisa/5c/capital*','themes/analisa/5c/collateral*','themes/analisa/5c/condition*','themes/analisa/kualitatif/karakter*','themes/analisa/kualitatif/usaha*','themes/analisa/memorandum/kebutuhan','themes/analisa/memorandum/sandi','themes/analisa/memorandum/usulan','themes/analisa/administrasi','themes/analisa/konfirmasi/analisa','themes/komite/kredit','themes/penolakan/pengajuan','themes/penolakan/tambah','themes/penolakan/edit','themes/komite/kredit/survei/analisa')? 'active': '' }}">
                <a href="#">
                    <i class="fa fa-suitcase"></i>
                    <span>Analisa Kredit</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">

                    <li class="{{ request()->is('analisa/penjadwalan') ? 'active' : '' }}">
                        <a href="{{ route('analisa.penjadwalan') }}" title="Penjadwalan Survey">
                            <i class="fa fa-calendar"></i>
                            Penjadwalan
                        </a>
                    </li>

                    <li
                        class="{{ request()->is('themes/permohonan/analisa','themes/analisa/usaha/perdagangan','themes/analisa/usaha/pertanian','themes/analisa/usaha/jasa','themes/analisa/usaha/lainnya','themes/analisa/identitas/usaha/perdagangan','themes/analisa/barang/usaha/perdagangan','themes/analisa/keuangan/usaha/perdagangan','themes/analisa/informasi/usaha/pertanian','themes/analisa/biaya/usaha/pertanian','themes/analisa/keuangan/usaha/pertanian','themes/analisa/keuangan/usaha/jasa','themes/analisa/identitas/usaha/lainnya','themes/analisa/identitas/usaha/lainnya','themes/analisa/keuangan/usaha/lainnya','themes/analisa/keuangan','themes/analisa/kepemilikan','themes/analisa/jaminan/kendaraan','themes/analisa/jaminan/tanah','themes/analisa/jaminan/lainnya','themes/analisa/5c/character*','themes/analisa/5c/capacity*','themes/analisa/5c/capital*','themes/analisa/5c/collateral*','themes/analisa/5c/condition*','themes/analisa/kualitatif/karakter*','themes/analisa/kualitatif/usaha*','themes/analisa/memorandum/kebutuhan','themes/analisa/memorandum/sandi','themes/analisa/memorandum/usulan','themes/analisa/administrasi','themes/analisa/konfirmasi/analisa')? 'active': '' }}">
                        <a href="{{ route('permohonan.analisa') }}">
                            <i class="fa fa-edit"></i>
                            Input Analisa
                        </a>
                    </li>

                    <li class="{{ request()->is('themes/komite/kredit') ? 'active' : '' }}">
                        <a href="{{ route('komite.kredit') }}" title="Input Persetujuan">
                            <i class="fa fa-file-text-o"></i>
                            Input Persetujuan
                        </a>
                    </li>
                    <li
                        class="{{ request()->is('themes/penolakan/pengajuan', 'themes/penolakan/tambah', 'themes/penolakan/edit') ? 'active' : '' }}">
                        <a href="{{ route('penolakan.pengajuan') }}" title="Input Penolakan">
                            <i class="fa fa-close"></i>
                            Input Penolakan
                        </a>
                    </li>
                    <li class="{{ request()->is('themes/komite/kredit/survei/analisa') ? 'active' : '' }}">
                        <a href="{{ route('survei.analisa') }}">
                            <i class="fa fa-hourglass-start"></i>
                            Survey dan Analisa
                        </a>
                    </li>

                </ul>
            </li>

            <li class="treeview">
                <a href="#">
                    <i class="fa fa-file-text"></i>
                    <span>Administratif</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li class="{{ request()->is('themes/notifikasi/kredit') ? 'active' : '' }}">
                        <a href="{{ route('notifikasi_kredit') }}" title="Tambah Notifikasi">
                            <i class="fa fa-plus"></i>
                            Add Notifikasi
                        </a>
                    </li>
                    <li class="">
                        <a href="">
                            <i class="fa fa-check"></i>
                            Otor Notifikasi
                        </a>
                    </li>
                    <li class="{{ request()->is('themes/notifikasi/perjanjian/kredit') ? 'active' : '' }}">
                        <a href="{{ route('perjanjian.kredit') }}" title="Perjanjian Kredit">
                            <i class="fa fa-exclamation-circle"></i>
                            Perjanjian Kredit
                        </a>
                    </li>
                    <li class="{{ request()->is('themes/notifikasi/realisasi/kredit') ? 'active' : '' }}">
                        <a href="{{ route('realisasi.kredit') }}" title="Realisasi Kredit">
                            <i class="fa fa-flag"></i>
                            Realisasi Kredit
                        </a>
                    </li>
                </ul>
            </li>

            <li
                class="treeview {{ request()->is('cetak/pengajuan', 'themes/cetak/analisa/kredit', 'themes/notifikasi/kredit', 'themes/fiducia', 'themes/notifikasi/perjanjian/kredit', 'themes/cetak/penolakan/kredit') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-print"></i>
                    <span>Cetak Berkas</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">

                    <li class="{{ request()->is('cetak/pengajuan', 'cetak/pengajuan/detail') ? 'active' : '' }}">
                        <a href="{{ route('cetak.pengajuan.index') }}" title="Berkas Pengajuan">
                            <i class="fa fa-users"></i>
                            Pengajuan
                        </a>
                    </li>

                    <li class="{{ request()->is('themes/cetak/analisa/kredit') ? 'active' : '' }}">
                        <a href="{{ route('analisa.kredit') }}" title="Cetak Analisa Kredit">
                            <i class="fa fa-suitcase"></i>
                            Analisa Kredit
                        </a>
                    </li>
                    <li class="">
                        <a href="#">
                            <i class="fa fa-file-text-o"></i>
                            Persetujuan Kredit
                        </a>
                    </li>

                    <li class="{{ request()->is('themes/notifikasi/kredit') ? 'active' : '' }}">
                        <a href="{{ route('notifikasi_kredit') }}" title="Cetak Notifikasi">
                            <i class="fa fa-bell-o"></i>
                            Notifikasi Kredit
                        </a>
                    </li>

                    <li class="{{ request()->is('themes/fiducia') ? 'active' : '' }}">
                        <a href="{{ route('fiducia') }}" title="Perhitungan Kredit">
                            <i class="fa fa-truck"></i>
                            Penjaminan Fiducia
                        </a>
                    </li>

                    <li class="{{ request()->is('themes/notifikasi/perjanjian/kredit') ? 'active' : '' }}">
                        <a href="{{ route('perjanjian.kredit') }}" title="Cetak Perjanjian Kredit">
                            <i class="fa fa-exclamation-circle"></i>
                            Perjanjian Kredit
                        </a>
                    </li>

                    <li class="{{ request()->is('themes/cetak/penolakan/kredit') ? 'active' : '' }}">
                        <a href="{{ route('data_penolakan.kredit') }}" title="Cetak Penolakan">
                            <i class="fa fa-close"></i>
                            Penolakan Kredit
                        </a>
                    </li>

                </ul>
            </li>

            <li
                class="treeview {{ request()->is('laporan/fasilitas', 'laporan/realisasi', 'laporan/penolakan', 'laporan/pendaftaran', 'laporan/survei') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-folder-open"></i>
                    <span>Laporan</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li class="{{ request()->is('laporan/fasilitas') ? 'active' : '' }}">
                        <a href="/laporan/fasilitas">
                            <i class="fa fa-bank"></i>
                            Fasilitas Kredit
                        </a>
                    </li>
                    <li class="{{ request()->is('laporan/realisasi') ? 'active' : '' }}">
                        <a href="/laporan/realisasi">
                            <i class="fa fa-flag"></i>
                            Realisasi Kredit
                        </a>
                    </li>
                    <li class="{{ request()->is('laporan/penolakan') ? 'active' : '' }}">
                        <a href="/laporan/penolakan">
                            <i class="fa fa-close"></i>
                            Penolakan Kredit
                        </a>
                    </li>
                    <li class="{{ request()->is('laporan/pendaftaran') ? 'active' : '' }}">
                        <a href="/laporan/pendaftaran">
                            <i class="fa fa-users"></i>
                            Pendaftaran Kredit
                        </a>
                    </li>
                    <li class="{{ request()->is('laporan/survei') ? 'active' : '' }}">
                        <a href="/laporan/survei">
                            <i class="fa fa-hourglass-start"></i>
                            Survey dan Analisa
                        </a>
                    </li>
                </ul>
            </li>

            <li class="header">TOOLS</li>
            <li
                class="treeview {{ request()->is('perhitungan/flat', 'perhitungan/efektif_musiman', 'perhitungan/simulasi') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-folder-open"></i>
                    <span>Alat Bantu</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li
                        class="{{ request()->is('perhitungan/flat', 'perhitungan/efektif_musiman') ? 'active' : '' }}">
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
            </li>
        </ul>
    </section>
</aside>
