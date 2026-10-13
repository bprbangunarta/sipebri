<!DOCTYPE html>
<html lang="id">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>Analisa Kredit KPMI - {{ $pengajuan->nasabah->nama_nasabah }}</title>

   <style>
      @page { size: A4; margin: 14mm 14mm 16mm; }
      * { box-sizing: border-box; }
      body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #111; margin: 0; background: #eee; }
      .halaman { width: 210mm; min-height: 297mm; margin: 10px auto; padding: 14mm; background: #fff; }
      h1 { font-size: 16px; margin: 0; text-align: center; letter-spacing: .5px; }
      .sub { text-align: center; margin: 3px 0 12px; color: #333; }
      h2 { font-size: 11.5px; margin: 14px 0 5px; padding: 4px 6px; background: #e8eef4; border-left: 4px solid #2c5d8a; }
      table { width: 100%; border-collapse: collapse; }
      td, th { padding: 3px 6px; vertical-align: top; }
      .data td { padding: 2px 6px; }
      .data td:first-child { width: 34%; color: #333; }
      .grid th, .grid td { border: 1px solid #888; }
      .grid th { background: #f0f0f0; text-align: center; font-size: 10.5px; }
      .grid tr { page-break-inside: avoid; }
      .kelompok td { background: #f6f6f6; font-weight: bold; }
      .total td { font-weight: bold; background: #f0f0f0; }
      .c { text-align: center; }
      .r { text-align: right; }
      .ok { color: #0b6b3a; }
      .gagal { color: #a52a1c; font-weight: bold; }
      .hasil { display: table; width: 100%; border: 1px solid #888; margin-top: 4px; }
      .hasil > div { display: table-cell; padding: 8px 10px; text-align: center; vertical-align: middle; }
      .hasil .judul { font-size: 10px; color: #444; }
      .hasil .angka { font-size: 20px; font-weight: bold; margin-top: 2px; }
      .rekomendasi { font-size: 15px; font-weight: bold; margin-top: 2px; }
      .rekomendasi.success { color: #0b6b3a; }
      .rekomendasi.warning { color: #8a5a00; }
      .rekomendasi.danger { color: #a52a1c; }
      .catatan { border: 1px solid #888; min-height: 40px; padding: 6px; white-space: pre-line; }
      .ttd { margin-top: 26px; width: 100%; }
      .ttd td { width: 50%; text-align: center; padding: 0 20px; }
      .ttd .garis { margin-top: 56px; border-top: 1px solid #111; padding-top: 3px; }
      .alat { position: fixed; top: 10px; right: 14px; }
      .alat button { padding: 7px 14px; font-size: 12px; cursor: pointer; }
      .kecil { font-size: 9.5px; color: #555; }

      @media print {
         body { background: #fff; }
         .halaman { width: auto; min-height: 0; margin: 0; padding: 0; }
         .alat { display: none; }
      }
   </style>
</head>
@php
   $nasabah = $pengajuan->nasabah;
   $pendamping = $pengajuan->pendamping;
   $tanggal = fn($ymd) => \Carbon\Carbon::createFromFormat('Ymd', $ymd)->format('d-m-Y');
   $rp = fn($n) => ($n < 0 ? '-Rp ' : 'Rp ') . number_format(abs($n), 0, ',', '.');
   $desimal = fn($n, $d = 1) => number_format($n, $d, ',', '.');

   $rekomendasiKelas = match (true) {
      $analisa->rekomendasi === 'LAYAK DIREKOMENDASIKAN' => 'success',
      $analisa->rekomendasi === 'DIPERTIMBANGKAN KOMITE' => 'warning',
      default => 'danger',
   };

   $penghasilanStress = $analisa->penghasilan * $aturan['stress_penghasilan'];
   $biayaStress = $analisa->biaya_hidup * $aturan['stress_biaya'];
   $seluruhAngsuran = $analisa->angsuran_berjalan + $analisa->angsuran_kredit;
   $pembuat = $analisa->update_user ?? $analisa->input_user ?? '-';
@endphp
<body>
   <div class="alat">
      <button type="button" onclick="window.print()">Cetak / Simpan PDF</button>
   </div>

   <div class="halaman">
      <h1>ANALISA KREDIT KPMI</h1>
      <div class="sub">
         {{ $pengajuan->produk->nama_produk }} &middot; {{ $pengajuan->resort->nama_resort }}
      </div>

      {{-- 1. DATA PENGAJUAN --}}
      <h2>1. Data Pengajuan dan Debitur</h2>
      <table>
         <tr>
            <td style="width: 50%; padding: 0 8px 0 0;">
               <table class="data">
                  <tr><td>Kode Pengajuan</td><td><b>{{ $pengajuan->kode_pengajuan }}</b></td></tr>
                  <tr><td>Nama Debitur</td><td><b>{{ $nasabah->nama_nasabah }}</b></td></tr>
                  <tr><td>NIK</td><td>{{ $nasabah->no_identitas }}</td></tr>
                  <tr><td>Tempat, Tgl Lahir</td><td>{{ $nasabah->tempat_lahir }}, {{ $tanggal($nasabah->tanggal_lahir) }}</td></tr>
                  <tr><td>Alamat</td><td>{{ $nasabah->alamat_sekarang }}</td></tr>
                  <tr><td>No. Telepon</td><td>{{ $nasabah->no_telp }}</td></tr>
               </table>
            </td>
            <td style="width: 50%; padding: 0 0 0 8px;">
               <table class="data">
                  <tr><td>Plafon</td><td><b>{{ $rp($pengajuan->plafon) }}</b></td></tr>
                  <tr><td>Suku Bunga</td><td>{{ $pengajuan->suku_bunga }}% / tahun, {{ $pengajuan->metode_rps }}</td></tr>
                  <tr><td>Jangka Waktu</td><td>{{ $pengajuan->jangka_waktu }} bulan</td></tr>
                  <tr><td>Angsuran per bulan</td><td><b>{{ $rp($analisa->angsuran_kredit) }}</b></td></tr>
                  @if ($pendamping)
                     <tr><td>Pendamping</td><td><b>{{ $pendamping->nama_pendamping }}</b> ({{ $pendamping->status }})</td></tr>
                     <tr><td>NIK Pendamping</td><td>{{ $pendamping->no_identitas }}</td></tr>
                  @endif
                  @if ($analisa->skor_lendana !== null)
                     <tr><td>Skor Lendana</td><td>{{ $desimal($analisa->skor_lendana) }} <span class="kecil">(referensi)</span></td></tr>
                  @endif
               </table>
            </td>
         </tr>
      </table>

      {{-- 2. KEMAMPUAN BAYAR --}}
      <h2>2. Kemampuan Bayar Keluarga (per bulan)</h2>
      <table class="grid">
         <thead>
            <tr>
               <th style="width: 58%;">Komponen</th>
               <th>Nilai</th>
            </tr>
         </thead>
         <tbody>
            <tr><td>Penghasilan bersih</td><td class="r">{{ $rp($analisa->penghasilan) }}</td></tr>
            <tr><td>Biaya hidup rumah tangga</td><td class="r">{{ $rp($analisa->biaya_hidup) }}</td></tr>
            <tr><td>Angsuran kredit berjalan</td><td class="r">{{ $rp($analisa->angsuran_berjalan) }}</td></tr>
            <tr><td>Angsuran kredit ini</td><td class="r">{{ $rp($analisa->angsuran_kredit) }}</td></tr>
            <tr><td>Seluruh angsuran</td><td class="r">{{ $rp($seluruhAngsuran) }}</td></tr>
            <tr><td><b>DSR setelah kredit</b> (seluruh angsuran &divide; penghasilan)</td><td class="r"><b>{{ $desimal($analisa->dsr * 100, 2) }}%</b></td></tr>
            <tr><td>Sisa penghasilan (penghasilan &minus; biaya hidup &minus; seluruh angsuran)</td><td class="r">{{ $rp($analisa->sisa_penghasilan) }}</td></tr>
            <tr>
               <td>
                  <b>Stress test</b>: penghasilan turun {{ $desimal((1 - $aturan['stress_penghasilan']) * 100, 0) }}% menjadi {{ $rp($penghasilanStress) }},
                  biaya hidup naik {{ $desimal(($aturan['stress_biaya'] - 1) * 100, 0) }}% menjadi {{ $rp($biayaStress) }}.
                  Sisa penghasilan saat stress test
               </td>
               <td class="r"><b>{{ $rp($analisa->sisa_penghasilan_stress) }}</b></td>
            </tr>
         </tbody>
      </table>

      {{-- 3. PENILAIAN --}}
      <h2>3. Penilaian dan Skor</h2>
      <table class="grid">
         <thead>
            <tr>
               <th style="width: 26px;">No</th>
               <th>Kriteria</th>
               <th style="width: 90px;">Keterangan</th>
               <th style="width: 38px;">Bobot</th>
               <th style="width: 34px;">Nilai</th>
               <th style="width: 38px;">Skor</th>
               <th style="width: 104px;">Syarat wajib</th>
            </tr>
         </thead>
         <tbody>
            @php $no = 0; @endphp
            @foreach ($kelompok as $namaKelompok => $keys)
               @php
                  $baris = collect($keys)->map(fn($k) => $detail[$k]);
               @endphp
               <tr class="kelompok">
                  <td colspan="3">{{ $namaKelompok }}</td>
                  <td class="c">{{ $baris->sum('bobot') }}</td>
                  <td></td>
                  <td class="c">{{ $desimal($baris->sum('skor')) }}</td>
                  <td></td>
               </tr>
               @foreach ($keys as $key)
                  @php
                     $d = $detail[$key];
                     $no++;
                     $keterangan = match (true) {
                        $key === 'dsr' => $desimal($analisa->dsr * 100, 2) . '%',
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
                     <td class="c">{{ $desimal($d->skor) }}</td>
                     <td class="c">
                        @if ($d->wajib)
                           <span class="{{ $lolos ? 'ok' : 'gagal' }}">{{ $lolos ? 'Terpenuhi' : 'Belum terpenuhi' }}</span>
                        @else
                           -
                        @endif
                     </td>
                  </tr>
               @endforeach
            @endforeach
            <tr class="total">
               <td colspan="3" class="r">TOTAL</td>
               <td class="c">{{ $detail->sum('bobot') }}</td>
               <td></td>
               <td class="c">{{ $desimal($analisa->skor_total, 2) }}</td>
               <td></td>
            </tr>
         </tbody>
      </table>
      <div class="kecil" style="margin-top: 3px;">
         Skor kriteria = bobot &times; nilai &divide; 5. Nilai 0&ndash;5, nilai 0 berarti data belum tersedia.
         Syarat wajib harus bernilai minimal {{ $gateMinimal }}.
      </div>

      {{-- 4. HASIL --}}
      <h2>4. Hasil Analisa</h2>
      <div class="hasil">
         <div style="width: 22%;">
            <div class="judul">SKOR (dari 100)</div>
            <div class="angka">{{ $desimal($analisa->skor_total, 1) }}</div>
         </div>
         <div style="width: 26%;">
            <div class="judul">SYARAT WAJIB TERPENUHI</div>
            <div class="angka">{{ $analisa->syarat_terpenuhi }} / {{ $analisa->syarat_total }}</div>
         </div>
         <div>
            <div class="judul">REKOMENDASI</div>
            <div class="rekomendasi {{ $rekomendasiKelas }}">{{ $analisa->rekomendasi }}</div>
         </div>
      </div>

      @if (count($belumTerpenuhi) > 0)
         <div style="margin-top: 6px;">
            <b>Syarat wajib yang belum terpenuhi:</b>
            <ul style="margin: 3px 0 0 18px; padding: 0;">
               @foreach ($belumTerpenuhi as $item)
                  <li>{{ $item }}</li>
               @endforeach
            </ul>
         </div>
      @endif

      <div class="kecil" style="margin-top: 6px;">
         Ada syarat wajib belum terpenuhi: TUNDA. Semua terpenuhi: skor 80 ke atas Layak Direkomendasikan,
         70&ndash;79,9 Dipertimbangkan Komite, di bawah 70 Tidak Direkomendasikan.
      </div>

      {{-- 5. CATATAN --}}
      <h2>5. Catatan Analis</h2>
      <div class="catatan">{{ $analisa->catatan ?: '-' }}</div>

      <table class="ttd">
         <tr>
            <td>
               {{ $analisa->updated_at->format('d-m-Y') }}<br>
               Dianalisa oleh,
               <div class="garis">{{ $pembuat }}</div>
            </td>
         </tr>
      </table>

      <div class="kecil" style="margin-top: 14px;">
         Dicetak {{ now()->format('d-m-Y H:i') }} &middot; analisa tersimpan {{ $analisa->updated_at->format('d-m-Y H:i') }}
      </div>
   </div>
</body>
</html>
