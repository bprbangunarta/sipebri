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

   // ---- Dokumen pendukung: langsung diunggah, disimpan di server ----
   var boxDokumen = $('box-dokumen');
   if (boxDokumen) {
      var jenisBoleh = JSON.parse(boxDokumen.dataset.jenis);
      var maksByte = parseFloat(boxDokumen.dataset.maksMb) * 1024 * 1024;
      var tersimpan = JSON.parse(boxDokumen.dataset.tersimpan || '{}');
      var urlDokumen = boxDokumen.dataset.urlDokumen;
      var tokenCsrf = form.querySelector('input[name="_token"]').value;
      var barisDokumen = boxDokumen.querySelectorAll('tr[data-dokumen]');

      function urlJenis(jenis) {
         return urlDokumen.replace(/JENIS$/, encodeURIComponent(jenis));
      }

      function ukuranFile(byte) {
         return byte >= 1024 * 1024
            ? (byte / 1024 / 1024).toLocaleString('id-ID', { maximumFractionDigits: 1 }) + ' MB'
            : Math.max(1, Math.round(byte / 1024)).toLocaleString('id-ID') + ' KB';
      }

      function hitungDokumen() {
         var terunggah = boxDokumen.querySelectorAll('tr.terisi[data-dokumen]').length;
         var penanda = $('dokumen-jumlah');
         penanda.textContent = terunggah + ' / ' + barisDokumen.length + ' terunggah';
         penanda.className = 'label pull-right ' + (terunggah === barisDokumen.length ? 'label-success' : 'label-default');

         var resumeTeks = $('resume-dokumen-teks');
         if (resumeTeks) {
            var lengkap = terunggah === barisDokumen.length;
            resumeTeks.textContent = terunggah + ' dari ' + barisDokumen.length + ' dokumen terunggah';
            $('resume-dokumen-status').innerHTML = '<b>' + (lengkap ? 'Lengkap' : 'Belum lengkap') + '</b>';
            $('resume-dokumen-status').className = lengkap ? 'text-green' : 'text-aqua';
            $('resume-dokumen-ikon').className = 'fa fa-lg ' + (lengkap ? 'fa-check-circle text-green' : 'fa-info-circle text-aqua');
         }

         var penandaTab = $('tab-dokumen-jumlah');
         if (penandaTab) {
            penandaTab.textContent = terunggah + '/' + barisDokumen.length;
            penandaTab.className = 'label ' + (terunggah === barisDokumen.length ? 'label-success' : 'label-default');
         }
      }

      // Gambar ulang baris dari data dokumen (null = belum ada); sibuk = sedang mengunggah/menghapus.
      function gambarBaris(baris, sibuk) {
         var dok = baris._dok;
         var status = baris.querySelector('.dokumen-status');
         var lihat = baris.querySelector('.dokumen-lihat');
         var hapus = baris.querySelector('.dokumen-hapus');
         var tombol = baris.querySelector('.dokumen-tombol');
         var pilih = baris.querySelector('label.btn');

         status.textContent = '';
         status.removeAttribute('title');

         if (sibuk) {
            status.className = 'dokumen-status text-muted';
            var putar = document.createElement('i');
            putar.className = 'fa fa-spinner fa-spin';
            status.appendChild(putar);
            status.appendChild(document.createTextNode(' ' + sibuk));
         } else if (dok) {
            status.className = 'dokumen-status';
            var ikon = document.createElement('i');
            ikon.className = 'fa fa-file-o';
            status.appendChild(ikon);
            status.appendChild(document.createTextNode(' ' + dok.nama + ' '));
            var kecil = document.createElement('small');
            kecil.className = 'text-muted';
            kecil.textContent = '(' + ukuranFile(dok.ukuran) + ')';
            status.appendChild(kecil);
            status.title = dok.nama;
         } else {
            status.className = 'dokumen-status text-muted';
            status.textContent = 'Belum diunggah';
         }

         tombol.textContent = dok ? 'Ganti' : 'Pilih file';
         lihat.style.display = dok ? '' : 'none';
         hapus.style.display = dok ? '' : 'none';
         baris.classList.toggle('terisi', !!dok);

         [lihat, hapus].forEach(function (el) { el.disabled = !!sibuk; });
         pilih.classList.toggle('disabled', !!sibuk);
         pilih.style.pointerEvents = sibuk ? 'none' : '';
      }

      // Pratinjau dari file yang tersimpan di server; PDF dalam bingkai, gambar apa adanya.
      function lihatDokumen(url, nama, ext) {
         if (typeof Swal === 'undefined') {
            window.open(url, '_blank');
            return;
         }

         var isi = ext === 'pdf'
            ? '<iframe src="' + escapeHtml(url) + '" style="width:100%;height:70vh;border:0"></iframe>'
            : '<img src="' + escapeHtml(url) + '" alt="" style="max-width:100%;max-height:70vh">';

         Swal.fire({
            titleText: nama,
            html: isi,
            width: '900px',
            showConfirmButton: false,
            showCloseButton: true
         });
      }

      function pesanGalat(res) {
         var json = res.json || {};
         if (json.errors && json.errors.file) return json.errors.file[0];
         return json.message || 'Gagal diproses, coba lagi.';
      }

      function panggil(url, opsi) {
         opsi.headers = Object.assign({ 'Accept': 'application/json', 'X-CSRF-TOKEN': tokenCsrf }, opsi.headers || {});
         opsi.credentials = 'same-origin';

         return fetch(url, opsi).then(function (r) {
            return r.json().catch(function () { return {}; }).then(function (json) {
               return { ok: r.ok, status: r.status, json: json };
            });
         });
      }

      barisDokumen.forEach(function (baris) {
         var jenis = baris.dataset.dokumen;
         var input = baris.querySelector('.kpmi-file');
         var galat = baris.querySelector('.dokumen-galat');

         baris._dok = tersimpan[jenis] || null;
         gambarBaris(baris);

         input.addEventListener('change', function () {
            galat.style.display = 'none';
            var file = input.files[0];
            if (!file) return;

            var ekstensi = file.name.split('.').pop().toLowerCase();
            var pesan = null;

            if (jenisBoleh.indexOf(ekstensi) === -1) {
               pesan = 'Format tidak didukung. Gunakan ' + jenisBoleh.join(', ').toUpperCase() + '.';
            } else if (file.size > maksByte) {
               pesan = 'Ukuran ' + ukuranFile(file.size) + ' melebihi batas ' + boxDokumen.dataset.maksMb + ' MB.';
            }

            if (pesan) {
               input.value = '';
               galat.textContent = pesan;
               galat.style.display = '';
               return;
            }

            var data = new FormData();
            data.append('file', file);
            gambarBaris(baris, 'Mengunggah...');

            panggil(urlJenis(jenis), { method: 'POST', body: data })
               .then(function (res) {
                  if (res.ok) {
                     baris._dok = res.json;
                  } else {
                     galat.textContent = pesanGalat(res);
                     galat.style.display = '';
                  }
               })
               .catch(function () {
                  galat.textContent = 'Koneksi gagal, file belum terunggah.';
                  galat.style.display = '';
               })
               .then(function () {
                  input.value = '';
                  gambarBaris(baris);
                  hitungDokumen();
               });
         });

         baris.querySelector('.dokumen-lihat').addEventListener('click', function () {
            if (baris._dok) lihatDokumen(urlJenis(jenis), baris._dok.nama, baris._dok.ext);
         });

         baris.querySelector('.dokumen-hapus').addEventListener('click', function () {
            if (!baris._dok) return;

            function hapus() {
               galat.style.display = 'none';
               gambarBaris(baris, 'Menghapus...');

               panggil(urlJenis(jenis), { method: 'DELETE' })
                  .then(function (res) {
                     if (res.ok) {
                        baris._dok = null;
                     } else {
                        galat.textContent = pesanGalat(res);
                        galat.style.display = '';
                     }
                  })
                  .catch(function () {
                     galat.textContent = 'Koneksi gagal, file belum terhapus.';
                     galat.style.display = '';
                  })
                  .then(function () {
                     gambarBaris(baris);
                     hitungDokumen();
                  });
            }

            if (typeof Swal === 'undefined') {
               if (confirm('Hapus dokumen ' + baris._dok.nama + '?')) hapus();
               return;
            }

            Swal.fire({
               title: 'Hapus dokumen?',
               text: baris._dok.nama,
               icon: 'warning',
               showCancelButton: true,
               confirmButtonColor: '#d33',
               cancelButtonColor: '#6c757d',
               confirmButtonText: 'Ya, hapus',
               cancelButtonText: 'Batal',
               reverseButtons: true,
               focusCancel: true
            }).then(function (hasil) {
               if (hasil.isConfirmed) hapus();
            });
         });
      });

      hitungDokumen();
   }

   // ---- Tab tahap analisa ----
   var tautTab = document.querySelectorAll('.kpmi-tahap > .nav-tabs a[data-tab]');

   function bukaTab(id) {
      var ada = false;
      tautTab.forEach(function (a) {
         var aktif = a.getAttribute('href') === '#' + id;
         ada = ada || aktif;
         a.parentNode.classList.toggle('active', aktif);
      });
      if (!ada) return false;

      document.querySelectorAll('.kpmi-tahap > .tab-content > .tab-pane').forEach(function (pane) {
         pane.classList.toggle('active', pane.id === id);
      });
      return true;
   }

   tautTab.forEach(function (a) {
      a.addEventListener('click', function (e) {
         e.preventDefault();
         var id = a.getAttribute('href').slice(1);
         if (bukaTab(id) && window.history && history.replaceState) history.replaceState(null, '', '#' + id);
      });
   });

   // tautan "Buka tahap" pada resume
   document.querySelectorAll('[data-buka-tab]').forEach(function (a) {
      a.addEventListener('click', function (e) {
         e.preventDefault();
         var id = a.dataset.bukaTab;
         if (bukaTab(id) && window.history && history.replaceState) history.replaceState(null, '', '#' + id);
         document.querySelector('.kpmi-tahap').scrollIntoView({ block: 'start' });
      });
   });


   // ---- Sub-tab pada tahap Memorandum dan Biaya Administrasi ----
   document.querySelectorAll('.kpmi-subtab').forEach(function (kotak) {
      var tautan = kotak.querySelectorAll(':scope > .nav-tabs a[data-subtab]');

      tautan.forEach(function (a) {
         a.addEventListener('click', function (e) {
            e.preventDefault();
            var id = a.getAttribute('href').slice(1);

            tautan.forEach(function (l) { l.parentNode.classList.toggle('active', l === a); });
            if (window.history && history.replaceState) history.replaceState(null, '', '#' + id);
            kotak.querySelectorAll(':scope > .tab-content > .tab-pane').forEach(function (pane) {
               pane.classList.toggle('active', pane.id === id);
            });
         });
      });
   });

   // ---- Isian rupiah dan persen pada tahap Memorandum dan Biaya Administrasi ----
   function angkaDigit(el) {
      var digit = el.value.replace(/\D/g, '');
      return digit === '' ? 0 : parseInt(digit, 10);
   }

   function tulisUang(id, nilai) {
      var el = $(id);
      if (el) el.value = angka(nilai, 0);
   }

   // Jumlah kebutuhan dana = penjumlahan lima kebutuhan; ikut tampil di Usulan Kredit.
   function hitungKebutuhan() {
      var jumlah = 0;
      document.querySelectorAll('.kpmi-kebutuhan').forEach(function (el) { jumlah += angkaDigit(el); });
      tulisUang('f-jumlah_kebutuhan_dana', jumlah);
      tulisUang('f-kebutuhan_dana', jumlah);
   }

   function formatUang(el) {
      var digit = el.value.replace(/\D/g, '');
      el.value = digit === '' ? '' : angka(parseInt(digit, 10), 0);
   }

   // Max Plafond dan Repayment Capacity (metode efektif anuitas, dari kemampuan angsuran pada analisa kredit)
   function hitungUsulan() {
      var formUsulan = $('form-usulan');
      var kemampuan = formUsulan ? parseFloat(formUsulan.dataset.kemampuan) : NaN;
      var maks = $('f-max_plafond');
      var rc = $('f-repayment_capacity');
      if (!maks || !rc || !(kemampuan > 0)) return;

      var bunga = parseFloat(($('f-suku_bunga').value || '').replace(',', '.')) / 100 / 12;
      var tenor = parseInt($('f-jangka_waktu').value, 10);
      var plafond = angkaDigit($('f-usulan_plafond'));

      if (!(tenor > 0) || isNaN(bunga)) {
         maks.value = '';
         rc.value = '';
         return;
      }

      var faktor = bunga === 0 ? tenor : (1 - Math.pow(1 + bunga, -tenor)) / bunga;
      var batas = Math.round(kemampuan * faktor);
      maks.value = angka(batas, 0);
      rc.value = (plafond / faktor / kemampuan * 100).toFixed(2);

      // usulan plafond tidak boleh melebihi max plafond
      var lebih = plafond > batas;
      var peringatan = $('peringatan-max');
      var tombol = $('simpan-usulan');
      $('f-usulan_plafond').closest('.form-group').classList.toggle('has-error', lebih);
      peringatan.style.display = lebih ? '' : 'none';
      peringatan.textContent = lebih
         ? 'Usulan plafond melebihi Max Plafond Rp ' + angka(batas, 0) + ' (Repayment Capacity ' + rc.value + '%). Turunkan usulan atau sesuaikan jangka waktu.'
         : '';
      tombol.disabled = lebih;
   }

   document.addEventListener('input', function (e) {
      var el = e.target;
      if (['f-usulan_plafond', 'f-jangka_waktu', 'f-suku_bunga'].indexOf(el.id) !== -1) {
         if (el.classList.contains('kpmi-uang')) formatUang(el);
         else if (el.classList.contains('kpmi-persen')) el.value = el.value.replace(',', '.').replace(/[^0-9.]/g, '');
         hitungUsulan();
         return;
      }

      if (el.classList.contains('kpmi-uang')) {
         formatUang(el);
         if (el.classList.contains('kpmi-kebutuhan')) hitungKebutuhan();
      } else if (el.classList.contains('kpmi-persen')) {
         el.value = el.value.replace(',', '.').replace(/[^0-9.]/g, '');
      }
   });

   // persen biaya ditulis lengkap dua desimal (1 menjadi 1.00) saat petugas pindah kolom
   document.addEventListener('focusout', function (e) {
      var el = e.target;
      if (!el.classList || !el.classList.contains('kpmi-desimal2') || el.value.trim() === '') return;

      var nilai = parseFloat(el.value.replace(',', '.'));
      if (!isNaN(nilai)) {
         el.value = nilai.toFixed(2);
         hitungUsulan();
      }
   });

   document.querySelectorAll('.kpmi-uang').forEach(function (el) { if (el.value !== '') formatUang(el); });
   hitungKebutuhan();
   hitungUsulan();

   // ---- Kirim ke Komite ----
   function isianSekarang() {
      var data = new FormData(form);
      data.delete('_token');
      return new URLSearchParams(data).toString();
   }

   var isianAwal = isianSekarang();

   function escapeHtml(teks) {
      var el = document.createElement('div');
      el.textContent = teks;
      return el.innerHTML;
   }

   var tombolKomite = $('tombol-komite');
   if (tombolKomite) {
      tombolKomite.addEventListener('click', function (e) {
         e.preventDefault();

         var formKomite = $('form-komite');
         var belumDisimpan = isianSekarang() !== isianAwal;
         var rekomendasi = tombolKomite.dataset.rekomendasi;
         var skor = parseFloat(tombolKomite.dataset.skor).toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 2 });

         var html =
            '<div style="text-align:left">' +
            '<p style="margin:0 0 10px">Rekomendasi analisa tersimpan: <b>' + escapeHtml(rekomendasi) + '</b> (skor ' + skor + ').</p>' +
            '<p style="margin:0 0 10px"><b>Pastikan analisa sudah sesuai dan benar</b> sebelum dikirim. ' +
            'Setelah dikirim, pengajuan masuk ke tahap persetujuan Komite.</p>' +
            '<p style="margin:0">Yang dikirim adalah analisa yang <b>sudah disimpan</b>.</p>' +
            (belumDisimpan
               ? '<p style="margin:12px 0 0;padding:8px 10px;background:#fdeeec;border-left:4px solid #dd4b39;color:#a52a1c">' +
                 'Ada isian yang <b>belum disimpan</b>. Perubahan itu tidak ikut terkirim. Tekan Batal lalu SIMPAN dulu jika ingin ikut.</p>'
               : '') +
            '</div>';

         if (typeof Swal === 'undefined') {
            if (confirm('Kirim analisa yang tersimpan ke Komite?\nRekomendasi: ' + rekomendasi)) formKomite.submit();
            return;
         }

         Swal.fire({
            title: 'Kirim ke Komite?',
            html: html,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#00a65a',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, kirim',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            focusCancel: true
         }).then(function (hasil) {
            if (hasil.isConfirmed) formKomite.submit();
         });
      });
   }

   // pilihan panjang (sandi lapbul) dibuat bisa dicari bila select2 tersedia pada template
   if (window.jQuery && jQuery.fn.select2) {
      jQuery('.kpmi-select2').select2({ width: '100%' });
   }

   // buka tab atau sub-tab sesuai alamat (misalnya #tab-dokumen atau #mm-usulan)
   (function () {
      var id = location.hash.slice(1);
      if (!id || bukaTab(id)) return;

      var pane = document.getElementById(id);
      var induk = pane && pane.closest('.kpmi-subtab') ? pane.closest('.kpmi-tahap > .tab-content > .tab-pane') : null;
      if (!induk) return;

      bukaTab(induk.id);
      var tautanSub = document.querySelector('.kpmi-subtab a[data-subtab][href="#' + id + '"]');
      if (tautanSub) tautanSub.click();
   })();
});
