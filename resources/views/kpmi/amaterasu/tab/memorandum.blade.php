{{-- Tab 3. Memorandum: kebutuhan dana, usulan kredit, sandi lapbul. Variabel: $memorandum, $awalTahap. Kolom lewat _kolom.blade.php. --}}
<p class="kpmi-hint" style="margin-bottom: 10px;">
   <i class="fa fa-info-circle"></i> Isian tahap ini belum tersambung ke penyimpanan.
</p>

<div class="nav-tabs-custom kpmi-subtab kpmi-padat">
   <ul class="nav nav-tabs">
      <li class="active"><a href="#mm-kebutuhan" data-subtab>Kebutuhan Dana</a></li>
      <li><a href="#mm-usulan" data-subtab>Usulan Kredit</a></li>
      <li><a href="#mm-sandi" data-subtab>Sandi Lapbul</a></li>
   </ul>

   <div class="tab-content">
      {{-- KEBUTUHAN DANA --}}
      <div class="tab-pane active" id="mm-kebutuhan">
         @foreach ($memorandum['kebutuhan'] as $k)
            <div class="row">
               <div class="col-md-6">
                  @include('kpmi.amaterasu._kolom', ['awal' => $awalTahap, 'f' => ['name' => $k['name'], 'label' => $k['label'], 'tipe' => 'rupiah', 'kelas' => 'kpmi-kebutuhan']])
               </div>
               <div class="col-md-6">
                  @include('kpmi.amaterasu._kolom', ['awal' => $awalTahap, 'f' => ['name' => 'keterangan_' . $k['name'], 'label' => 'Keterangan', 'tipe' => 'teks']])
               </div>
            </div>
         @endforeach

         <div class="row">
            <div class="col-md-6">
               @include('kpmi.amaterasu._kolom', ['awal' => $awalTahap, 'f' => ['name' => 'jumlah_kebutuhan_dana', 'label' => 'Jumlah Kebutuhan Dana', 'tipe' => 'rupiah', 'readonly' => true, 'kelas' => 'kpmi-total']])
            </div>
         </div>

         <button type="button" class="btn btn-primary btn-block disabled" style="cursor: not-allowed;" title="Penyimpanan belum tersambung">SIMPAN</button>
      </div>

      {{-- USULAN KREDIT --}}
      <div class="tab-pane" id="mm-usulan">
         @foreach ($memorandum['usulan'] as $judulGrup => $kolom)
            <div class="kpmi-grup">{{ $judulGrup }}</div>
            <div class="row">
               @foreach ($kolom as $f)
                  <div class="col-sm-6 col-md-4">
                     @include('kpmi.amaterasu._kolom', ['awal' => $awalTahap, 'f' => $f])
                  </div>
               @endforeach
            </div>
         @endforeach

         <button type="button" class="btn btn-primary btn-block disabled" style="cursor: not-allowed;" title="Penyimpanan belum tersambung">SIMPAN</button>
         <p class="text-red" style="margin: 12px 0 0;">*Pengisian Sektor Ekonomi ada di tab <b>Sandi Lapbul</b>.</p>
      </div>

      {{-- SANDI LAPBUL --}}
      <div class="tab-pane" id="mm-sandi">
         <div class="row">
            @foreach ($memorandum['sandi'] as $f)
               <div class="col-sm-6 col-md-4">
                  @include('kpmi.amaterasu._kolom', ['awal' => $awalTahap, 'f' => $f])
               </div>
            @endforeach
         </div>

         <button type="button" class="btn btn-primary btn-block disabled" style="cursor: not-allowed;" title="Penyimpanan belum tersambung">SIMPAN</button>
      </div>
   </div>
</div>
