{{-- Langkah A. Kemampuan bayar keluarga. Variabel: $pengajuan, $isian, $angsuran, $aturan. --}}
{{-- LANGKAH A --}}
<div class="box box-primary">
   <div class="box-header with-border">
      <h3 class="box-title"><span class="kpmi-step">A</span> Kemampuan bayar keluarga</h3>
   </div>

   <div class="box-body kpmi-padat">
      <p class="kpmi-hint">
         Isi angka <b>per bulan</b> yang sudah terverifikasi. Jangan memasukkan proyeksi gaji di Jepang.
      </p>

      <div class="row">
         <div class="col-sm-6">
            <div class="form-group">
               <label>Penghasilan bersih</label>
               <div class="input-group">
                  <span class="input-group-addon">Rp</span>
                  <input type="text" inputmode="numeric" name="penghasilan" class="form-control kpmi-rupiah kpmi-isian" placeholder="0" value="{{ $isian['penghasilan'] ?? '' }}">
               </div>
               <p class="help-block">Rata-rata 6-12 bulan</p>
            </div>
         </div>
         <div class="col-sm-6">
            <div class="form-group">
               <label>Biaya hidup rumah tangga</label>
               <div class="input-group">
                  <span class="input-group-addon">Rp</span>
                  <input type="text" inputmode="numeric" name="biaya_hidup" class="form-control kpmi-rupiah kpmi-isian" placeholder="0" value="{{ $isian['biaya_hidup'] ?? '' }}">
               </div>
               <p class="help-block">Termasuk tanggungan dan kebutuhan dasar</p>
            </div>
         </div>
      </div>

      <div class="row">
         <div class="col-sm-6">
            <div class="form-group">
               <label>Angsuran kredit berjalan</label>
               <div class="input-group">
                  <span class="input-group-addon">Rp</span>
                  <input type="text" inputmode="numeric" name="angsuran_berjalan" class="form-control kpmi-rupiah kpmi-isian" placeholder="0" value="{{ $isian['angsuran_berjalan'] ?? '' }}">
               </div>
               <p class="help-block">Isi 0 jika tidak ada. Cocokkan dengan SLIK</p>
            </div>
         </div>
         <div class="col-sm-6">
            <div class="form-group">
               <label>Angsuran kredit ini <small class="text-muted">(per bulan)</small></label>
               <div class="input-group">
                  <span class="input-group-addon">Rp</span>
                  <input type="text" id="angsuran-kredit" class="form-control" value="{{ number_format($angsuran, 0, ',', '.') }}" readonly>
               </div>
               <p class="help-block">
                  Otomatis dari plafon Rp {{ number_format($pengajuan->plafon, 0, ',', '.') }},
                  bunga {{ $pengajuan->suku_bunga }}%, {{ $pengajuan->jangka_waktu }} bulan
               </p>
            </div>
         </div>
      </div>

      <table class="table table-bordered table-condensed" style="margin: 5px 0 0;">
         <thead>
            <tr class="bg-gray">
               <th>Hasil hitung otomatis</th>
               <th class="text-right" style="width: 150px;">Nilai</th>
               <th class="text-center" style="width: 90px;">Skala 0-5</th>
            </tr>
         </thead>
         <tbody>
            <tr>
               <td>DSR setelah kredit <small class="text-muted">(angsuran &divide; penghasilan)</small></td>
               <td class="text-right" id="rc-dsr">-</td>
               <td class="text-center" id="rc-nilai-dsr">-</td>
            </tr>
            <tr>
               <td>Sisa penghasilan <small class="text-muted">(setelah biaya hidup dan angsuran)</small></td>
               <td class="text-right" id="rc-residual">-</td>
               <td class="text-center">&nbsp;</td>
            </tr>
            <tr>
               <td>
                  Sisa penghasilan saat keadaan memburuk
                  <small class="text-muted">(penghasilan -{{ round((1 - $aturan['stress_penghasilan']) * 100) }}%, biaya hidup +{{ round(($aturan['stress_biaya'] - 1) * 100) }}%)</small>
               </td>
               <td class="text-right" id="rc-stress-residual">-</td>
               <td class="text-center" id="rc-nilai-stress">-</td>
            </tr>
         </tbody>
      </table>
   </div>
</div>
