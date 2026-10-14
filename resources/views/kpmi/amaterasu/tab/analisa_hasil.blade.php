{{-- Panel hasil analisa di kanan (menempel saat scroll), tombol kembali dan simpan. Variabel: $analisa, $totalWajib, $totalBobot, $aturan, $totalIsian, $kodeRoute. --}}
{{-- ===================== KOLOM KANAN: HASIL (menempel saat scroll) ===================== --}}
<div class="col-lg-4">
   <div class="kpmi-aside">
      <div class="box box-success">
         <div class="box-header with-border">
            <h3 class="box-title">Hasil Analisa</h3>
            <div class="box-tools pull-right">
               @if ($analisa)
                  <a href="{{ route('kpmi.amaterasi.cetak', $kodeRoute) }}" target="_blank" class="btn btn-box-tool"
                     title="Cetak analisa yang sudah tersimpan" style="font-size: 16px;">
                     <i class="fa fa-print"></i>
                  </a>
               @else
                  <span title="Simpan analisa dulu agar bisa dicetak" style="cursor: not-allowed;">
                     <a class="btn btn-box-tool disabled" style="font-size: 16px; opacity: .35;">
                        <i class="fa fa-print"></i>
                     </a>
                  </span>
               @endif
            </div>
         </div>

         <div class="box-body">
            <div style="margin-bottom: 12px;">
               <small class="text-muted">
                  Kelengkapan isian:
                  <b id="progres-teks">0 / {{ $totalIsian }}</b>
               </small>
               <div class="progress progress-xs" style="margin: 4px 0 0;">
                  <div id="progres-bar" class="progress-bar progress-bar-success" style="width: 0%;"></div>
               </div>
            </div>

            <div id="hasil-rekomendasi-box" class="kpmi-hasil">
               <small>REKOMENDASI</small>
               <h4 id="hasil-rekomendasi">Belum ada hasil</h4>
               <small id="hasil-catatan"></small>
            </div>

            <div class="row text-center" style="margin-bottom: 10px;">
               <div class="col-xs-6">
                  <small class="text-muted">SKOR (dari {{ $totalBobot }})</small>
                  <h2 id="hasil-skor" style="margin: 2px 0;">-</h2>
               </div>
               <div class="col-xs-6">
                  <small class="text-muted">SYARAT WAJIB TERPENUHI</small>
                  <h2 id="hasil-gate" style="margin: 2px 0;">- <small>/ {{ $totalWajib }}</small></h2>
               </div>
            </div>

            <div id="hasil-daftar-gagal" style="display: none;">
               <small class="text-muted">Yang harus dipenuhi:</small>
               <ul id="hasil-daftar-gagal-isi" class="list-unstyled" style="margin: 4px 0 0;"></ul>
            </div>

            <hr style="margin: 12px 0;">

            <small class="text-muted">
               <b>Cara membaca:</b><br>
               Ada syarat wajib belum terpenuhi &rarr; <b>TUNDA</b>.<br>
               Semua lolos &rarr; skor &ge;{{ $aturan['skor_layak'] }} <b>Layak</b>, {{ $aturan['skor_komite'] }}-{{ number_format($aturan['skor_layak'] - 0.1, 1, ',', '.') }} <b>Komite</b>, &lt;{{ $aturan['skor_komite'] }} <b>Tidak direkomendasikan</b>.
            </small>
         </div>

         <div class="box-footer">
            <a href="{{ route('permohonan.analisa') }}" class="btn btn-sm btn-default">
               KEMBALI
            </a>

            <button type="submit" class="btn btn-sm btn-primary pull-right">
               SIMPAN
            </button>
         </div>
      </div>
   </div>
</div>
