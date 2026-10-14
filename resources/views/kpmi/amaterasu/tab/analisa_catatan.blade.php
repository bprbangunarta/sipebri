{{-- Catatan analis. Variabel: $isian. --}}
<div class="box box-primary">
   <div class="box-body kpmi-padat">
      <div class="form-group" style="margin-bottom: 0;">
         <label>Catatan analis <small class="text-muted">(opsional)</small></label>
         <textarea name="catatan" class="form-control" rows="3" placeholder="Hal penting yang perlu diketahui Komite...">{{ $isian['catatan'] ?? '' }}</textarea>
      </div>
   </div>
</div>
