<!doctype html>
<html lang="en">

<head>
  <title>@yield('title')</title>
  <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">

  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <meta name="description" content="Sistem Informasi Pemberian Kredit">
  <meta name="keywords" content="BPR Bangunarta, bprbangunarta" />

  <meta content='SIPEBRI' property='og:title' />
  <meta content='https://sipebri.bprbangunarta.co.id/' property='og:url' />
  <meta content='Aplikasi Presensi' property='og:site_name' />
  <meta content='website' property='og:type' />
  <meta content='Sistem Informasi Pemberian Kredit' property='og:description' />
  <meta content='SIPEBRI' property='og:image:alt' />
  <meta content='https://sipebri.bprbangunarta.co.id/assets/img/banner.png' property='og:image' />

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

<body>
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