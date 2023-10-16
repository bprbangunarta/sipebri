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
                                <li class="{{ request()->is('themes/analisa/5c/character') ? 'active' : '' }}">
                                    <a href="{{ route('analisa5c.character', ['pengajuan' => $pengajuan]) }}"
                                        class="{{ request()->is('themes/analisa/5c/character') ? 'text-bold' : '' }}">
                                        CHARACTER
                                    </a>
                                </li>

                                <li class="{{ request()->is('themes/analisa/5c/capacity') ? 'active' : '' }}">
                                    <a href="/themes/analisa/5c/capacity"
                                        class="{{ request()->is('themes/analisa/5c/capacity') ? 'text-bold' : '' }}">
                                        CAPACITY
                                    </a>
                                </li>

                                <li class="{{ request()->is('themes/analisa/5c/capital') ? 'active' : '' }}">
                                    <a href="/themes/analisa/5c/capital"
                                        class="{{ request()->is('themes/analisa/5c/capital') ? 'text-bold' : '' }}">
                                        CAPITAL
                                    </a>
                                </li>

                                <li class="{{ request()->is('themes/analisa/5c/collateral') ? 'active' : '' }}">
                                    <a href="/themes/analisa/5c/collateral"
                                        class="{{ request()->is('themes/analisa/5c/collateral') ? 'text-bold' : '' }}">
                                        COLLATERAL
                                    </a>
                                </li>

                                <li class="{{ request()->is('themes/analisa/5c/condition') ? 'active' : '' }}">
                                    <a href="/themes/analisa/5c/condition"
                                        class="{{ request()->is('themes/analisa/5c/condition') ? 'text-bold' : '' }}">
                                        CONDITION
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
