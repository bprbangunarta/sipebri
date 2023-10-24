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
                <input type="text" name="name" class="form-control" value="{{ Request('name') }}" placeholder="Cari Debitur">
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

            <li class="treeview {{ request()->is('data/pengajuan', 'pengajuan') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-users"></i>
                    <span>Pengajuan</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li class="{{ request()->is('data/pengajuan') ? 'active' : '' }}">
                        <a href="{{ route('pengajuan.data') }}" title=" Data Pengajuan">
                            <i class="fa fa-circle-o"></i>
                            Data Pengajuan
                        </a>
                    </li>

                    @can('tambah pengajuan kredit')
                        <li class="{{ request()->is('pengajuan') ? 'active' : '' }}">
                            <a href="{{ route('pengajuan.index') }}" title="Permission" title="Input Pengajuan">
                                <i class="fa fa-circle-o"></i>
                                Input Pengajuan
                            </a>
                        </li>
                    @endcan

                    @can('otorisasi pengajuan kredit')
                        <li class="{{ request()->is('pengajuan') ? 'active' : '' }}">
                            <a href="{{ route('pengajuan.index') }}" title="Otorisasi Pengajuan">
                                <i class="fa fa-circle-o"></i>
                                Otorisasi Pengajuan
                            </a>
                        </li>
                    @endcan

                    <li class="{{ request()->is('tracking') ? 'active' : '' }}">
                        <a href="/tracking" title=" Tracking Pengajuan">
                            <i class="fa fa-circle-o"></i>
                            Tracking Pengajuan
                        </a>
                    </li>
                </ul>
            </li>

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

            @can('menu permohonan')
                <li
                    class="treeview {{ request()->is('themes/permohonan/analisa','themes/analisa/usaha/perdagangan','themes/analisa/usaha/pertanian','themes/analisa/usaha/jasa','themes/analisa/usaha/lainnya','themes/analisa/identitas/usaha/perdagangan','themes/analisa/barang/usaha/perdagangan','themes/analisa/keuangan/usaha/perdagangan','themes/analisa/informasi/usaha/pertanian','themes/analisa/biaya/usaha/pertanian','themes/analisa/keuangan/usaha/pertanian','themes/analisa/keuangan/usaha/jasa','themes/analisa/identitas/usaha/lainnya','themes/analisa/identitas/usaha/lainnya','themes/analisa/keuangan/usaha/lainnya','themes/analisa/keuangan','themes/analisa/kepemilikan','themes/analisa/jaminan/kendaraan','themes/analisa/jaminan/tanah','themes/analisa/jaminan/lainnya','themes/analisa/5c/character*','themes/analisa/5c/capacity*','themes/analisa/5c/capital*','themes/analisa/5c/collateral*','themes/analisa/5c/condition*','themes/analisa/kualitatif/karakter*','themes/analisa/kualitatif/usaha*','themes/analisa/memorandum/kebutuhan','themes/analisa/memorandum/sandi','themes/analisa/memorandum/usulan','themes/analisa/administrasi','themes/analisa/konfirmasi/analisa','themes/komite/kredit','themes/penolakan/pengajuan','analisa/penjadwalan')? 'active': '' }}">
                    <a href="#">
                        <i class="fa fa-file-text-o"></i>
                        <span>Permohonan</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        @can('penjadwalan survey')
                            <li class="{{ request()->is('analisa/penjadwalan') ? 'active' : '' }}">
                                <a href="{{ route('analisa.penjadwalan') }}" title="Penjadwalan">
                                    <i class="fa fa-circle-o"></i>
                                    Penjadwalan
                                </a>
                            </li>
                        @endcan

                        @can('input analisa')
                            <li
                                class="{{ request()->is('themes/permohonan/analisa','themes/analisa/usaha/perdagangan','themes/analisa/usaha/pertanian','themes/analisa/usaha/jasa','themes/analisa/usaha/lainnya','themes/analisa/identitas/usaha/perdagangan','themes/analisa/barang/usaha/perdagangan','themes/analisa/keuangan/usaha/perdagangan','themes/analisa/informasi/usaha/pertanian','themes/analisa/biaya/usaha/pertanian','themes/analisa/keuangan/usaha/pertanian','themes/analisa/keuangan/usaha/jasa','themes/analisa/identitas/usaha/lainnya','themes/analisa/identitas/usaha/lainnya','themes/analisa/keuangan/usaha/lainnya','themes/analisa/keuangan','themes/analisa/kepemilikan','themes/analisa/jaminan/kendaraan','themes/analisa/jaminan/tanah','themes/analisa/jaminan/lainnya','themes/analisa/5c/character*','themes/analisa/5c/capacity*','themes/analisa/5c/capital*','themes/analisa/5c/collateral*','themes/analisa/5c/condition*','themes/analisa/kualitatif/karakter*','themes/analisa/kualitatif/usaha*','themes/analisa/memorandum/kebutuhan','themes/analisa/memorandum/sandi','themes/analisa/memorandum/usulan','themes/analisa/administrasi','themes/analisa/konfirmasi/analisa')? 'active': '' }}">
                                <a href="{{ route('permohonan.analisa') }}" title="Input Analisa">
                                    <i class="fa fa-circle-o"></i>
                                    Input Analisa
                                </a>
                            </li>
                        @endcan

                        <li class="{{ request()->is('themes/komite/kredit') ? 'active' : '' }}">
                            <a href="{{ route('komite.kredit') }}" title="Input Persetujuan">
                                <i class="fa fa-circle-o"></i>
                                Input Persetujuan
                            </a>
                        </li>

                        <li
                            class="{{ request()->is('themes/penolakan/pengajuan', 'themes/penolakan/tambah', 'themes/penolakan/edit') ? 'active' : '' }}">
                            <a href="{{ route('penolakan.pengajuan') }}" title="Input Penolakan">
                                <i class="fa fa-circle-o"></i>
                                Input Penolakan
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan

            <li class="treeview {{ request()->is('perhitungan/flat', 'perhitungan/efektif_musiman', 'perhitungan/simulasi')? 'active': '' }}">
                <a href="#" title="Data Debitur">
                    <i class="fa fa-cube"></i>
                    <span>Perhitungan</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li class="{{ request()->is('perhitungan/flat', 'perhitungan/efektif_musiman')? 'active': '' }}">
                        <a href="{{ route('flat') }}" title="Perhitungan Kredit">
                            <i class="fa fa-circle-o"></i>
                            Perhitungan Kredit
                        </a>
                    </li>
                    <li class="{{ request()->is('perhitungan/simulasi')? 'active': '' }}">
                        <a href="{{ route('simulasi_ajk') }}" title="Perhitungan Asuransi">
                            <i class="fa fa-circle-o"></i>
                            Perhitungan Asuransi
                        </a>
                    </li>
                </ul>
            </li>


            <li class="header">LAPORAN</li>

            <li>
                <a href="#" title="Data Global">
                    <i class="fa fa-file-archive-o"></i>
                    <span>Data Global</span>
                </a>
            </li>
        </ul>
    </section>
</aside>
