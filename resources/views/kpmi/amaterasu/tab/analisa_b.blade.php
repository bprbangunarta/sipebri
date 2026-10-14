{{-- Langkah B. Syarat dan kesepakatan. Variabel: $syaratWajib, $syaratPendukung, $isian. --}}
{{-- LANGKAH B --}}
<div class="box box-primary">
   <div class="box-header with-border">
      <h3 class="box-title"><span class="kpmi-step">B</span> Syarat dan kesepakatan</h3>
   </div>

   <div class="box-body no-padding">
      <p class="kpmi-hint" style="padding: 10px 10px 0;">
         Jawab <b>Ya</b> hanya jika sudah terpenuhi. Jawaban <b>Tidak</b> pada syarat wajib
         membuat keputusan <b>TUNDA</b>.
      </p>

      <table class="table table-condensed" style="margin-bottom: 0;">
         <tbody>
            <tr class="active">
               <td colspan="2">
                  <b>Syarat wajib</b> <span class="label label-danger">harus Ya</span>
               </td>
            </tr>
            @foreach ($syaratWajib as $s)
               <tr class="kpmi-row">
                  <td style="padding-left: 15px;">{{ $s['label'] }}</td>
                  <td style="width: 140px;" class="text-right">
                     <div class="btn-group btn-group-sm kpmi-opt">
                        <label class="btn btn-default" data-on="btn-success"><input type="radio" name="{{ $s['name'] }}" value="Ya" class="kpmi-isian-radio" data-wajib="1" @checked(($isian[$s['name']] ?? null) === 'Ya')>Ya</label>
                        <label class="btn btn-default" data-on="btn-danger"><input type="radio" name="{{ $s['name'] }}" value="Tidak" class="kpmi-isian-radio" data-wajib="1" @checked(($isian[$s['name']] ?? null) === 'Tidak')>Tidak</label>
                     </div>
                  </td>
               </tr>
            @endforeach

            <tr class="active">
               <td colspan="2"><b>Kesepakatan pendukung</b></td>
            </tr>
            @foreach ($syaratPendukung as $s)
               <tr class="kpmi-row">
                  <td style="padding-left: 15px;">{{ $s['label'] }}</td>
                  <td style="width: 140px;" class="text-right">
                     <div class="btn-group btn-group-sm kpmi-opt">
                        <label class="btn btn-default" data-on="btn-success"><input type="radio" name="{{ $s['name'] }}" value="Ya" class="kpmi-isian-radio" data-wajib="0" @checked(($isian[$s['name']] ?? null) === 'Ya')>Ya</label>
                        <label class="btn btn-default" data-on="btn-danger"><input type="radio" name="{{ $s['name'] }}" value="Tidak" class="kpmi-isian-radio" data-wajib="0" @checked(($isian[$s['name']] ?? null) === 'Tidak')>Tidak</label>
                     </div>
                  </td>
               </tr>
            @endforeach
         </tbody>
      </table>
   </div>
</div>
