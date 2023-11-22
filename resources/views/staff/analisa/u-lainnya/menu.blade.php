<!DOCTYPE html>
<html>

<head>
    @include('theme.header')
</head>

<body class="hold-transition skin-blue fixed sidebar-mini">
    <div class="wrapper">

        @include('theme.topbar')

        <x-navigation></x-navigation>

        <div class="content-wrapper">
            <section class="content">
                <div class="row">
                    <div class="col-md-3">
                        @include('theme.menu-analisa', [$data])
                    </div>

                    <div class="col-xs-9">
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs">
                                <li
                                    class="{{ request()->is('themes/analisa/identitas/usaha/lainnya*') ? 'active' : '' }}">
                                    <a href="{{ route('lain.identitas', ['pengajuan' => $pengajuan, 'kode_usaha' => $kode_usaha]) }}"
                                        class="{{ request()->is('themes/analisa/identitas/usaha/lainnya*') ? 'text-bold' : '' }}">
                                        IDENTITAS
                                    </a>
                                </li>

                                <li class="{{ request()->is('themes/analisa/bahan-baku/usaha/lainnya') ? 'active' : '' }}">
                                    <a href="/themes/analisa/bahan-baku/usaha/lainnya" class="{{ request()->is('themes/analisa/bahan-baku/usaha/lainnya') ? 'text-bold' : '' }}">
                                        BAHAN BAKU
                                    </a>
                                </li>

                                <li
                                    class="{{ request()->is('themes/analisa/keuangan/usaha/lainnya*') ? 'active' : '' }}">
                                    <a href="{{ route('lain.keuangan', ['pengajuan' => $pengajuan, 'kode_usaha' => $kode_usaha]) }}"
                                        class="{{ request()->is('themes/analisa/keuangan/usaha/lainnya*') ? 'text-bold' : '' }}">
                                        KEUANGAN
                                    </a>
                                </li>

                                <li>
                                    <a
                                        href="{{ route('lain.index', ['pengajuan' => $pengajuan, 'kode_usaha' => $kode_usaha]) }}">
                                        <i class="fa fa-arrow-left"></i> KEMBALI
                                    </a>
                                </li>
                            </ul>

                            @yield('content')
                        </div>
                    </div>
            </section>
        </div>

        <footer class="main-footer">
            <div class="pull-right hidden-xs">
                <b>VERSI</b> 1.1.0
            </div>
            <strong>SISTEM PEMBERIAN KREDIT</strong>
        </footer>

        <div class="control-sidebar-bg"></div>
    </div>

    @include('theme.footer')
    @stack('myscript')

    <script>
        $(document).ready(function() {
            $('.sidebar-menu').tree()
        })
    </script>
</body>

</html>
