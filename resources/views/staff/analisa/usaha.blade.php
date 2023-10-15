<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>@yield('title')</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('theme/favicon.png') }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <meta name="description" content="Sistem Informasi Pemberitan Kredit BPR Bangunarta" />
    <meta property="og:locale" content="id_ID" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="SIPEBRI - Pemberian Kredit" />
    <meta property="og:description" content="Sistem Informasi Pemberian Kredit BPR Bangunarta" />
    <meta property="og:url" content="https://sipebri.bprbangunarta.co.id/" />
    <meta property="og:site_name" content="Indiepers" />
    <meta property="og:image" content="https://sipebri.bprbangunarta.co.id/simontok.png" />
    <meta property="og:image:width" content="600" />
    <meta property="og:image:height" content="400" />
    <meta property="og:image:type" content="image/png" />

    @section('jquery')
        <!-- Jquery -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @endsection

    <link rel="stylesheet" href="{{ asset('theme/assets/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/assets/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/assets/Ionicons/css/ionicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/dist/css/AdminLTE.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/dist/css/skins/_all-skins.min.css') }}">

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

    <style>
        .fw-bold {
            font-weight: bold;
        }

        .form-border {
            border: 1px solid black;
        }

        .div-left {
            /* border:1px solid black; */
            width: 49.5%;
            float: left;
        }

        .div-right {
            /* border:1px solid black; */
            width: 49.5%;
            float: right;
        }
    </style>
</head>

<body class="hold-transition skin-blue fixed sidebar-mini">
    <div class="wrapper">
        @include('sweetalert::alert')
        @include('theme.header')

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
                                <li class="{{ request()->is('themes/analisa/usaha/perdagangan') ? 'active' : '' }}">
                                    <a href="{{ route('perdagangan.in', ['pengajuan' => $pengajuan]) }}"
                                        {{-- <a href="/themess/analisa/usaha/perdagangan" --}}
                                        class="{{ request()->is('themess/analisa/usaha/perdagangan') ? 'text-bold' : '' }}">
                                        USAHA PERDAGANGAN
                                    </a>
                                </li>

                                <li class="{{ request()->is('themes/analisa/usaha/pertanian') ? 'active' : '' }}">
                                    <a href="{{ route('pertanian.ind', ['pengajuan' => $pengajuan]) }}"
                                        {{-- <a href="/themes/analisa/usaha/pertanian" --}}
                                        class="{{ request()->is('themes/analisa/usaha/pertanian') ? 'text-bold' : '' }}">
                                        USAHA PERTANIAN
                                    </a>
                                </li>

                                <li class="{{ request()->is('themes/analisa/usaha/jasa') ? 'active' : '' }}">
                                    <a href="/themes/analisa/usaha/jasa"
                                        class="{{ request()->is('themes/analisa/usaha/jasa') ? 'text-bold' : '' }}">
                                        USAHA JASA
                                    </a>
                                </li>

                                <li class="{{ request()->is('themes/analisa/usaha/lainnya') ? 'active' : '' }}">
                                    <a href="/themes/analisa/usaha/lainnya"
                                        class="{{ request()->is('themes/analisa/usaha/lainnya') ? 'text-bold' : '' }}">
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