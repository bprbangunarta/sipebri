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
                    <div class="col-xs-12">
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs">
                                <li class="{{ request()->is('analisa/penjadwalan*') ? 'active' : '' }}">
                                    <a href="{{ route('analisa.penjadwalan') }}"
                                        class="{{ request()->is('analisa/penjadwalan*') ? 'text-bold' : '' }}"
                                        style="cursor: pointer;">
                                        PENJADWALAN
                                    </a>
                                </li>

                                <li class="{{ request()->is('hasil/survei*') ? 'active' : '' }}">
                                    <a href="{{ route('hasil.survei') }}"
                                        class="{{ request()->is('hasil/survei*') ? 'text-bold' : '' }}"
                                        style="cursor: pointer;">
                                        HASIL SURVEI
                                    </a>
                                </li>

                                <li class="{{ request()->is('hasil/pelaksanaan*') ? 'active' : '' }}">
                                    <a href="{{ route('pelaksanaan.survei') }}"
                                        class="{{ request()->is('hasil/pelaksanaan*') ? 'text-bold' : '' }}"
                                        style="cursor: pointer;">
                                        PELAKSANAAN SURVEI
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
