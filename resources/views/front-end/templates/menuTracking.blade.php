<!DOCTYPE html>
<html>

<head>
    @include('theme.header')
</head>

<body class="hold-transition skin-blue layout-top-nav">
    <header class="main-header">
        <nav class="navbar navbar-static-top">
            <div class="container">
                <div class="navbar-header">
                    <a href="/" class="navbar-brand"><b>BPR BANGUNARTA</b></a>
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                        data-target="#navbar-collapse">
                        <i class="fa fa-bars"></i>
                    </button>
                </div>

                <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
                    <ul class="nav navbar-nav">
                        <li class="{{ Route::is('monitoring.tracking') ? 'active' : '' }}">
                            <a href="#">Tracking Kredit</a>
                        </li>
                    </ul>
                </div>

                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">

                        <li class="dropdown user user-menu">
                            <a href="/login">
                                <span class="hidden-xs">Member Area</span>
                            </a>
                        </li>

                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <div class="wrapper">
        <div class="content-wrapper">
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs">
                                <li class="{{ Route::is('monitoring.informasi_tracking') ? 'active' : '' }}">
                                    <a href="{{ route('monitoring.informasi_tracking', ['kode' => $kode]) }}"
                                        class="{{ Route::is('monitoring.informasi_tracking') ? 'text-bold' : '' }}"
                                        style="cursor: pointer;">
                                        INFORMASI
                                    </a>
                                </li>

                                <li class="{{ Route::is('monitoring.tracking') ? 'active' : '' }}">
                                    <a href="{{ route('monitoring.tracking', ['kode' => $kode]) }}"
                                        class="{{ Route::is('monitoring.tracking') ? 'text-bold' : '' }}"
                                        style="cursor: pointer;">
                                        TRACKING PENGAJUAN
                                    </a>
                                </li>
                            </ul>

                            @yield('content')
                        </div>
                    </div>
            </section>
        </div>

    </div>

    <footer class="main-footer">
        <div class="container">
            <div class="pull-right">
                <b>VERSI</b> 1.1.0
            </div>
            <strong>SISTEM PEMBERIAN KREDIT</strong>
        </div>
    </footer>
    @include('theme.footer')
</body>

</html>
