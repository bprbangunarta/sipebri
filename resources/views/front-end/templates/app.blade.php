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
              <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
                <i class="fa fa-bars"></i>
              </button>
            </div>
    
            <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
              <ul class="nav navbar-nav">
                <li class="{{ request()->is('pengajuan/kredit') ? 'active' : '' }}">
                    <a href="/pengajuan/kredit">Pengajuan Kredit</a>
                </li>

                <li class="{{ request()->is('pengajuan/tracking') ? 'active' : '' }}">
                    <a href="/pengajuan/tracking">Tracking Pengajuan</a>
                </li>

                <li class="{{ request()->is('verifikasi') ? 'active' : '' }}">
                  <a href="/verifikasi">Verifikasi</a>
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

    @yield('content')

    <footer class="main-footer">
        <div class="container">
          <div class="pull-right hidden-xs">
            <b>VERSI</b> 1.1.0
          </div>
          <strong>SISTEM PEMBERIAN KREDI</strong>
        </div>
      </footer>
    @include('theme.footer')
</body>
</html>
