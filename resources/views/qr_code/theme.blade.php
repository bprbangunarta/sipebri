<!DOCTYPE html>
<html>

<head>
    @include('theme.header')
</head>

<body class="hold-transition skin-blue">

    <body class="hold-transition">
        <div class="wrapper">

            {{-- @include('theme.topbar') --}}

            {{-- <x-navigation></x-navigation> --}}

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
    </body>

</html>
