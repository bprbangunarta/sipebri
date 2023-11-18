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

            <li class="treeview">
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
                        class="{{ request()->is('pengajuan', 'nasabah/edit', 'pendamping/edit', 'pengajuan/edit', 'pengajuan/agunan', 'survei/edit', 'konfirmasi/pengajuan') ? 'active' : '' }}">
                        <a href="{{ route('pengajuan.index') }}" title="Otorisasi Pengajuan">
                            <i class="fa fa-check"></i>
                            Otor Pengajuan
                        </a>
                    </li>


                </ul>
            </li>

            <li class="treeview">
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

            {{-- <li class="{{ request()->is('data/pengajuan', 'tracking/pengajuan') ? 'active' : '' }}">
                <a href="{{ route('pengajuan.data') }}" title="Data Pengajuan">
                    <i class="fa fa-user"></i>
                    Data Pengajuan
                </a>
            </li> --}}

            {{-- @can('tambah pengajuan kredit')
                <li
                    class="{{ request()->is('pengajuan', 'nasabah/edit', 'pendamping/edit', 'pengajuan/edit', 'pengajuan/agunan', 'survei/edit', 'konfirmasi/pengajuan') ? 'active' : '' }}">
                    <a href="{{ route('pengajuan.index') }}" title="Permission" title="Input Pengajuan">
                        <i class="fa fa-edit"></i>
                        Input Pengajuan
                    </a>
                </li>
            @endcan --}}

            {{-- @can('input analisa kta')
                <li class="#">
                    <a href="#" title="Input Analisa KTA">
                        <i class="fa fa-edit"></i>
                        Input Analisa KTA
                    </a>
                </li>
            @endcan --}}

            {{-- @can('otorisasi pengajuan kredit')
                <li
                    class="{{ request()->is('pengajuan', 'nasabah/edit', 'pendamping/edit', 'pengajuan/edit', 'pengajuan/agunan', 'survei/edit', 'otorisasi/pengajuan') ? 'active' : '' }}">
                    <a href="{{ route('pengajuan.index') }}" title="Otorisasi Pengajuan">
                        <i class="fa fa-check-square-o"></i>
                        Otorisasi Pengajuan
                    </a>
                </li>
            @endcan --}}

            {{-- @can('input analisa')
                <li
                    class="{{ request()->is('themes/permohonan/analisa','themes/analisa/usaha/perdagangan','themes/analisa/usaha/pertanian','themes/analisa/usaha/jasa','themes/analisa/usaha/lainnya','themes/analisa/identitas/usaha/perdagangan','themes/analisa/barang/usaha/perdagangan','themes/analisa/keuangan/usaha/perdagangan','themes/analisa/informasi/usaha/pertanian','themes/analisa/biaya/usaha/pertanian','themes/analisa/keuangan/usaha/pertanian','themes/analisa/keuangan/usaha/jasa','themes/analisa/identitas/usaha/lainnya','themes/analisa/identitas/usaha/lainnya','themes/analisa/keuangan/usaha/lainnya','themes/analisa/keuangan','themes/analisa/kepemilikan','themes/analisa/jaminan/kendaraan','themes/analisa/jaminan/tanah','themes/analisa/jaminan/lainnya','themes/analisa/5c/character*','themes/analisa/5c/capacity*','themes/analisa/5c/capital*','themes/analisa/5c/collateral*','themes/analisa/5c/condition*','themes/analisa/kualitatif/karakter*','themes/analisa/kualitatif/usaha*','themes/analisa/memorandum/kebutuhan','themes/analisa/memorandum/sandi','themes/analisa/memorandum/usulan','themes/analisa/administrasi','themes/analisa/konfirmasi/analisa')? 'active': '' }}">
                    <a href="{{ route('permohonan.analisa') }}" title="Input Analisa">
                        <i class="fa fa-edit"></i>
                        Analisa Kredit
                    </a>
                </li>
            @endcan --}}

            {{-- @can('penjadwalan survey')
                <li class="{{ request()->is('analisa/penjadwalan') ? 'active' : '' }}">
                    <a href="{{ route('analisa.penjadwalan') }}" title="Penjadwalan Survey">
                        <i class="fa fa-calendar"></i>
                        Data Penjadwalan
                    </a>
                </li>
            @endcan --}}

            {{-- @can('menu permohonan')
                <li
                    class="{{ request()->is('themes/penolakan/pengajuan', 'themes/penolakan/tambah', 'themes/penolakan/edit') ? 'active' : '' }}">
                    <a href="{{ route('penolakan.pengajuan') }}" title="Input Penolakan">
                        <i class="fa fa-ban"></i>
                        Input Penolakan
                    </a>
                </li>

                <li class="{{ request()->is('themes/komite/kredit') ? 'active' : '' }}">
                    <a href="{{ route('komite.kredit') }}" title="Input Persetujuan">
                        <i class="fa fa-check-square-o"></i>
                        Input Persetujuan
                    </a>
                </li>

                <li class="{{ request()->is('themes/komite/kredit/survei/analisa') ? 'active' : '' }}">
                    <a href="{{ route('survei.analisa') }}">
                        <i class="fa fa-file-text-o"></i>
                        Survey dan Analisa
                    </a>
                </li>
            @endcan --}}

            @can('menu cetak')
                <li class="header">CETAK BERKAS</li>
            @endcan

            <li class="treeview">
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
                @endcan

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

        <li class="treeview">
            <a href="#">
                <i class="fa fa-folder-open"></i>
                <span>Laporan</span>
                <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                </span>
            </a>
            <ul class="treeview-menu">
                <li class="">
                    <a href="#">
                        <i class="fa fa-bank"></i>
                        Fasilitas Kredit
                    </a>
                </li>
                <li class="">
                    <a href="#">
                        <i class="fa fa-flag"></i>
                        Realisasi Kredit
                    </a>
                </li>
                <li class="">
                    <a href="#">
                        <i class="fa fa-close"></i>
                        Penolakan Kredit
                    </a>
                </li>
                <li class="">
                    <a href="#">
                        <i class="fa fa-users"></i>
                        Pendaftaran Kredit
                    </a>
                </li>
                <li class="">
                    <a href="#">
                        <i class="fa fa-hourglass-start"></i>
                        Survey dan Analisa
                    </a>
                </li>
            </ul>
        </li>

        <li class="header">ALAT BANTU</li>
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

        {{-- @can('cetak pengajuan')
                <li class="{{ request()->is('cetak/pengajuan', 'cetak/pengajuan/detail') ? 'active' : '' }}">
                    <a href="{{ route('cetak.pengajuan.index') }}" title="Berkas Pengajuan">
                        <i class="fa fa-print"></i>
                        Berkas Pengajuan
                    </a>
                </li>
            @endcan

            @can('cetak notifikasi')
                <li class="{{ request()->is('themes/notifikasi/kredit') ? 'active' : '' }}">
                    <a href="{{ route('notifikasi_kredit') }}" title="Cetak Notifikasi">
                        <i class="fa fa-print"></i>
                        Notifikasi Kredit
                    </a>
                </li>
            @endcan

            @can('cetak perjanjian')
                <li class="{{ request()->is('themes/notifikasi/perjanjian/kredit') ? 'active' : '' }}">
                    <a href="{{ route('perjanjian.kredit') }}" title="Cetak Perjanjian Kredit">
                        <i class="fa fa-print"></i>
                        Perjanjian Kredit
                    </a>
                </li>
            @endcan

            @can('realisasi kredit')
                <li class="{{ request()->is('themes/notifikasi/realisasi/kredit') ? 'active' : '' }}">
                    <a href="{{ route('realisasi.kredit') }}" title="Cetak Realisasi Kredit">
                        <i class="fa fa-print"></i>
                        Realisasi Kredit
                    </a>
                </li>
            @endcan

            @can('menu permohonan')
                <li class="{{ request()->is('themes/cetak/analisa/kredit') ? 'active' : '' }}">
                    <a href="{{ route('analisa.kredit') }}" title="Cetak Analisa Kredit">
                        <i class="fa fa-print"></i>
                        Analisa Kredit
                    </a>
                </li>

                <li class="{{ request()->is('themes/cetak/penolakan/kredit') ? 'active' : '' }}">
                    <a href="{{ route('data_penolakan.kredit') }}" title="Cetak Penolakan">
                        <i class="fa fa-print"></i>
                        Penolakan Kredit
                    </a>
                </li>

                <li class="">
                    <a href="#" title="Cetak Persetujuan Komite">
                        <i class="fa fa-print"></i>
                        Persetujuan Komite
                    </a>
                </li>
            @endcan

            @can('cetak fiducia')
                <li class="{{ request()->is('themes/fiducia') ? 'active' : '' }}">
                    <a href="{{ route('fiducia') }}" title="Perhitungan Kredit">
                        <i class="fa fa-print"></i>
                        Pendaftaran Fiducia
                    </a>
                </li>
            @endcan --}}

        {{-- <li class="header">SIMULASI</li>
            <li class="{{ request()->is('perhitungan/flat', 'perhitungan/efektif_musiman') ? 'active' : '' }}">
                <a href="{{ route('flat') }}" title="Perhitungan Kredit">
                    <i class="fa fa-calculator"></i>
                    Perhitungan Kredit
                </a>
            </li>
            <li class="{{ request()->is('perhitungan/simulasi') ? 'active' : '' }}">
                <a href="{{ route('simulasi_ajk') }}" title="Perhitungan Asuransi">
                    <i class="fa fa-calculator"></i>
                    Perhitungan Asuransi
                </a>
            </li> --}}


        <li class="header">LAPORAN</li>

        <li>
            <a href="#" title="Data Global">
                <i class="fa fa-file-text-o"></i>
                <span>Data Global</span>
            </a>
        </li>

        <li>
            <a href="#" title="Sudah Survey">
                <i class="fa fa-file-text-o"></i>
                <span>Sudah Survey</span>
            </a>
        </li>
    </ul>
</section>
</aside>
