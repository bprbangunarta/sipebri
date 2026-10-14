{{-- Tab 4. Biaya Administrasi. Variabel: $administrasi, $awalTahap. Kolom lewat _kolom.blade.php. --}}
<p class="kpmi-hint" style="margin-bottom: 10px;">
   <i class="fa fa-info-circle"></i> Isian tahap ini belum tersambung ke penyimpanan.
   Administrasi dan provisi terisi awal dari plafon dan persen pengajuan; semua kolom bisa diubah.
</p>

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

         <button type="button" class="btn btn-primary btn-block disabled" style="cursor: not-allowed;" title="Penyimpanan belum tersambung">SIMPAN</button>
      </div>
   </div>
</div>
