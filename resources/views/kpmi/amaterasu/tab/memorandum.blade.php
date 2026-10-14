{{-- Tab 3. Memorandum: kebutuhan dana, usulan kredit, sandi lapbul. Variabel: $memorandum, $awalTahap, $kebutuhanTersimpan, $memorandumTersimpan, $kodeRoute. Kolom lewat _kolom.blade.php. --}}
@php
   $waktu = fn($m) => $m ? ($m->updated_at ?? $m->created_at)?->format('d-m-Y H:i') . ' oleh ' . $m->input_user : null;
@endphp

<p class="kpmi-hint" style="margin-bottom: 10px;">
   <i class="fa fa-info-circle"></i> Tiap sub-tab disimpan sendiri. Simpan <b>Usulan Kredit</b> lebih dulu sebelum <b>Sandi Lapbul</b>.
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
         <form method="POST" action="{{ route('kpmi.amaterasi.memorandum.kebutuhan', $kodeRoute) }}" autocomplete="off">
            @csrf
            @if ($kebutuhanTersimpan)
               <p class="kpmi-hint" style="margin: 0 0 8px;"><i class="fa fa-check-circle text-green"></i> Tersimpan {{ $waktu($kebutuhanTersimpan) }}.</p>
            @endif

            @foreach ($memorandum['kebutuhan'] as $k)
               <div class="row">
                  <div class="col-md-6">
                     @include('kpmi.amaterasu._kolom', ['awal' => $awalTahap, 'f' => ['name' => $k['name'], 'label' => $k['label'], 'tipe' => 'rupiah', 'kelas' => 'kpmi-kebutuhan']])
                  </div>
                  <div class="col-md-6">
                     @include('kpmi.amaterasu._kolom', ['awal' => $awalTahap, 'f' => ['name' => 'ket_' . $k['name'], 'label' => 'Keterangan', 'tipe' => 'teks', 'kelas' => 'text-uppercase']])
                  </div>
               </div>
            @endforeach

            <div class="row">
               <div class="col-md-6">
                  @include('kpmi.amaterasu._kolom', ['awal' => $awalTahap, 'f' => ['name' => 'jumlah_kebutuhan_dana', 'label' => 'Jumlah Kebutuhan Dana', 'tipe' => 'rupiah', 'readonly' => true, 'kelas' => 'kpmi-total']])
               </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block">SIMPAN</button>
         </form>
      </div>

      {{-- USULAN KREDIT --}}
      <div class="tab-pane" id="mm-usulan">
         <form method="POST" action="{{ route('kpmi.amaterasi.memorandum.usulan', $kodeRoute) }}" autocomplete="off">
            @csrf
            @if ($memorandumTersimpan)
               <p class="kpmi-hint" style="margin: 0 0 8px;"><i class="fa fa-check-circle text-green"></i> Tersimpan {{ $waktu($memorandumTersimpan) }}.</p>
            @endif

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

            <button type="submit" class="btn btn-primary btn-block">SIMPAN</button>
            <p class="text-red" style="margin: 12px 0 0;">*Pengisian Sektor Ekonomi ada di tab <b>Sandi Lapbul</b>.</p>
            <p class="text-muted" style="margin: 4px 0 0; font-size: 12px;">
               Menyimpan usulan juga mengubah plafon, jangka waktu, bunga, dan biaya pada data pengajuan, seperti pada alur lama.
            </p>
         </form>
      </div>

      {{-- SANDI LAPBUL --}}
      <div class="tab-pane" id="mm-sandi">
         <form method="POST" action="{{ route('kpmi.amaterasi.memorandum.sandi', $kodeRoute) }}" autocomplete="off">
            @csrf
            @if ($statusMemorandum['sandi'])
               <p class="kpmi-hint" style="margin: 0 0 8px;"><i class="fa fa-check-circle text-green"></i> Tersimpan {{ $waktu($memorandumTersimpan) }}.</p>
            @elseif (! $memorandumTersimpan)
               <p class="text-yellow" style="margin: 0 0 8px;"><i class="fa fa-exclamation-circle"></i> Simpan usulan kredit terlebih dahulu.</p>
            @endif

            <div class="row">
               @foreach ($memorandum['sandi'] as $f)
                  <div class="col-sm-6 col-md-4">
                     @include('kpmi.amaterasu._kolom', ['awal' => $awalTahap, 'f' => $f])
                  </div>
               @endforeach
            </div>

            <button type="submit" class="btn btn-primary btn-block">SIMPAN</button>
         </form>
      </div>
   </div>
</div>
