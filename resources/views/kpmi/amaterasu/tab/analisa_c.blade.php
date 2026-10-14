{{-- Langkah C. Penilaian 0-5. Variabel: $penilaian, $aturan, $isian. --}}
{{-- LANGKAH C --}}
<div class="box box-primary">
   <div class="box-header with-border">
      <h3 class="box-title"><span class="kpmi-step">C</span> Penilaian</h3>
   </div>

   <div class="box-body no-padding">
      <p class="kpmi-hint" style="padding: 10px 10px 0;">
         Beri nilai <b>0 sampai {{ $aturan['nilai_maks'] }}</b>.
         {!! collect($aturan['skala'])->map(fn($s) => '<b>' . e($s[0]) . '</b> = ' . e($s[1]))->implode(' &middot; ') !!}.
         Angka <i>bobot</i> menunjukkan seberapa besar pengaruh kriteria itu.
      </p>

      <table class="table table-condensed" style="margin-bottom: 0;">
         <tbody>
            @foreach ($penilaian as $kelompok => $items)
               <tr class="active">
                  <td colspan="2"><b>{{ $kelompok }}</b></td>
                  <td class="text-center" style="width: 60px;"><small>Bobot</small></td>
               </tr>
               @foreach ($items as $item)
                  <tr class="kpmi-row">
                     <td style="padding-left: 15px;">{{ $item['label'] }}</td>
                     <td style="width: 250px;" class="text-right">
                        <div class="btn-group btn-group-sm kpmi-opt">
                           @for ($n = 0; $n <= $aturan['nilai_maks']; $n++)
                              <label class="btn btn-default" data-on="btn-primary"><input type="radio" name="{{ $item['name'] }}" value="{{ $n }}" class="kpmi-isian-radio" data-bobot="{{ $item['bobot'] }}" @checked(($isian[$item['name']] ?? null) === $n)>{{ $n }}</label>
                           @endfor
                        </div>
                     </td>
                     <td class="text-center text-muted">{{ $item['bobot'] }}</td>
                  </tr>
               @endforeach
            @endforeach
         </tbody>
      </table>
   </div>
</div>
