{{-- Ringkasan debitur, pendamping, dan pengajuan di atas semua tab. Variabel: $pengajuan. --}}
@php
   $nasabah = $pengajuan->nasabah;
   $pendamping = $pengajuan->pendamping;
   $tglLahir = \Carbon\Carbon::createFromFormat('Ymd', $nasabah->tanggal_lahir)->format('d-m-Y');
   $tglLahirPendamping = $pendamping
      ? \Carbon\Carbon::createFromFormat('Ymd', $pendamping->tanggal_lahir)->format('d-m-Y')
      : null;
@endphp

{{-- ===================== DATA PENGAJUAN (konteks bersama semua tahap) ===================== --}}
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
