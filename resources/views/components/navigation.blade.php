<aside class="main-sidebar">
    <section class="sidebar">
        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{ asset('theme/dist/img/user2-160x160.jpg') }}" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p style="text-transform: uppercase;">{{ Auth::user()->username }}</p>
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Cari Debitur">
                <span class="input-group-btn">
                    <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i
                            class="fa fa-search"></i>
                    </button>
                </span>
            </div>
        </form>

        <ul class="sidebar-menu" data-widget="tree">
            <li class="header">MAIN MENU</li>

            @hasanyrole($roles)
                <li class="{{ request()->is('/themes/dashboard') ? 'active' : '' }}">
                    <a href="/themes/dashboard" title="Dashboard">
                        <i class="fa fa-laptop"></i> <span>Dashboard</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                </li>
            @endhasanyrole

            <li class="treeview active">
                <a href="#" title="Data Debitur">
                    <i class="fa fa-file-text-o"></i>
                    <span>Permohonan</span>
                    <span class="pull-right-container">
                        <small class="label pull-right bg-blue">4</small>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li class="{{ request()->is('themes/permohonan/analisa', 'themes/analisa/usaha/perdagangan', 'themes/analisa/usaha/pertanian', 'themes/analisa/usaha/jasa', 'themes/analisa/usaha/lainnya', 'themes/analisa/identitas/usaha/perdagangan', 'themes/analisa/barang/usaha/perdagangan', 'themes/analisa/keuangan/usaha/perdagangan', 'themes/analisa/informasi/usaha/pertanian', 'themes/analisa/biaya/usaha/pertanian', 'themes/analisa/keuangan/usaha/pertanian', 'themes/analisa/keuangan/usaha/jasa', 'themes/analisa/identitas/usaha/lainnya', 'themes/analisa/identitas/usaha/lainnya', 'themes/analisa/keuangan/usaha/lainnya', 'themes/analisa/keuangan', 'themes/analisa/kepemilikan', 'themes/analisa/jaminan/kendaraan', 'themes/analisa/jaminan/tanah', 'themes/analisa/jaminan/lainnya', 'themes/analisa/5c/character*', 'themes/analisa/5c/capacity*', 'themes/analisa/5c/capital*', 'themes/analisa/5c/collateral*', 'themes/analisa/5c/condition*', 'themes/analisa/kualitatif/karakter*', 'themes/analisa/kualitatif/usaha*', 'themes/analisa/memorandum/kebutuhan', 'themes/analisa/memorandum/sandi', 'themes/analisa/memorandum/usulan', 'themes/analisa/administrasi', 'themes/analisa/konfirmasi/analisa') ? 'active' : '' }}">
                        <a href="{{ route('permohonan.analisa') }}">
                            <i class="fa fa-circle-o"></i>
                            Input Analisa
                        </a>
                    </li>

                    <li class="{{ request()->is('themes/komite/kredit') ? 'active' : '' }}">
                        <a href="{{ route('komite.kredit') }}">
                            <i class="fa fa-circle-o"></i>
                            Input Persetujuan
                        </a>
                    </li>

                    <li class="{{ request()->is('themes/penolakan/pengajuan', 'themes/penolakan/tambah') ? 'active' : '' }}">
                        <a href="{{ route('penolakan.pengajuan') }}">
                            <i class="fa fa-circle-o"></i>
                            Input Penolakan
                        </a>
                    </li>
                </ul>
            </li>

            <li class="treeview">
                <a href="#" title="Data Debitur">
                    <i class="fa fa-cube"></i>
                    <span>Perhitungan</span>
                    <span class="pull-right-container">
                        <small class="label pull-right bg-blue">2</small>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li>
                        <a href="#" title="Wilayah Kalijati">
                            <i class="fa fa-circle-o"></i>
                            Perhitungan Kredit
                        </a>
                    </li>
                    <li>
                        <a href="#" title="Wilayah Subang">
                            <i class="fa fa-circle-o"></i>
                            Perhitungan Asuransi
                        </a>
                    </li>
                </ul>
            </li>


            <li class="header">LAPORAN</li>
            <li>
                <a href="#" title="Janji Bayar">
                    <i class="fa fa-hourglass-end"></i> <span>Tracking</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
            </li>

            <li>
                <a href="#" title="Data Debitur">
                    <i class="fa fa-file-archive-o"></i> <span>Data Global</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
            </li>

            {{-- <li class="header">KASI KREDIT</li>
            <li class="{{ request()->is('dashboard') ? 'active' :'' }}">
                <a href="/dashboard" title="Dashboard">
                    <i class="fa fa-laptop"></i> <span>Dashboard</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
            </li>

            <li>
                <a href="#" title="Janji Bayar">
                    <i class="fa fa-calendar"></i> <span>Janji Bayar</span>
                    <span class="pull-right-container">
                        <small class="label pull-right bg-yellow">3</small>
                    </span>
                </a>
            </li>

            <li>
                <a href="#" title="Data Debitur">
                    <i class="fa fa-user"></i> <span>Data Debitur</span>
                    <span class="pull-right-container">
                        <small class="label pull-right bg-green">2301</small>
                    </span>
                </a>
            </li>

            <li>
                <a href="#" title="Surat Tugas">
                    <i class="fa fa-file-text"></i> <span>Surat Tugas</span>
                    <span class="pull-right-container">
                        <small class="label pull-right bg-red">3</small>
                    </span>
                </a>
            </li>

            <li>
                <a href="#" title="Catatan Kasi">
                    <i class="fa fa-comments"></i> <span>Catatan Kasi</span>
                    <span class="pull-right-container">
                        <small class="label pull-right bg-blue">3</small>
                    </span>
                </a>
            </li>

            <li>
                <a href="#" title="Data Prospek">
                    <i class="fa fa-rocket"></i> <span>Data Prospek</span>
                    <span class="pull-right-container">
                        <small class="label pull-right bg-purple">3</small>
                    </span>
                </a>
            </li>

            <li>
                <a href="#" title="Rekap Surat Tugas">
                    <i class="fa fa-files-o"></i> <span>Rekap Surtug</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
            </li>

            <li class="header">WRITE OFF</li>
            <li>
                <a href="#" title="Data Debitur">
                    <i class="fa fa-user"></i> <span>Debitur WO</span>
                    <span class="pull-right-container">
                        <small class="label pull-right bg-green">600</small>
                    </span>
                </a>
            </li>

            <li>
                <a href="#" title="Surat Tugas">
                    <i class="fa fa-file-text"></i> <span>Surat Tugas</span>
                    <span class="pull-right-container">
                        <small class="label pull-right bg-red">3</small>
                    </span>
                </a>
            </li>

            <li class="header">CUSTOMER CARE</li>
            <li>
                <a href="#" title="Dashboard">
                    <i class="fa fa-laptop"></i> <span>Dashboard</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
            </li>

            <li>
                <a href="#" title="Tele Billing">
                    <i class="fa fa-phone-square"></i> <span>Tele Billing</span>
                    <span class="pull-right-container">
                        <small class="label pull-right bg-green">3</small>
                    </span>
                </a>
            </li>

            <li>
                <a href="#" title="Janji Bayar">
                    <i class="fa fa-calendar"></i> <span>Janji Bayar</span>
                    <span class="pull-right-container">
                        <small class="label pull-right bg-yellow">3</small>
                    </span>
                </a>
            </li>

            <li class="treeview">
                <a href="#" title="Data Debitur">
                    <i class="fa fa-user"></i>
                    <span>Data Debitur</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li>
                        <a href="#" title="Wilayah Kalijati">
                            <i class="fa fa-circle-o"></i>
                            Kalijati</a>
                    </li>
                    <li>
                        <a href="#" title="Wilayah Subang">
                            <i class="fa fa-circle-o"></i>
                            Subang</a>
                    </li>
                    <li>
                        <a href="#" title="Wilayah Pagaden">
                            <i class="fa fa-circle-o"></i>
                            Pagaden</a>
                    </li>
                    <li>
                        <a href="#" title="Wilayah Sukamandi">
                            <i class="fa fa-circle-o"></i>
                            Sukamandi</a>
                    </li>
                    <li>
                        <a href="#" title="Wilayah Jalancagak">
                            <i class="fa fa-circle-o"></i>
                            Jalancagak</a>
                    </li>
                    <li>
                        <a href="#" title="Wilayah Pusakajaya">
                            <i class="fa fa-circle-o"></i>
                            Pusakajaya</a>
                    </li>
                    <li>
                        <a href="#" title="Wilayah Pamanukan">
                            <i class="fa fa-circle-o"></i>
                            Pamanukan</a>
                    </li>
                </ul>
            </li> --}}
        </ul>
    </section>
</aside>
