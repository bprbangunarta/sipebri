<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>@yield('title')</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">

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

  <link href="tabler/dist/css/tabler.min.css?1674944402" rel="stylesheet" />
  <link href="tabler/dist/css/tabler-flags.min.css?1674944402" rel="stylesheet" />
  <link href="tabler/dist/css/tabler-payments.min.css?1674944402" rel="stylesheet" />
  <link href="tabler/dist/css/tabler-vendors.min.css?1674944402" rel="stylesheet" />
  <link href="tabler/dist/css/demo.min.css?1674944402" rel="stylesheet" />
  <style>
    @import url('https://rsms.me/inter/inter.css');

    :root {
      --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
    }

    body {
      font-feature-settings: "cv03", "cv04", "cv11";
    }
  </style>
</head>

<body class="d-flex flex-column @yield('bg')">
  <script src="tabler/dist/js/demo-theme.min.js?1674944402"></script>

  @yield('content')

  <script src="tabler/dist/js/tabler.min.js?1674944402" defer></script>
  <script src="tabler/dist/js/demo.min.js?1674944402" defer></script>
</body>

</html>