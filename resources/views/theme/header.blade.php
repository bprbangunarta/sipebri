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
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('jquery')
    <!-- Jquery -->
    <script src="https://code.jquery.com/jquery-3.7.0.js" integrity="sha256-JlqSTELeR4TLqP0OG9dxM7yDPqX1ox/HfgiSLBj8+kM="
        crossorigin="anonymous"></script>
@endsection

<link rel="stylesheet" href="{{ asset('theme/assets/bootstrap/dist/css/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('theme/assets/font-awesome/css/font-awesome.min.css') }}">
<link rel="stylesheet" href="{{ asset('theme/assets/Ionicons/css/ionicons.min.css') }}">
<link rel="stylesheet" href="{{ asset('theme/assets/bootstrap-daterangepicker/daterangepicker.cs') }}s">
<link rel="stylesheet" href="{{ asset('theme/assets/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('theme/plugins/iCheck/all.css') }}">
<link rel="stylesheet" href="{{ asset('theme/assets/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('theme/plugins/timepicker/bootstrap-timepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('theme/assets/select2/dist/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('theme/dist/css/AdminLTE.min.css') }}">
<link rel="stylesheet" href="{{ asset('theme/dist/css/skins/_all-skins.min.css') }}">
<link rel="stylesheet" href="{{ asset('theme/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css') }}">

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

    .text-middle {
        vertical-align: middle;
    }
</style>
