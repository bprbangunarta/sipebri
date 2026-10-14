@extends('theme.app')
@section('title', 'Analisa KPMI - Amaterasu')

@section('content')
<div class="content-wrapper">
   <section class="content">
      @include('kpmi.amaterasu._style')

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

      @include('kpmi.amaterasu._data_pengajuan')

      {{-- ===================== TAHAP ANALISA ===================== --}}
      <div class="nav-tabs-custom kpmi-tahap">
         <ul class="nav nav-tabs">
            <li class="active">
               <a href="#tab-analisa" data-tab>
                  1. Analisa Kredit
                  @if ($analisa) <i class="fa fa-check-circle text-green" title="Analisa sudah tersimpan"></i> @endif
               </a>
            </li>
            <li>
               <a href="#tab-dokumen" data-tab>
                  2. Analisa Dokumen
                  <span class="label label-default" id="tab-dokumen-jumlah">0/{{ count($dokumen['daftar']) }}</span>
               </a>
            </li>
            <li>
               <a href="#tab-memorandum" data-tab>3. Memorandum</a>
            </li>
            <li>
               <a href="#tab-administrasi" data-tab>4. Biaya Administrasi</a>
            </li>
            <li>
               <a href="#tab-komite" data-tab>
                  5. Pengajuan Komite
                  @if ($pengajuan->tracking === 'Persetujuan Komite') <i class="fa fa-check-circle text-green" title="Sudah dikirim ke Komite"></i> @endif
               </a>
            </li>
         </ul>

         <div class="tab-content">
            <div class="tab-pane active" id="tab-analisa">
               @include('kpmi.amaterasu.tab.analisa')
            </div>

            <div class="tab-pane" id="tab-dokumen">
               @include('kpmi.amaterasu.tab.dokumen')
            </div>

            <div class="tab-pane" id="tab-memorandum">
               @include('kpmi.amaterasu.tab.memorandum')
            </div>

            <div class="tab-pane" id="tab-administrasi">
               @include('kpmi.amaterasu.tab.administrasi')
            </div>

            <div class="tab-pane" id="tab-komite">
               @include('kpmi.amaterasu.tab.komite')
            </div>
         </div>
      </div>

      <form id="form-komite" method="POST" action="{{ route('kpmi.amaterasi.komite', $kodeRoute) }}">
         @csrf
      </form>
   </section>
</div>
@endsection

@push('myscript')
<script src="{{ asset('assets/js/myscript/kpmi_amaterasu.js') }}"></script>
@endpush