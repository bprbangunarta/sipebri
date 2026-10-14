{{-- Isi halaman "Analisa Kredit KPMI" pada cetak analisa kredit alur lama (cetak-analisa.blade.php).
     Variabel dari AmaterasuController::dataCetakAnalisa: $pengajuan, $analisa, $detail, $kelompok, $label, $aturan,
     $gateMinimal, $totalBobot, $belumTerpenuhi. --}}
@php
   $rp = fn($n) => ($n < 0 ? '-Rp. ' : 'Rp. ') . number_format(abs($n), 0, ',', '.');
   $des = fn($n, $d = 1) => number_format($n, $d, ',', '.');
   $seluruhAngsuran = $analisa->angsuran_berjalan + $analisa->angsuran_kredit;
   $penghasilanStress = $analisa->penghasilan * $aturan['stress_penghasilan'];
   $biayaStress = $analisa->biaya_hidup * $aturan['stress_biaya'];
   $no = 0;
@endphp

<style>
   .ak { margin-bottom: 6px; }
   .ak td, .ak th { border: 1px solid #000; padding: 1px 4px; font-size: 9pt; vertical-align: top; }
   .ak th { background-color: #f2f2f2; text-align: center; }
   .ak th:last-child, .ak td:last-child { border-right: 1px solid #000 !important; }
   .ak .c { text-align: center; }
   .ak .r { text-align: right; }
   .ak .kel td { background-color: #f9f9f9; font-weight: bold; }
   .ak .tot td { background-color: #f2f2f2; font-weight: bold; }
   .ak-judul { font-size: 10pt; font-weight: bold; margin: 8px 0 3px; }
   .ak-kecil { font-size: 8pt; margin: 2px 0 0; }
</style>

<div class="ak-judul">A. Kemampuan Bayar Keluarga (per bulan)</div>
<table class="ak">
   <tr>
      <td width="26%">Penghasilan bersih</td>
      <td width="24%" class="r">{{ $rp($analisa->penghasilan) }}</td>
      <td width="26%">Biaya hidup rumah tangga</td>
      <td width="24%" class="r">{{ $rp($analisa->biaya_hidup) }}</td>
   </tr>
   <tr>
      <td>Angsuran kredit berjalan</td>
      <td class="r">{{ $rp($analisa->angsuran_berjalan) }}</td>
      <td>Angsuran kredit ini</td>
      <td class="r">{{ $rp($analisa->angsuran_kredit) }}</td>
   </tr>
   <tr>
      <td>Seluruh angsuran</td>
      <td class="r">{{ $rp($seluruhAngsuran) }}</td>
      <td><b>DSR setelah kredit</b> (nilai {{ $analisa->nilai_dsr }})</td>
      <td class="r"><b>{{ $des($analisa->dsr * 100, 2) }} %</b></td>
   </tr>
   <tr>
      <td>Sisa penghasilan</td>
      <td class="r">{{ $rp($analisa->sisa_penghasilan) }}</td>
      <td><b>Sisa penghasilan stress test</b> (nilai {{ $analisa->nilai_stress }})</td>
      <td class="r"><b>{{ $rp($analisa->sisa_penghasilan_stress) }}</b></td>
   </tr>
</table>
<div class="ak-kecil">
   Stress test: penghasilan turun {{ $des((1 - $aturan['stress_penghasilan']) * 100, 0) }}% menjadi {{ $rp($penghasilanStress) }},
   biaya hidup naik {{ $des(($aturan['stress_biaya'] - 1) * 100, 0) }}% menjadi {{ $rp($biayaStress) }}.
</div>

<div class="ak-judul">B. Penilaian dan Skor</div>
<table class="ak">
   <tr>
      <th width="4%">No</th>
      <th>Kriteria</th>
      <th width="15%">Keterangan</th>
      <th width="7%">Bobot</th>
      <th width="7%">Nilai</th>
      <th width="7%">Skor</th>
      <th width="17%">Syarat wajib</th>
   </tr>
   @foreach ($kelompok as $namaKelompok => $keys)
      @php $baris = collect($keys)->map(fn($k) => $detail[$k]); @endphp
      <tr class="kel">
         <td colspan="3">{{ $namaKelompok }}</td>
         <td class="c">{{ $baris->sum('bobot') }}</td>
         <td></td>
         <td class="c">{{ $des($baris->sum('skor')) }}</td>
         <td></td>
      </tr>
      @foreach ($keys as $key)
         @php
            $d = $detail[$key];
            $no++;
            $keterangan = match (true) {
               $key === 'dsr' => $des($analisa->dsr * 100, 2) . ' %',
               $key === 'stress' => $rp($analisa->sisa_penghasilan_stress),
               $d->jawaban !== null => $d->jawaban,
               default => '-',
            };
            $lolos = $d->nilai >= $gateMinimal;
         @endphp
         <tr>
            <td class="c">{{ $no }}</td>
            <td>{{ $label[$key] }}</td>
            <td class="c">{{ $keterangan }}</td>
            <td class="c">{{ $d->bobot }}</td>
            <td class="c">{{ $d->nilai }}</td>
            <td class="c">{{ $des($d->skor) }}</td>
            <td class="c">{{ $d->wajib ? ($lolos ? 'Terpenuhi' : 'Belum terpenuhi') : '-' }}</td>
         </tr>
      @endforeach
   @endforeach
   <tr class="tot">
      <td colspan="3" class="r">TOTAL</td>
      <td class="c">{{ $detail->sum('bobot') }}</td>
      <td></td>
      <td class="c">{{ $des($analisa->skor_total, 2) }}</td>
      <td></td>
   </tr>
</table>
<div class="ak-kecil">
   Skor kriteria = bobot &times; nilai &divide; {{ $aturan['nilai_maks'] }}. Nilai 0&ndash;{{ $aturan['nilai_maks'] }}, nilai 0 berarti data belum tersedia.
   Syarat wajib harus bernilai minimal {{ $gateMinimal }}.
</div>

<div class="ak-judul">C. Hasil Analisa</div>
<table class="ak">
   <tr>
      <th width="25%">Skor (dari {{ $totalBobot }})</th>
      <th width="30%">Syarat wajib terpenuhi</th>
      <th>Rekomendasi</th>
   </tr>
   <tr>
      <td class="c" style="font-size: 11pt;"><b>{{ $des($analisa->skor_total, 1) }}</b></td>
      <td class="c" style="font-size: 11pt;"><b>{{ $analisa->syarat_terpenuhi }} / {{ $analisa->syarat_total }}</b></td>
      <td class="c" style="font-size: 11pt;"><b>{{ $analisa->rekomendasi }}</b></td>
   </tr>
   @if (count($belumTerpenuhi) > 0)
      <tr>
         <td colspan="3">
            <b>Syarat wajib yang belum terpenuhi:</b>
            @foreach ($belumTerpenuhi as $item)
               {{ $loop->iteration }}. {{ $item }}@if (! $loop->last); @endif
            @endforeach
         </td>
      </tr>
   @endif
</table>
<div class="ak-kecil">
   Ada syarat wajib belum terpenuhi: TUNDA. Semua terpenuhi: skor {{ $aturan['skor_layak'] }} ke atas Layak Direkomendasikan,
   {{ $aturan['skor_komite'] }}&ndash;{{ number_format($aturan['skor_layak'] - 0.1, 1, ',', '.') }} Dipertimbangkan Komite,
   di bawah {{ $aturan['skor_komite'] }} Tidak Direkomendasikan.
</div>

<div class="ak-judul">D. Catatan Analis</div>
<table class="ak">
   <tr>
      <td style="min-height: 30px; height: 30px; white-space: pre-line;">{{ $analisa->catatan ?: '-' }}</td>
   </tr>
</table>
<div class="ak-kecil">Analisa disimpan {{ $analisa->updated_at->format('d-m-Y H:i') }}.</div>
