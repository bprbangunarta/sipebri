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
                                    class="{{ request()->is('themes/rsc/analisa/usaha/lain/identitas*') ? 'active' : '' }}">
                                    <a href="{{ route('rsc.usaha.lain.identitas', ['kode' => $data->kode, 'rsc' => $data->rsc, 'kode_usaha' => $data->kode_usaha, 'status_rsc' => $data->status_rsc]) }}"
                                        class="{{ request()->is('themes/rsc/analisa/usaha/lain/identitas*') ? 'text-bold' : '' }}">
                                        IDENTITAS
                                    </a>
                                </li>

                                <li class="{{ request()->is('themes/rsc/analisa/usaha/lain/bahan*') ? 'active' : '' }}">
                                    <a href="{{ route('rsc.usaha.lain.bahan', ['kode' => $data->kode, 'rsc' => $data->rsc, 'kode_usaha' => $data->kode_usaha, 'status_rsc' => $data->status_rsc]) }}"
                                        class="{{ request()->is('themes/rsc/analisa/usaha/lain/bahan*') ? 'text-bold' : '' }}">
                                        BAHAN BAKU
                                    </a>
                                </li>

                                <li
                                    class="{{ request()->is('themes/rsc/analisa/usaha/lain/keuangan*') ? 'active' : '' }}">
                                    <a href="{{ route('rsc.usaha.lain.keuangan', ['kode' => $data->kode, 'rsc' => $data->rsc, 'kode_usaha' => $data->kode_usaha, 'status_rsc' => $data->status_rsc]) }}"
                                        class="{{ request()->is('themes/rsc/analisa/usaha/lain/keuangan*') ? 'text-bold' : '' }}">
                                        KEUANGAN
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
