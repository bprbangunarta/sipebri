{{-- Tab 2. Analisa Dokumen: unggah tujuh dokumen pendukung. Variabel: $dokumen, $dokumenTersimpan, $kodeRoute. --}}
{{-- DOKUMEN PENDUKUNG --}}
<div class="box box-primary" id="box-dokumen"
   data-jenis="{{ json_encode($dokumen['jenis']) }}" data-maks-mb="{{ $dokumen['maks_mb'] }}"
   data-tersimpan="{{ json_encode($dokumenTersimpan, JSON_FORCE_OBJECT) }}"
   data-url-dokumen="{{ route('kpmi.amaterasi.dokumen.simpan', [$kodeRoute, 'JENIS']) }}">
   <div class="box-header with-border">
      <h3 class="box-title">Dokumen pendukung</h3>
      <span class="label label-default pull-right" id="dokumen-jumlah" style="margin-top: 6px;">
         0 / {{ count($dokumen['daftar']) }} terunggah
      </span>
   </div>

   <div class="box-body no-padding">
      <p class="kpmi-hint" style="padding: 10px 10px 0;">
         Unggah dokumen yang diserahkan LPK. Format
         <b>{{ strtoupper(implode(', ', $dokumen['jenis'])) }}</b>,
         maksimal <b>{{ $dokumen['maks_mb'] }} MB</b> per file.
      </p>

      <table class="table table-condensed kpmi-dokumen" style="margin-bottom: 0;">
         <thead>
            <tr>
               <th style="padding-left: 15px; width: 32%;">Dokumen</th>
               <th>File</th>
               <th class="text-right" style="width: 235px; padding-right: 10px;">Aksi</th>
            </tr>
         </thead>
         <tbody>
            @foreach ($dokumen['daftar'] as $dok)
               <tr class="kpmi-row" data-dokumen="{{ $dok['name'] }}">
                  <td style="padding-left: 15px;">{{ $loop->iteration }}. {{ $dok['label'] }}</td>
                  <td>
                     <span class="dokumen-status text-muted">Belum diunggah</span>
                     <div class="dokumen-galat text-red" style="display: none;"></div>
                  </td>
                  <td class="dokumen-aksi" style="padding-right: 10px;">
                     <button type="button" class="btn btn-default btn-sm dokumen-lihat" style="display: none;" title="Lihat isi file">
                        <i class="fa fa-eye"></i> Lihat
                     </button>
                     <label class="btn btn-default btn-sm">
                        <i class="fa fa-upload"></i> <span class="dokumen-tombol">Pilih file</span>
                        <input type="file" class="kpmi-file" accept="{{ '.' . implode(',.', $dokumen['jenis']) }}" hidden>
                     </label>
                     <button type="button" class="btn btn-default btn-sm dokumen-hapus" style="display: none;" title="Hapus file">
                        <i class="fa fa-times text-red"></i>
                     </button>
                  </td>
               </tr>
            @endforeach
         </tbody>
      </table>
   </div>
</div>
