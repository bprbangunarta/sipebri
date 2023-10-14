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

    <!-- Jquery -->
    <script src="https://code.jquery.com/jquery-3.7.0.js" integrity="sha256-JlqSTELeR4TLqP0OG9dxM7yDPqX1ox/HfgiSLBj8+kM="
        crossorigin="anonymous"></script>
    @yield('scr')

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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="hold-transition skin-blue fixed sidebar-mini">
    <div class="wrapper">
        @include('sweetalert::alert')

        @include('theme.header')

        <x-navigation></x-navigation>

        @yield('content')

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
