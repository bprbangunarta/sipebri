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
                        {{-- @include('theme.menu-static', [$data]) --}}
                        @include('theme.menu-static')
                    </div>

                    <div class="col-xs-9">
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs">
                                <li class="{{ request()->is('themes/analisa/kualitatif/karakter') ? 'active' : '' }}">
                                    <a href="{{ route('kualitatif.karakter') }}" class="{{ request()->is('themes/analisa/kualitatif/karakter') ? 'text-bold' : '' }}">
                                        KARAKTER NASABAH
                                    </a>
                                </li>

                                <li class="{{ request()->is('themes/analisa/kualitatif/usaha') ? 'active' : '' }}">
                                    <a href="{{ route('kualitatif.usaha') }}" class="{{ request()->is('themes/analisa/kualitatif/usaha') ? 'text-bold' : '' }}">
                                        USAHA NASABAH
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
