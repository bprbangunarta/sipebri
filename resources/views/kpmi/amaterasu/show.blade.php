@extends('theme.app')
@section('title', 'Analisa KPMI - Amaterasu')

@php
   $nasabah = $pengajuan->nasabah;
   $pendamping = $pengajuan->pendamping;
   $tglLahir = \Carbon\Carbon::createFromFormat('Ymd', $nasabah->tanggal_lahir)->format('d-m-Y');
   $tglLahirPendamping = $pendamping
      ? \Carbon\Carbon::createFromFormat('Ymd', $pendamping->tanggal_lahir)->format('d-m-Y')
      : null;
@endphp

@section('content')
<div class="content-wrapper">
   <section class="content">
      <style>
         .kpmi-aside { position: sticky; top: 65px; }
         @media (max-width: 991px) { .kpmi-aside { position: static; } }
         .kpmi-step { display: inline-block; width: 24px; height: 24px; line-height: 24px; border-radius: 50%;
            background: #3c8dbc; color: #fff; text-align: center; font-size: 13px; margin-right: 6px; }
         .kpmi-hint { color: #777; margin: 0 0 12px; }
         .kpmi-opt .btn { min-width: 38px; }
         .kpmi-row td { vertical-align: middle !important; }
         .kpmi-petunjuk { background: #f4f8fb; border: 1px solid #d6e4ef; border-left: 4px solid #3c8dbc;
            color: #333; padding: 10px 15px; margin-bottom: 15px; border-radius: 3px; }
         .kpmi-petunjuk .kpmi-garis { display: inline-block; width: 4px; height: 14px; vertical-align: -2px; margin: 0 4px 0 2px; }
         .kpmi-hasil { border: 1px solid #ddd; border-left: 5px solid #999; background: #f7f7f7; color: #333;
            padding: 10px 15px; margin-bottom: 12px; text-align: center; border-radius: 3px; }
         .kpmi-hasil small { color: #555; display: block; }
         .kpmi-hasil h4 { margin: 4px 0 2px; font-weight: 600; }
         .kpmi-hasil.hasil-success { background: #eaf6ee; border-color: #b7dfc4; border-left-color: #00a65a; }
         .kpmi-hasil.hasil-success h4 { color: #0b6b3a; }
         .kpmi-hasil.hasil-warning { background: #fff6e0; border-color: #f0dca6; border-left-color: #f39c12; }
         .kpmi-hasil.hasil-warning h4 { color: #8a5a00; }
         .kpmi-hasil.hasil-danger { background: #fdeeec; border-color: #f0c4be; border-left-color: #dd4b39; }
         .kpmi-hasil.hasil-danger h4 { color: #a52a1c; }
         .kpmi-row.terisi td:first-child { border-left: 3px solid #00a65a; }
         .kpmi-row:not(.terisi) td:first-child { border-left: 3px solid #f39c12; }
      </style>

      @if (session('success'))
         <div class="alert alert-success alert-dismissible" style="margin-bottom: 15px;">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <i class="fa fa-check"></i> {{ session('success') }}
         </div>
      @endif

      @if ($errors->any())
         <div class="alert alert-danger" style="margin-bottom: 15px;">
            <b>Analisa belum tersimpan.</b> Lengkapi isian berikut:
            <ul style="margin: 5px 0 0 18px; padding: 0;">
               @foreach ($errors->all() as $pesan)
                  <li>{{ $pesan }}</li>
               @endforeach
            </ul>
         </div>
      @endif

      <div class="row">
         <div class="col-md-12">
            <div class="kpmi-petunjuk">
               <b>Cara mengisi:</b> kerjakan <b>Langkah 1 &rarr; 2 &rarr; 3</b> dari atas ke bawah.
               Hasil analisa di panel kanan terhitung otomatis dan berubah setiap Anda mengisi.
               <br>
               <span class="kpmi-garis" style="background: #f39c12;"></span> belum diisi
               &nbsp;&nbsp;
               <span class="kpmi-garis" style="background: #00a65a;"></span> sudah diisi
               @if ($analisa)
                  <br>Tersimpan {{ $analisa->updated_at->format('d-m-Y H:i') }}
                  oleh {{ $analisa->update_user ?? $analisa->input_user ?? '-' }}.
               @endif
            </div>
         </div>
      </div>

      <form id="form-analisa" method="POST" action="{{ route('kpmi.amaterasi.store', $kodeRoute) }}" autocomplete="off" data-config="{{ json_encode($konfigurasi) }}" data-angsuran="{{ $angsuran }}">
         @csrf
         <div class="row">
            {{-- ===================== KOLOM KIRI: ISIAN ===================== --}}
            <div class="col-lg-8">

               {{-- DATA PENGAJUAN (ringkas, bisa dilipat) --}}
               <div class="box box-primary">
                  <div class="box-header with-border">
                     <h3 class="box-title">
                        {{ $nasabah->nama_nasabah }}
                        <small>&middot; {{ $pengajuan->kode_pengajuan }} &middot; {{ $pengajuan->resort->nama_resort }}</small>
                     </h3>
                     <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" title="Lipat/buka data pengajuan">
                           <i class="fa fa-minus"></i>
                        </button>
                     </div>
                  </div>

                  <div class="box-body">
                     <div class="row">
                        <div class="col-md-6">
                           <table class="table table-condensed" style="margin-bottom: 0;">
                              <tr>
                                 <td style="width: 38%;">NIK</td>
                                 <td>{{ $nasabah->no_identitas }}</td>
                              </tr>
                              <tr>
                                 <td>Tempat, Tgl Lahir</td>
                                 <td>{{ $nasabah->tempat_lahir }}, {{ $tglLahir }}</td>
                              </tr>
                              <tr>
                                 <td>Alamat</td>
                                 <td>{{ $nasabah->alamat_sekarang }}</td>
                              </tr>
                              <tr>
                                 <td>No. Telepon</td>
                                 <td>{{ $nasabah->no_telp }}</td>
                              </tr>
                           </table>
                        </div>
                        <div class="col-md-6">
                           <table class="table table-condensed" style="margin-bottom: 0;">
                              <tr>
                                 <td style="width: 38%;">Plafon</td>
                                 <td><b>Rp {{ number_format($pengajuan->plafon, 0, ',', '.') }}</b></td>
                              </tr>
                              <tr>
                                 <td>Bunga</td>
                                 <td>{{ $pengajuan->suku_bunga }}% / tahun &middot; {{ $pengajuan->metode_rps }}</td>
                              </tr>
                              <tr>
                                 <td>Jangka waktu</td>
                                 <td>{{ $pengajuan->jangka_waktu }} bulan</td>
                              </tr>
                              <tr>
                                 <td>Skor Lendana</td>
                                 <td>
                                    <div class="input-group input-group-sm" style="width: 170px;">
                                       <input type="number" step="0.1" min="0" max="100" name="skor_lendana" class="form-control" placeholder="0 - 100" value="{{ $isian['skor_lendana'] ?? '' }}">
                                       <span class="input-group-addon">referensi</span>
                                    </div>
                                 </td>
                              </tr>
                           </table>
                        </div>
                     </div>

                     <hr style="margin: 10px 0;">

                     <b>Pendamping (calon Debitur Bersama)</b>
                     @if ($pendamping)
                        <div class="row" style="margin-top: 5px;">
                           <div class="col-md-6">
                              <table class="table table-condensed" style="margin-bottom: 0;">
                                 <tr>
                                    <td style="width: 38%;">Nama</td>
                                    <td><b>{{ $pendamping->nama_pendamping }}</b> ({{ $pendamping->status }})</td>
                                 </tr>
                                 <tr>
                                    <td>NIK</td>
                                    <td>{{ $pendamping->no_identitas }}</td>
                                 </tr>
                              </table>
                           </div>
                           <div class="col-md-6">
                              <table class="table table-condensed" style="margin-bottom: 0;">
                                 <tr>
                                    <td style="width: 38%;">Tempat, Tgl Lahir</td>
                                    <td>{{ $pendamping->tempat_lahir }}, {{ $tglLahirPendamping }}</td>
                                 </tr>
                              </table>
                           </div>
                        </div>
                     @else
                        <p class="text-muted" style="margin: 5px 0 0;">Data pendamping belum ada.</p>
                     @endif
                  </div>
               </div>

               {{-- LANGKAH 1 --}}
               <div class="box box-primary">
                  <div class="box-header with-border">
                     <h3 class="box-title"><span class="kpmi-step">1</span> Kemampuan bayar keluarga</h3>
                  </div>

                  <div class="box-body">
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
                                 <small class="text-muted">(penghasilan -30%, biaya hidup +10%)</small>
                              </td>
                              <td class="text-right" id="rc-stress-residual">-</td>
                              <td class="text-center" id="rc-nilai-stress">-</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
               </div>

               {{-- LANGKAH 2 --}}
               <div class="box box-primary">
                  <div class="box-header with-border">
                     <h3 class="box-title"><span class="kpmi-step">2</span> Syarat dan kesepakatan</h3>
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
                                    <div class="btn-group btn-group-sm kpmi-opt" data-toggle="buttons">
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
                                    <div class="btn-group btn-group-sm kpmi-opt" data-toggle="buttons">
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

               {{-- LANGKAH 3 --}}
               <div class="box box-primary">
                  <div class="box-header with-border">
                     <h3 class="box-title"><span class="kpmi-step">3</span> Penilaian</h3>
                  </div>

                  <div class="box-body no-padding">
                     <p class="kpmi-hint" style="padding: 10px 10px 0;">
                        Beri nilai <b>0 sampai 5</b>.
                        <b>0</b> = data belum tersedia &middot; <b>3</b> = cukup &middot; <b>5</b> = sangat baik.
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
                                       <div class="btn-group btn-group-sm kpmi-opt" data-toggle="buttons">
                                          @for ($n = 0; $n <= 5; $n++)
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

               <div class="box box-primary">
                  <div class="box-body">
                     <div class="form-group" style="margin-bottom: 0;">
                        <label>Catatan analis <small class="text-muted">(opsional)</small></label>
                        <textarea name="catatan" class="form-control" rows="3" placeholder="Hal penting yang perlu diketahui Komite...">{{ $isian['catatan'] ?? '' }}</textarea>
                     </div>
                  </div>
               </div>
            </div>

            {{-- ===================== KOLOM KANAN: HASIL (menempel saat scroll) ===================== --}}
            <div class="col-lg-4">
               <div class="kpmi-aside">
                  <div class="box box-success">
                     <div class="box-header with-border">
                        <h3 class="box-title">Hasil Analisa</h3>
                        <div class="box-tools pull-right">
                           @if ($analisa)
                              <a href="{{ route('kpmi.amaterasi.cetak', $kodeRoute) }}" target="_blank" class="btn btn-box-tool"
                                 title="Cetak analisa yang sudah tersimpan" style="font-size: 16px;">
                                 <i class="fa fa-print"></i>
                              </a>
                           @else
                              <span title="Simpan analisa dulu agar bisa dicetak" style="cursor: not-allowed;">
                                 <a class="btn btn-box-tool disabled" style="font-size: 16px; opacity: .35;">
                                    <i class="fa fa-print"></i>
                                 </a>
                              </span>
                           @endif
                        </div>
                     </div>

                     <div class="box-body">
                        <div style="margin-bottom: 12px;">
                           <small class="text-muted">
                              Kelengkapan isian:
                              <b id="progres-teks">0 / {{ $totalIsian }}</b>
                           </small>
                           <div class="progress progress-xs" style="margin: 4px 0 0;">
                              <div id="progres-bar" class="progress-bar progress-bar-success" style="width: 0%;"></div>
                           </div>
                        </div>

                        <div id="hasil-rekomendasi-box" class="kpmi-hasil">
                           <small>REKOMENDASI</small>
                           <h4 id="hasil-rekomendasi">Belum ada hasil</h4>
                           <small id="hasil-catatan"></small>
                        </div>

                        <div class="row text-center" style="margin-bottom: 10px;">
                           <div class="col-xs-6">
                              <small class="text-muted">SKOR (dari 100)</small>
                              <h2 id="hasil-skor" style="margin: 2px 0;">-</h2>
                           </div>
                           <div class="col-xs-6">
                              <small class="text-muted">SYARAT WAJIB TERPENUHI</small>
                              <h2 id="hasil-gate" style="margin: 2px 0;">- <small>/ 7</small></h2>
                           </div>
                        </div>

                        <div id="hasil-daftar-gagal" style="display: none;">
                           <small class="text-muted">Yang harus dipenuhi:</small>
                           <ul id="hasil-daftar-gagal-isi" class="list-unstyled" style="margin: 4px 0 0;"></ul>
                        </div>

                        <hr style="margin: 12px 0;">

                        <small class="text-muted">
                           <b>Cara membaca:</b><br>
                           Ada syarat wajib belum terpenuhi &rarr; <b>TUNDA</b>.<br>
                           Semua lolos &rarr; skor &ge;80 <b>Layak</b>, 70-79,9 <b>Komite</b>, &lt;70 <b>Tidak direkomendasikan</b>.
                        </small>
                     </div>

                     <div class="box-footer">
                        <a href="{{ route('permohonan.analisa') }}" class="btn btn-sm btn-default">
                           KEMBALI
                        </a>

                        <button type="submit" class="btn btn-sm btn-primary pull-right">
                           SIMPAN
                        </button>

                        @if (! $analisa)
                           <span class="pull-right" title="Simpan analisa dulu agar bisa dikirim ke Komite" style="cursor: not-allowed; margin-right: 6px;">
                              <a class="btn btn-sm btn-success disabled">KIRIM KE KOMITE</a>
                           </span>
                        @elseif ($pengajuan->tracking === 'Persetujuan Komite')
                           <span class="pull-right" title="Pengajuan sudah dikirim ke Komite" style="cursor: not-allowed; margin-right: 6px;">
                              <a class="btn btn-sm btn-success disabled">SUDAH DIKIRIM KE KOMITE</a>
                           </span>
                        @else
                           <button type="submit" form="form-komite" id="tombol-komite" class="btn btn-sm btn-success pull-right" style="margin-right: 6px;"
                              data-rekomendasi="{{ $analisa->rekomendasi }}" data-skor="{{ $analisa->skor_total }}">
                              KIRIM KE KOMITE
                           </button>
                        @endif
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </form>

      <form id="form-komite" method="POST" action="{{ route('kpmi.amaterasi.komite', $kodeRoute) }}">
         @csrf
      </form>
   </section>
</div>
@endsection

@push('myscript')
<script src="{{ asset('assets/js/myscript/kpmi_amaterasu.js') }}"></script>
@endpush