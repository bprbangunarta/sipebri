{{-- Tab 4. Biaya Administrasi. Variabel: $administrasi, $awalTahap, $administrasiTersimpan, $kodeRoute. Kolom lewat _kolom.blade.php. --}}
<p class="kpmi-hint" style="margin-bottom: 10px;">
   @if ($administrasiTersimpan)
      <i class="fa fa-check-circle text-green"></i>
      Tersimpan {{ ($administrasiTersimpan->updated_at ?? $administrasiTersimpan->created_at)?->format('d-m-Y H:i') }}
      oleh {{ $administrasiTersimpan->input_user }} ({{ $administrasiTersimpan->kode_analisa }}).
   @else
      <i class="fa fa-info-circle"></i> Belum disimpan.
      Administrasi dan provisi terisi awal dari plafon dan persen pengajuan; semua kolom bisa diubah.
   @endif
</p>

<form method="POST" action="{{ route('kpmi.amaterasi.administrasi.simpan', $kodeRoute) }}" autocomplete="off">
   @csrf

   <div class="nav-tabs-custom kpmi-subtab kpmi-padat">
      <ul class="nav nav-tabs">
         <li class="active"><a href="#ad-biaya" data-subtab>Biaya Kredit</a></li>
      </ul>

      <div class="tab-content">
         <div class="tab-pane active" id="ad-biaya">
            <div class="row">
               @foreach ($administrasi['biaya'] as $f)
                  <div class="col-sm-6 col-md-3">
                     @include('kpmi.amaterasu._kolom', ['awal' => $awalTahap, 'f' => $f + ['tipe' => 'rupiah']])
                  </div>
               @endforeach
            </div>

            <button type="submit" class="btn btn-primary btn-block">SIMPAN</button>
         </div>
      </div>
   </div>
</form>
