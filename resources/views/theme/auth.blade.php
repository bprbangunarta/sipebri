<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
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
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="{{ asset('theme/assets/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/assets/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/assets/Ionicons/css/ionicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/dist/css/AdminLTE.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/plugins/iCheck/square/blue.css') }}">

    <!-- Google Font -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-logo">
            <a href="/"><b>SIPEBRI</b></a>
        </div>

        @yield('content')

    </div>

    <script src="{{ asset('theme/assets/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('theme/assets/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('theme/plugins/iCheck/icheck.min.js') }}"></script>
</body>

</html>
