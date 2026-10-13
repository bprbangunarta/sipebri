document.addEventListener('DOMContentLoaded', function () {
   var form = document.getElementById('form-analisa');
   var cfg = JSON.parse(form.dataset.config);
   var aturan = cfg.aturan;
   var EPS = 1e-9;

   var totalIsian = form.querySelectorAll('.kpmi-isian').length +
      new Set(Array.from(form.querySelectorAll('.kpmi-isian-radio')).map(function (el) { return el.name; })).size;

   var $ = function (id) { return document.getElementById(id); };

   function angka(n, desimal) {
      return n.toLocaleString('id-ID', { minimumFractionDigits: desimal, maximumFractionDigits: desimal });
   }

   function rupiah(n) {
      return (n < 0 ? '-Rp ' : 'Rp ') + angka(Math.abs(Math.round(n)), 0);
   }

   // Isian rupiah: tampil dengan pemisah ribuan, nilai dibaca sebagai angka.
   function bacaRupiah(el) {
      var digit = el.value.replace(/\D/g, '');
      return digit === '' ? null : parseInt(digit, 10);
   }

   function formatRupiah(el) {
      var n = bacaRupiah(el);
      el.value = n === null ? '' : angka(n, 0);
   }

   function pilihan(name) {
      var dipilih = form.querySelector('input[name="' + name + '"]:checked');
      return dipilih ? dipilih.value : null;
   }

   // ---- Nilai 0-5 dari angka RC ----
   function nilaiDsr(dsr) {
      if (dsr === null) return 0;
      for (var i = 0; i < aturan.dsr.length; i++) {
         if (dsr <= aturan.dsr[i][0] + EPS) return aturan.dsr[i][1];
      }
      return 1;
   }

   function nilaiStress(sisa, penghasilanStress) {
      if (sisa === null) return 0;
      if (sisa <= 0) return 1;
      var rasio = sisa / penghasilanStress;
      if (rasio >= aturan.stress_rasio.nilai_5 - EPS) return 5;
      if (rasio >= aturan.stress_rasio.nilai_4 - EPS) return 4;
      return 3;
   }

   function hitung() {
      var penghasilan = bacaRupiah(form.elements.penghasilan);
      var biayaHidup = bacaRupiah(form.elements.biaya_hidup);
      var berjalan = bacaRupiah(form.elements.angsuran_berjalan) || 0;
      var angsuran = berjalan + parseFloat(form.dataset.angsuran);

      var adaPenghasilan = penghasilan !== null && penghasilan > 0;

      var dsr = adaPenghasilan ? angsuran / penghasilan : null;
      var sisa = adaPenghasilan && biayaHidup !== null ? penghasilan - biayaHidup - angsuran : null;

      var penghasilanStress = adaPenghasilan ? penghasilan * aturan.stress_penghasilan : null;
      var sisaStress = adaPenghasilan && biayaHidup !== null
         ? penghasilanStress - biayaHidup * aturan.stress_biaya - angsuran
         : null;

      var nDsr = nilaiDsr(dsr);
      var nStress = nilaiStress(sisaStress, penghasilanStress);

      var total = 0;
      var gagal = [];

      cfg.syarat.forEach(function (s) {
         var nilai = pilihan(s.name) === 'Ya' ? 5 : 0;
         total += s.bobot * nilai / 5;
         if (s.wajib && nilai < aturan.gate_minimal) gagal.push(s.label);
      });

      cfg.penilaian.forEach(function (p) {
         var v = pilihan(p.name);
         total += p.bobot * (v === null ? 0 : parseInt(v, 10)) / 5;
      });

      total += cfg.rc.dsr.bobot * nDsr / 5;
      total += cfg.rc.stress.bobot * nStress / 5;
      if (nDsr < aturan.gate_minimal) gagal.push(cfg.rc.dsr.label);
      if (nStress < aturan.gate_minimal) gagal.push(cfg.rc.stress.label);

      // Dibulatkan dulu agar skor yang tepat 80,0 tidak terbaca 79,99999 saat dibandingkan.
      total = Math.round(total * 100) / 100;

      var rekomendasi;
      if (gagal.length > 0) rekomendasi = { teks: 'TUNDA', kelas: 'hasil-danger' };
      else if (total >= aturan.skor_layak) rekomendasi = { teks: 'LAYAK DIREKOMENDASIKAN', kelas: 'hasil-success' };
      else if (total >= aturan.skor_komite) rekomendasi = { teks: 'DIPERTIMBANGKAN KOMITE', kelas: 'hasil-warning' };
      else rekomendasi = { teks: 'TIDAK DIREKOMENDASIKAN', kelas: 'hasil-danger' };

      return {
         dsr: dsr, sisa: sisa, sisaStress: sisaStress, nDsr: nDsr, nStress: nStress,
         total: total, gagal: gagal, rekomendasi: rekomendasi
      };
   }

   // ---- Tampilan ----
   function tampilkanKelengkapan() {
      var jumlah = 0;
      var sudah = {};

      form.querySelectorAll('.kpmi-isian').forEach(function (el) {
         if (el.value.trim() !== '') jumlah++;
      });

      form.querySelectorAll('.kpmi-isian-radio').forEach(function (el) {
         if (sudah[el.name]) return;
         sudah[el.name] = true;

         var dipilih = form.querySelector('input[name="' + el.name + '"]:checked');
         if (dipilih) jumlah++;
         el.closest('tr').classList.toggle('terisi', !!dipilih);
      });

      form.querySelectorAll('.kpmi-opt label').forEach(function (l) {
         l.classList.remove('btn-success', 'btn-danger', 'btn-primary', 'active');
      });
      form.querySelectorAll('.kpmi-isian-radio:checked').forEach(function (el) {
         var label = el.closest('label');
         label.classList.add(label.dataset.on, 'active');
      });

      $('progres-teks').textContent = jumlah + ' / ' + totalIsian;
      $('progres-bar').style.width = Math.round(jumlah / totalIsian * 100) + '%';

      return totalIsian - jumlah;
   }

   function tampilkanHasil(h, belumDiisi) {
      $('rc-dsr').textContent = h.dsr === null ? '-' : angka(h.dsr * 100, 2) + '%';
      $('rc-nilai-dsr').textContent = h.nDsr;
      $('rc-residual').textContent = h.sisa === null ? '-' : rupiah(h.sisa);
      $('rc-stress-residual').textContent = h.sisaStress === null ? '-' : rupiah(h.sisaStress);
      $('rc-nilai-stress').textContent = h.nStress;

      $('hasil-skor').textContent = h.total.toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 2 });
      var totalWajib = cfg.syarat.filter(function (s) { return s.wajib; }).length + 2;
      var terpenuhi = totalWajib - h.gagal.length;
      var gate = $('hasil-gate');
      gate.innerHTML = terpenuhi + ' <small>/ ' + totalWajib + '</small>';
      gate.className = h.gagal.length === 0 ? 'text-green' : 'text-red';

      var box = $('hasil-rekomendasi-box');
      box.className = 'kpmi-hasil ' + h.rekomendasi.kelas;
      $('hasil-rekomendasi').textContent = h.rekomendasi.teks;
      $('hasil-catatan').textContent = belumDiisi > 0
         ? 'Hasil sementara: ' + belumDiisi + ' isian belum diisi dihitung bernilai 0.'
         : '';

      var daftar = $('hasil-daftar-gagal-isi');
      daftar.innerHTML = '';
      h.gagal.forEach(function (teks) {
         var li = document.createElement('li');
         li.innerHTML = '<i class="fa fa-times text-red"></i> ';
         li.appendChild(document.createTextNode(teks));
         daftar.appendChild(li);
      });
      $('hasil-daftar-gagal').style.display = h.gagal.length > 0 ? '' : 'none';
   }

   function perbarui() {
      var belumDiisi = tampilkanKelengkapan();
      tampilkanHasil(hitung(), belumDiisi);
   }

   form.addEventListener('input', function (e) {
      if (e.target.classList.contains('kpmi-rupiah')) formatRupiah(e.target);
      perbarui();
   });
   form.addEventListener('change', perbarui);

   form.querySelectorAll('.kpmi-rupiah').forEach(formatRupiah);
   perbarui();
});
