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
                                <li class="{{ request()->is('themes/rsc/analisa/usaha/perdagangan') ? 'active' : '' }}">
                                    <a href="{{ route('rsc.usaha.perdagangan', ['kode' => $data->kode, 'rsc' => $data->rsc, 'status_rsc' => $data->status_rsc]) }}"
                                        class="{{ request()->is('themes/rsc/analisa/usaha/perdagangan') ? 'text-bold' : '' }}">
                                        USAHA PERDAGANGAN
                                    </a>
                                </li>

                                <li class="{{ request()->is('themes/rsc/analisa/usaha/pertanian') ? 'active' : '' }}">
                                    <a href="{{ route('rsc.usaha.pertanian', ['kode' => $data->kode, 'rsc' => $data->rsc, 'status_rsc' => $data->status_rsc]) }}"
                                        class="{{ request()->is('themes/rsc/analisa/usaha/pertanian') ? 'text-bold' : '' }}">
                                        USAHA PERTANIAN
                                    </a>
                                </li>

                                <li class="{{ request()->is('themes/rsc/analisa/usaha/jasa') ? 'active' : '' }}">
                                    <a href="{{ route('rsc.usaha.jasa', ['kode' => $data->kode, 'rsc' => $data->rsc, 'status_rsc' => $data->status_rsc]) }}"
                                        class="{{ request()->is('themes/rsc/analisa/usaha/jasa') ? 'text-bold' : '' }}">
                                        USAHA JASA
                                    </a>
                                </li>

                                <li class="{{ request()->is('themes/rsc/analisa/usaha/lain') ? 'active' : '' }}">
                                    <a href="{{ route('rsc.usaha.lain', ['kode' => $data->kode, 'rsc' => $data->rsc, 'status_rsc' => $data->status_rsc]) }}"
                                        class="{{ request()->is('themes/rsc/analisa/usaha/lain') ? 'text-bold' : '' }}">
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
