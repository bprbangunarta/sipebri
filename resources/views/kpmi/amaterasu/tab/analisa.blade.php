{{-- Tab 1. Analisa Kredit: petunjuk, form isian (langkah A, B, C, catatan) dan panel hasil. Variabel: $analisa, $kodeRoute, $konfigurasi, $angsuran. --}}
<div class="row">
   <div class="col-md-12">
      <div class="kpmi-petunjuk">
         <b>Cara mengisi:</b> kerjakan <b>Langkah A &rarr; B &rarr; C</b> dari atas ke bawah.
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

@if ($angsuranBerubah)
   <div class="kpmi-peringatan">
      <b>Plafon, bunga, atau tenor pengajuan berubah sejak analisa disimpan.</b>
      Angsuran saat disimpan Rp {{ number_format($analisa->angsuran_kredit, 0, ',', '.') }}, sekarang Rp {{ number_format($angsuran, 0, ',', '.') }}.
      Periksa hasil di bawah lalu tekan <b>SIMPAN</b> agar analisa sesuai dengan data terbaru.
   </div>
@endif

<form id="form-analisa" method="POST" action="{{ route('kpmi.amaterasi.store', $kodeRoute) }}" autocomplete="off" data-config="{{ json_encode($konfigurasi) }}" data-angsuran="{{ $angsuran }}">
   @csrf
   <div class="row">
      {{-- ===================== KOLOM KIRI: ISIAN ===================== --}}
      <div class="col-lg-8">

         @include('kpmi.amaterasu.tab.analisa_a')

         @include('kpmi.amaterasu.tab.analisa_b')

         @include('kpmi.amaterasu.tab.analisa_c')

         @include('kpmi.amaterasu.tab.analisa_catatan')
      </div>

      @include('kpmi.amaterasu.tab.analisa_hasil')
   </div>
</form>
