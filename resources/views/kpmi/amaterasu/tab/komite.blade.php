{{-- Tab 5. Pengajuan Komite: resume tahap 1 sampai 4 dan tombol kirim ke Komite. Variabel: $tahap, $analisa, $angsuranBerubah, $pengajuan, $dokumen, $dokumenTersimpan, $statusMemorandum, $memorandumTersimpan, $administrasi, $administrasiTersimpan, $totalBobot. --}}
   <p class="kpmi-hint" style="margin-bottom: 10px;">
      <i class="fa fa-info-circle"></i> Periksa ringkasan tiap tahap sebelum mengirim pengajuan ke Komite.
   </p>

   <table class="table table-condensed kpmi-resume">
      <thead>
         <tr>
            <th style="width: 40px;"></th>
            <th style="width: 24%;">Tahap</th>
            <th>Ringkasan</th>
            <th class="text-right" style="width: 150px;">Status</th>
         </tr>
      </thead>
      <tbody>
         @foreach ($tahap as $t)
            <tr data-resume="{{ $t['id'] }}">
               @if ($t['id'] === 'tab-analisa')
                  @php
                     $rekomKelas = match (true) {
                        ! $analisa => '',
                        $analisa->rekomendasi === 'LAYAK DIREKOMENDASIKAN' => 'text-green',
                        $analisa->rekomendasi === 'DIPERTIMBANGKAN KOMITE' => 'text-yellow',
                        default => 'text-red',
                     };
                  @endphp
                  <td class="text-center">
                     <i class="fa {{ $analisa && ! $angsuranBerubah ? 'fa-check-circle text-green' : 'fa-exclamation-circle text-yellow' }} fa-lg"></i>
                  </td>
                  <td><b>{{ $loop->iteration }}. {{ $t['nama'] }}</b></td>
                  <td>
                     @if ($analisa)
                        Skor <b>{{ number_format($analisa->skor_total, 1, ',', '.') }}</b> dari {{ $totalBobot }} &middot;
                        syarat wajib terpenuhi <b>{{ $analisa->syarat_terpenuhi }} / {{ $analisa->syarat_total }}</b> &middot;
                        <b class="{{ $rekomKelas }}">{{ $analisa->rekomendasi }}</b>
                        <br><small class="text-muted">Tersimpan {{ $analisa->updated_at->format('d-m-Y H:i') }} oleh {{ $analisa->update_user ?? $analisa->input_user ?? '-' }}</small>
                        @if ($angsuranBerubah)
                           <br><small class="text-yellow"><b>Plafon, bunga, atau tenor berubah sejak analisa disimpan.</b> Simpan ulang analisa agar hasilnya sesuai.</small>
                        @endif
                     @else
                        Analisa belum disimpan.
                     @endif
                  </td>
                  <td class="text-right">
                     <span class="{{ $analisa && ! $angsuranBerubah ? 'text-green' : 'text-yellow' }}"><b>{{ ! $analisa ? 'Belum disimpan' : ($angsuranBerubah ? 'Perlu disimpan ulang' : 'Tersimpan') }}</b></span><br>
                     <a href="#{{ $t['id'] }}" data-buka-tab="{{ $t['id'] }}"><small>Buka tahap</small></a>
                  </td>
               @elseif ($t['id'] === 'tab-dokumen')
                  @php $dokumenLengkap = count($dokumenTersimpan) === count($dokumen['daftar']); @endphp
                  <td class="text-center">
                     <i class="fa fa-lg {{ $dokumenLengkap ? 'fa-check-circle text-green' : 'fa-info-circle text-aqua' }}" id="resume-dokumen-ikon"></i>
                  </td>
                  <td><b>{{ $loop->iteration }}. {{ $t['nama'] }}</b></td>
                  <td>
                     <span id="resume-dokumen-teks">{{ count($dokumenTersimpan) }} dari {{ count($dokumen['daftar']) }} dokumen terunggah</span>
                     <br><small class="text-muted">Informasi tambahan, tidak memengaruhi analisa kredit.</small>
                  </td>
                  <td class="text-right">
                     <span id="resume-dokumen-status" class="{{ $dokumenLengkap ? 'text-green' : 'text-aqua' }}"><b>{{ $dokumenLengkap ? 'Lengkap' : 'Belum lengkap' }}</b></span><br>
                     <a href="#{{ $t['id'] }}" data-buka-tab="{{ $t['id'] }}"><small>Buka tahap</small></a>
                  </td>
               @elseif ($t['id'] === 'tab-memorandum')
                  @php $memoLengkap = $statusMemorandum['kebutuhan'] && $statusMemorandum['usulan'] && $statusMemorandum['sandi']; @endphp
                  <td class="text-center">
                     <i class="fa fa-lg {{ $memoLengkap ? 'fa-check-circle text-green' : 'fa-exclamation-circle text-yellow' }}"></i>
                  </td>
                  <td><b>{{ $loop->iteration }}. {{ $t['nama'] }}</b></td>
                  <td>
                     @foreach ([['kebutuhan', 'Kebutuhan dana'], ['usulan', 'Usulan kredit'], ['sandi', 'Sandi lapbul']] as [$kunciMemo, $namaMemo])
                        <span class="{{ $statusMemorandum[$kunciMemo] ? 'text-green' : 'text-yellow' }}">
                           <i class="fa {{ $statusMemorandum[$kunciMemo] ? 'fa-check' : 'fa-times' }}"></i> {{ $namaMemo }}</span>@if (! $loop->last) &nbsp;&middot;&nbsp; @endif
                     @endforeach
                     @if ($memorandumTersimpan)
                        <br><small class="text-muted">Usulan plafond Rp {{ number_format($memorandumTersimpan->usulan_plafond, 0, ',', '.') }} &middot; {{ $pengajuan->jangka_waktu }} bulan &middot; bunga {{ $pengajuan->suku_bunga }}%</small>
                     @endif
                  </td>
                  <td class="text-right">
                     <span class="{{ $memoLengkap ? 'text-green' : 'text-yellow' }}"><b>{{ $memoLengkap ? 'Lengkap' : 'Belum lengkap' }}</b></span><br>
                     <a href="#{{ $t['id'] }}" data-buka-tab="{{ $t['id'] }}"><small>Buka tahap</small></a>
                  </td>
               @elseif ($t['id'] === 'tab-administrasi')
                  @php
                     $totalBiaya = $administrasiTersimpan
                        ? collect($administrasi['biaya'])->sum(fn($f) => (int) ($administrasiTersimpan->{$f['name']} ?? 0)) + (int) $administrasiTersimpan->lainnya
                        : 0;
                  @endphp
                  <td class="text-center">
                     <i class="fa fa-lg {{ $administrasiTersimpan ? 'fa-check-circle text-green' : 'fa-exclamation-circle text-yellow' }}"></i>
                  </td>
                  <td><b>{{ $loop->iteration }}. {{ $t['nama'] }}</b></td>
                  <td>
                     @if ($administrasiTersimpan)
                        Total biaya <b>Rp {{ number_format($totalBiaya, 0, ',', '.') }}</b> &middot;
                        administrasi Rp {{ number_format($administrasiTersimpan->administrasi, 0, ',', '.') }} &middot;
                        provisi Rp {{ number_format($administrasiTersimpan->provisi, 0, ',', '.') }}
                        <br><small class="text-muted">Tersimpan {{ ($administrasiTersimpan->updated_at ?? $administrasiTersimpan->created_at)?->format('d-m-Y H:i') }} oleh {{ $administrasiTersimpan->input_user }}</small>
                     @else
                        Biaya administrasi belum disimpan.
                     @endif
                  </td>
                  <td class="text-right">
                     <span class="{{ $administrasiTersimpan ? 'text-green' : 'text-yellow' }}"><b>{{ $administrasiTersimpan ? 'Tersimpan' : 'Belum disimpan' }}</b></span><br>
                     <a href="#{{ $t['id'] }}" data-buka-tab="{{ $t['id'] }}"><small>Buka tahap</small></a>
                  </td>
               @else
                  <td class="text-center"><i class="fa fa-minus-circle text-muted fa-lg"></i></td>
                  <td><b>{{ $loop->iteration }}. {{ $t['nama'] }}</b></td>
                  <td>
                     Isian belum tersambung ke penyimpanan.
                     <br><small class="text-muted">Status akan tampil setelah tahap ini bisa disimpan.</small>
                  </td>
                  <td class="text-right">
                     <span class="text-muted"><b>Belum tersedia</b></span><br>
                     <a href="#{{ $t['id'] }}" data-buka-tab="{{ $t['id'] }}"><small>Buka tahap</small></a>
                  </td>
               @endif
            </tr>
         @endforeach
      </tbody>
   </table>

   <div class="kpmi-kirim">
      @if (! $analisa)
         <span title="Simpan analisa kredit dulu agar bisa dikirim ke Komite" style="cursor: not-allowed;">
            <a class="btn btn-success disabled"><i class="fa fa-send"></i> KIRIM KE KOMITE</a>
         </span>
      @elseif ($pengajuan->tracking === 'Persetujuan Komite')
         <span title="Pengajuan sudah dikirim ke Komite" style="cursor: not-allowed;">
            <a class="btn btn-success disabled"><i class="fa fa-check"></i> SUDAH DIKIRIM KE KOMITE</a>
         </span>
      @else
         <button type="submit" form="form-komite" id="tombol-komite" class="btn btn-success"
            data-rekomendasi="{{ $analisa->rekomendasi }}" data-skor="{{ $analisa->skor_total }}">
            <i class="fa fa-send"></i> KIRIM KE KOMITE
         </button>
      @endif
   </div>
