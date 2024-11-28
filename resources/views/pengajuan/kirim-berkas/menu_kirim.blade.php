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
                        <div class="box box-solid">
                            <div class="box-header with-border">
                                <h3 class="box-title"><b>Informasi</b></h3>

                                <div class="box-tools">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="box-body no-padding">
                                <ul class="nav nav-pills nav-stacked">
                                    <li>
                                        <a href="#">
                                            <p>
                                                1. Pilih dari kantor dan kantor tujuan <br>
                                                2. Data harus diisi semua ya. <br>
                                                3. Jika di kantor yang sama gunakan &nbsp;&nbsp;&nbsp;&nbsp; serah
                                                terima berkas.
                                            </p>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-9">
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs">
                                <li class="{{ request()->is('kirim/berkas/index') ? 'active' : '' }}">
                                    <a href="{{ route('kirim.berkas.index') }}"
                                        class="{{ request()->is('kirim/berkas/index') ? 'text-bold' : '' }}">
                                        KIRIM BERKAS
                                    </a>
                                </li>

                                <li class="{{ request()->is('serah/terima/index') ? 'active' : '' }}">
                                    <a href="{{ route('serah.terima.index') }}"
                                        class="{{ request()->is('serah/terima/index') ? 'text-bold' : '' }}">
                                        SERAH TERIMA BERKAS
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
