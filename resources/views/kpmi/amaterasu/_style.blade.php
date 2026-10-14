{{-- Gaya halaman analisa KPMI Amaterasu (tab, kolom padat, hasil, dokumen). --}}
<style>
   .kpmi-aside { position: sticky; top: 65px; }
   @media (max-width: 991px) { .kpmi-aside { position: static; } }
   .kpmi-step { display: inline-block; width: 24px; height: 24px; line-height: 24px; border-radius: 50%;
      background: #3c8dbc; color: #fff; text-align: center; font-size: 13px; margin-right: 6px; }
   .kpmi-hint { color: #777; margin: 0 0 12px; }
   .kpmi-opt .btn { min-width: 38px; }
   .kpmi-opt .btn { position: relative; }
   .kpmi-opt .btn input { position: absolute; clip: rect(0, 0, 0, 0); pointer-events: none; }
   .kpmi-opt .btn:focus-within { outline: 2px solid #3c8dbc; outline-offset: -2px; z-index: 2; }
   .kpmi-row td { vertical-align: middle !important; }
   .kpmi-file { display: none !important; }
   .kpmi-dokumen { table-layout: fixed; }
   .kpmi-dokumen th { font-weight: 600; font-size: 12px; color: #555; background: #f6f6f6; }
   .dokumen-status { display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
   .dokumen-aksi { white-space: nowrap; text-align: right; }
   .dokumen-aksi .btn { margin: 0 0 0 4px; }
   .kpmi-petunjuk { background: #f4f8fb; border: 1px solid #d6e4ef; border-left: 4px solid #3c8dbc;
      color: #333; padding: 10px 15px; margin-bottom: 15px; border-radius: 3px; }
   .kpmi-petunjuk .kpmi-garis { display: inline-block; width: 4px; height: 14px; vertical-align: -2px; margin: 0 4px 0 2px; }
   .kpmi-hasil { border: 1px solid #ddd; border-left: 5px solid #999; background: #f7f7f7; color: #333;
      padding: 10px 15px; margin-bottom: 12px; text-align: center; border-radius: 3px; }
   .kpmi-hasil small { color: #555; display: block; }
   .kpmi-hasil h4 { margin: 4px 0 2px; font-weight: 600; }
   .kpmi-hasil.hasil-success { background: #eaf6ee; border-color: #b7dfc4; border-left-color: #00a65a; }
   .kpmi-hasil.hasil-success h4 { color: #0b6b3a; }
   .kpmi-hasil.hasil-warning { background: #fff6e0; border-color: #f0dca6; border-left-color: #f39c12; }
   .kpmi-hasil.hasil-warning h4 { color: #8a5a00; }
   .kpmi-hasil.hasil-danger { background: #fdeeec; border-color: #f0c4be; border-left-color: #dd4b39; }
   .kpmi-hasil.hasil-danger h4 { color: #a52a1c; }
   .kpmi-tahap { box-shadow: 0 1px 1px rgba(0,0,0,.1); margin-bottom: 20px; }
   .kpmi-tahap > .nav-tabs { display: flex; flex-wrap: nowrap; overflow-x: auto; overflow-y: hidden; }
   .kpmi-tahap > .nav-tabs > li { float: none; flex: 0 0 auto; }
   .kpmi-tahap > .nav-tabs > li > a { color: #444; font-weight: 600; padding: 12px 14px; white-space: nowrap; }
   @media (max-width: 1199px) { .kpmi-tahap > .nav-tabs > li > a { padding: 10px 10px; font-size: 13px; } }
   .kpmi-tahap > .nav-tabs > li.kpmi-segera > a { color: #999; font-weight: normal; }
   .kpmi-tahap > .nav-tabs > li > a .label { margin-left: 4px; vertical-align: 1px; }
   .kpmi-tahap > .tab-content { padding: 15px; background: #f9fafc; }
   .kpmi-tahap > .tab-content > .tab-pane > .box { box-shadow: none; border: 1px solid #e6e9ed; border-top: 3px solid #3c8dbc; }
   .kpmi-subtab { box-shadow: none; border: 1px solid #e6e9ed; background: #fff; margin-bottom: 0; }
   .kpmi-subtab > .nav-tabs > li > a { font-weight: 600; text-transform: uppercase; font-size: 12px; color: #555; }
   .kpmi-subtab > .nav-tabs > li.active > a { color: #222; }
   .kpmi-subtab > .tab-content { padding: 15px; }
   .kpmi-grup { font-size: 11px; font-weight: 700; letter-spacing: .5px; text-transform: uppercase; color: #777;
      border-bottom: 1px solid #eee; margin: 4px 0 12px; padding-bottom: 4px; }
   .kpmi-total { background: #0073b7 !important; color: #fff; font-weight: 600; }
   .kpmi-resume { background: #fff; border: 1px solid #e6e9ed; margin-bottom: 0; }
   .kpmi-resume th { font-size: 12px; color: #555; background: #f6f6f6; }
   .kpmi-resume td { vertical-align: middle !important; padding: 10px 8px !important; }
   .kpmi-kirim { text-align: right; padding: 15px 0 0; }
   /* kolom isian rapat: dipakai tab 1, 3, dan 4 */
   .kpmi-subtab > .tab-content { padding: 12px; }
   .kpmi-padat .row { margin-left: -5px; margin-right: -5px; }
   .kpmi-padat [class*="col-"] { padding-left: 5px; padding-right: 5px; }
   .kpmi-padat .form-group { margin-bottom: 8px; }
   .kpmi-padat label { display: block; margin-bottom: 2px; font-size: 12px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
   .kpmi-padat .form-control, .kpmi-padat .input-group-addon { height: 30px; padding: 4px 8px; font-size: 13px; }
   .kpmi-padat textarea.form-control { height: auto; }
   .kpmi-padat .kpmi-grup { margin: 2px 0 8px; }
   .kpmi-padat .btn-block { margin-top: 6px; }
   .kpmi-padat .help-block { margin: 2px 0 0; font-size: 11px; }
   .kpmi-row.terisi td:first-child { border-left: 3px solid #00a65a; }
   .kpmi-row:not(.terisi) td:first-child { border-left: 3px solid #f39c12; }
</style>
