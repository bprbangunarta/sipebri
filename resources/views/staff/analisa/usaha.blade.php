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
                                <li class="{{ request()->is('themes/analisa/usaha/perdagangan') ? 'active' : '' }}">
                                    <a href="{{ route('perdagangan.in', ['pengajuan' => $pengajuan]) }}"
                                        class="{{ request()->is('themess/analisa/usaha/perdagangan') ? 'text-bold' : '' }}">
                                        USAHA PERDAGANGAN
                                    </a>
                                </li>

                                <li class="{{ request()->is('themes/analisa/usaha/pertanian') ? 'active' : '' }}">
                                    <a href="{{ route('pertanian.ind', ['pengajuan' => $pengajuan]) }}"
                                        class="{{ request()->is('themes/analisa/usaha/pertanian') ? 'text-bold' : '' }}">
                                        USAHA PERTANIAN
                                    </a>
                                </li>

                                <li class="{{ request()->is('themes/analisa/usaha/jasa') ? 'active' : '' }}">
                                    <a href="{{ route('usahajasa.ind', ['pengajuan' => $pengajuan]) }}"
                                        class="{{ request()->is('themes/analisa/usaha/jasa') ? 'text-bold' : '' }}">
                                        USAHA JASA
                                    </a>
                                </li>

                                <li class="{{ request()->is('themes/analisa/usaha/lainnya') ? 'active' : '' }}">
                                    <a href="{{ route('lain.index', ['pengajuan' => $pengajuan]) }}"
                                        class="{{ request()->is('themes/analisa/usaha/lainnya') ? 'text-bold' : '' }}">
                                        USAHA LAINNYA
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
