{{-- Satu kolom isian tahap 3 dan 4. Variabel: $f (definisi kolom), $awal (nilai awal) --}}
@php
   $nilai = $awal[$f['name']] ?? '';
   $tipe = $f['tipe'] ?? 'rupiah';
   $kunci = 'f-' . $f['name'];
   $hanyaBaca = ! empty($f['readonly']);
   $kelas = trim('form-control ' . ($f['kelas'] ?? ''));
   $petunjuk = $hanyaBaca ? '-' : '0';
@endphp

<div class="form-group">
   <label for="{{ $kunci }}" title="{{ $f['label'] }}">{{ $f['label'] }}</label>

   @if ($tipe === 'rupiah')
      <div class="input-group">
         <span class="input-group-addon">Rp</span>
         <input type="text" inputmode="numeric" id="{{ $kunci }}" name="{{ $f['name'] }}" class="{{ $kelas }} kpmi-uang"
            value="{{ $nilai }}" placeholder="{{ $petunjuk }}" @readonly($hanyaBaca)>
      </div>
   @elseif ($tipe === 'persen')
      <div class="input-group">
         <input type="text" inputmode="decimal" id="{{ $kunci }}" name="{{ $f['name'] }}" class="{{ $kelas }} kpmi-persen"
            value="{{ $nilai }}" placeholder="{{ $petunjuk }}" @readonly($hanyaBaca)>
         <span class="input-group-addon">%</span>
      </div>
   @elseif ($tipe === 'angka')
      <div class="input-group">
         <input type="text" inputmode="numeric" id="{{ $kunci }}" name="{{ $f['name'] }}" class="{{ $kelas }}"
            value="{{ $nilai }}" placeholder="{{ $petunjuk }}" @readonly($hanyaBaca)>
         @if (! empty($f['satuan']))
            <span class="input-group-addon">{{ $f['satuan'] }}</span>
         @endif
      </div>
   @elseif ($tipe === 'area')
      <textarea id="{{ $kunci }}" name="{{ $f['name'] }}" class="{{ $kelas }}" rows="2" @readonly($hanyaBaca)>{{ $nilai }}</textarea>
   @elseif ($tipe === 'select')
      <select id="{{ $kunci }}" name="{{ $f['name'] }}" class="{{ $kelas }}">
         <option value="">- Pilih -</option>
         @foreach ($f['opsi'] as $opsi)
            <option value="{{ $opsi }}" @selected($nilai === $opsi)>{{ $opsi }}</option>
         @endforeach
      </select>
   @else
      <input type="text" id="{{ $kunci }}" name="{{ $f['name'] }}" class="{{ $kelas }}" value="{{ $nilai }}" @readonly($hanyaBaca)>
   @endif
</div>
