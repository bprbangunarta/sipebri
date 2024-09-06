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
                    <div class="col-md-12">
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs">
                                <li class="ml-4 {{ request()->is('perhitungan/simulasi/pasific') ? 'active' : '' }}">
                                    <a href="{{ route('simulasi_ajk_pasific') }}"
                                        class="{{ request()->is('perhitungan/simulasi/pasific') ? 'text-bold' : '' }}">
                                        FORM ASURANSI PACIFIC
                                    </a>
                                </li>
                                <li class="ml-4 {{ request()->is('perhitungan/simulasi/bumida') ? 'active' : '' }}">
                                    <a href="{{ route('simulasi_ajk_bumida') }}"
                                        class="{{ request()->is('perhitungan/simulasi/bumida') ? 'text-bold' : '' }}">
                                        FORM ASURANSI BUMIDA
                                    </a>
                                </li>
                                <li class="ml-4 {{ request()->is('perhitungan/simulasi_tlo') ? 'active' : '' }}">
                                    <a href="{{ route('simulasi.tlo') }}"
                                        class="{{ request()->is('perhitungan/simulasi_tlo') ? 'text-bold' : '' }}">
                                        FORM ASURANSI PREMI TLO
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
