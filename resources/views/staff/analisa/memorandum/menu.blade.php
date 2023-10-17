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
                                <li class="{{ request()->is('themes/analisa/memorandum/kebutuhan') ? 'active' : '' }}">
                                    <a href="{{ route('memorandum.kebutuhan', ['pengajuan' => $pengajuan]) }}"
                                        class="{{ request()->is('themes/analisa/memorandum/kebutuhan') ? 'text-bold' : '' }}">
                                        KEBUTUHAN DANA
                                    </a>
                                </li>

                                <li class="{{ request()->is('themes/analisa/memorandum/sandi') ? 'active' : '' }}">
                                    <a href="{{ route('memorandum.sandi', ['pengajuan' => $pengajuan]) }}"
                                        class="{{ request()->is('themes/analisa/memorandum/sandi') ? 'text-bold' : '' }}">
                                        SANDI BI
                                    </a>
                                </li>

                                <li class="{{ request()->is('themes/analisa/memorandum/usulan') ? 'active' : '' }}">
                                    <a href="{{ route('memorandum.usulan', ['pengajuan' => $pengajuan]) }}"
                                        class="{{ request()->is('themes/analisa/memorandum/usulan') ? 'text-bold' : '' }}">
                                        USULAN KREDIT
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
