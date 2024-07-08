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
                        @include('theme.menu-rsc', [$data])
                    </div>

                    <div class="col-xs-9">
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs">
                                <li
                                    class="{{ request()->is('themes/rsc/analisa/usaha/perdagangan/identitas*') ? 'active' : '' }}">
                                    <a href="{{ route('rsc.index.usaha.perdagangan.identitas', ['kode' => $data->kode, 'rsc' => $data->rsc, 'kode_usaha' => $data->kode_usaha]) }}"
                                        class="{{ request()->is('themes/rsc/analisa/usaha/perdagangan/identitas*') ? 'text-bold' : '' }}">
                                        IDENTITAS
                                    </a>
                                </li>

                                <li
                                    class="{{ request()->is('themes/rsc/analisa/usaha/perdagangan/barang*') ? 'active' : '' }}">
                                    <a href="{{ route('rsc.usaha.perdagangan.barang', ['kode' => $data->kode, 'rsc' => $data->rsc, 'kode_usaha' => $data->kode_usaha]) }}"
                                        class="{{ request()->is('themes/rsc/analisa/usaha/perdagangan/barang*') ? 'text-bold' : '' }}">
                                        BARANG DAGANG
                                    </a>
                                </li>

                                <li
                                    class="{{ request()->is('themes/rsc/analisa/usaha/perdagangan/keuangan*') ? 'active' : '' }}">
                                    <a href="{{ route('rsc.usaha.perdagangan.keuangan', ['kode' => $data->kode, 'rsc' => $data->rsc, 'kode_usaha' => $data->kode_usaha]) }}"
                                        class="{{ request()->is('themes/rsc/analisa/usaha/perdagangan/keuangan*') ? 'text-bold' : '' }}">
                                        ANALISA KEUANGAN
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
