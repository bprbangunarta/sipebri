<!doctype html>
<html lang="en">

<head>
    <title>@yield('title')</title>
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">

    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="description"
        content="Sistem pemberian kredit adalah mekanisme untuk menilai potensi risiko dan kemampuan seseorang untuk membayar kembali pinjaman">
    <meta name="keywords" content="BPR Bangunarta, bprbangunarta" />

    <meta content='Sistem Pemberian Kredit' property='og:title' />
    <meta content='https://sipebri.bprbangunarta.co.id/' property='og:url' />
    <meta content='Sistem Pemberian Kredit' property='og:site_name' />
    <meta content='website' property='og:type' />
    <meta
        content='Sistem pemberian kredit adalah mekanisme untuk menilai potensi risiko dan kemampuan seseorang untuk membayar kembali pinjaman'
        property='og:description' />
    <meta content='Sistem Pemberian Kredit' property='og:image:alt' />
    <meta content='https://sipebri.bprbangunarta.co.id/assets/img/banner.png' property='og:image' />

    <!-- Jquery -->
    <script src="https://code.jquery.com/jquery-3.7.0.js" integrity="sha256-JlqSTELeR4TLqP0OG9dxM7yDPqX1ox/HfgiSLBj8+kM="
        crossorigin="anonymous"></script>
    @yield('scr')
    <!-- Jquery -->

    <!-- CSS files -->
    <link href="{{ asset('tabler/dist/css/tabler.min.css?1674944402') }}" rel="stylesheet" />
    <link href="{{ asset('tabler/dist/css/tabler-flags.min.css?1674944402') }}" rel="stylesheet" />
    <link href="{{ asset('tabler/dist/css/tabler-payments.min.css?1674944402') }}" rel="stylesheet" />
    <link href="{{ asset('tabler/dist/css/tabler-vendors.min.css?1674944402') }}" rel="stylesheet" />
    <link href="{{ asset('tabler/dist/css/demo.min.css?1674944402') }}" rel="stylesheet" />

    <style>
        @import url('https://rsms.me/inter/inter.css');

        :root {
            --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
        }

        body {
            font-feature-settings: "cv03", "cv04", "cv11";
        }
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body class="layout-fluid">
    <script src="{{ asset('tabler/dist/js/demo-theme.min.js?1674944402') }}"></script>

    @include('sweetalert::alert')

    <div class="page">

        <!-- Navbar -->
        <div class="sticky-top">
            @include('templates.header')

            <x-layouts.navigation></x-layouts.navigation>
        </div>
        <div class="page-wrapper">

            <!-- Page body -->
            @yield('content')

            @include('templates.footer')

            @stack('myscript')

</body>

</html>
