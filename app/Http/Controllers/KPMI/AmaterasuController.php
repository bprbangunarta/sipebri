<?php

namespace App\Http\Controllers\KPMI;

use App\Http\Controllers\Controller;
use App\Models\Midle;
use App\Models\Tambahan;
use App\Models\KPMI\DataAdministrasi;
use App\Models\KPMI\DataAnalisa;
use App\Models\KPMI\DataDokumen;
use App\Models\KPMI\DataKebutuhanDana;
use App\Models\KPMI\DataMemorandum;
use App\Models\KPMI\DataPengajuan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Encryption\DecryptException;

class AmaterasuController extends Controller
{
    public function show(string $kode_pengajuan)
    {
        $pengajuan = $this->ambilPengajuan($kode_pengajuan);
        $kode = $pengajuan->kode_pengajuan;
        // dd($pengajuan);

        $analisa = $pengajuan->analisa;

        $syaratWajib     = $this->syaratWajib();
        $syaratPendukung = $this->syaratPendukung();
        $penilaian       = $this->penilaian();
        $konfigurasi     = $this->konfigurasi($syaratWajib, $syaratPendukung, $penilaian);
        $angsuran        = $this->hitungAngsuran($pengajuan);

        return view('kpmi.amaterasu.show', [
            'pengajuan'       => $pengajuan,
            'analisa'         => $analisa,
            'kodeRoute'       => $kode_pengajuan,
            'syaratWajib'     => $syaratWajib,
            'syaratPendukung' => $syaratPendukung,
            'penilaian'       => $penilaian,
            'totalIsian'      => 3 + count($syaratWajib) + count($syaratPendukung) + collect($penilaian)->flatten(1)->count(),
            'angsuran'        => $angsuran,
            // plafon, bunga, atau tenor berubah (misalnya lewat usulan kredit) sejak analisa disimpan
            'angsuranBerubah' => $analisa && (int) $analisa->angsuran_kredit !== $angsuran,
            'konfigurasi'     => $konfigurasi,
            'aturan'          => $konfigurasi['aturan'],
            'totalWajib'      => collect($konfigurasi['syarat'])->where('wajib', true)->count() + 2, // + DSR dan stress test
            'totalBobot'      => $this->totalBobot($konfigurasi),
            'dokumen'         => $this->dokumenPendukung(),
            'tahap'           => $this->tahapAnalisa(),
            'memorandum'      => $this->memorandum(),
            'administrasi'    => $this->administrasi(),
            'awalTahap'       => $this->nilaiAwalTahap($pengajuan, $analisa, $angsuran),
            'kemampuanAngsuran' => $this->kemampuanAngsuran($analisa),
            'administrasiTersimpan' => $pengajuan->administrasi,
            'statusMemorandum' => $this->statusMemorandum($pengajuan),
            'memorandumTersimpan' => $pengajuan->memorandum,
            'kebutuhanTersimpan' => $pengajuan->kebutuhanDana,
            'dokumenTersimpan' => $pengajuan->dokumen->keyBy('jenis')->map(fn($d) => [
                'nama'   => $d->nama_asli,
                'ukuran' => $d->ukuran,
                'ext'    => pathinfo($d->path, PATHINFO_EXTENSION),
            ])->all(),
            'isian'           => $this->isian($analisa),
        ]);
    }

    public function store(Request $request, string $kode_pengajuan)
    {
        $pengajuan = $this->ambilPengajuan($kode_pengajuan);
        $kode = $pengajuan->kode_pengajuan;

        $this->rapikanAngka($request);
        $input = $request->validate($this->aturanValidasi(), $this->pesanValidasi(), $this->namaIsian());

        $angsuran = $this->hitungAngsuran($pengajuan);
        $hasil    = $this->hitung($input, $angsuran);
        $user     = auth()->user()?->username;

        DB::transaction(function () use ($kode, $input, $angsuran, $hasil, $user) {
            $analisa = DataAnalisa::firstOrNew(['pengajuan_kode' => $kode]);

            $analisa->fill([
                'skor_lendana'      => $input['skor_lendana'] ?? null,
                'penghasilan'       => $input['penghasilan'],
                'biaya_hidup'       => $input['biaya_hidup'],
                'angsuran_berjalan' => $input['angsuran_berjalan'],
                'angsuran_kredit'   => $angsuran,
                'catatan'           => $input['catatan'] ?? null,
            ] + $hasil['header']);

            if ($analisa->exists) {
                $analisa->update_user = $user;
            } else {
                $analisa->input_user = $user;
            }

            $analisa->save();

            $analisa->details()->delete();
            $analisa->details()->createMany($hasil['detail']);
        });

        return redirect()
            ->route('kpmi.amaterasi.show', $kode_pengajuan)
            ->with('success', 'Analisa berhasil disimpan. Rekomendasi: ' . $hasil['header']['rekomendasi'] . '.');
    }

    /**
     * Kirim ke Komite. Untuk KPMI tidak ada syarat analisa standar (collateral, sandi lapbul, administrasi);
     * cukup analisa KPMI sudah tersimpan. Perubahan tracking sama dengan konfirmasi analisa produk lain.
     */
    public function komite(string $kode_pengajuan)
    {
        $pengajuan = $this->ambilPengajuan($kode_pengajuan);
        $kode = $pengajuan->kode_pengajuan;

        if (! $pengajuan->analisa) {
            return redirect()
                ->route('kpmi.amaterasi.show', $kode_pengajuan)
                ->withErrors(['analisa' => 'Analisa belum disimpan, simpan dulu sebelum dikirim ke Komite.']);
        }

        if ($pengajuan->tracking === 'Persetujuan Komite') {
            return redirect()
                ->route('kpmi.amaterasi.show', $kode_pengajuan)
                ->withErrors(['analisa' => 'Pengajuan ini sudah dikirim ke Komite.']);
        }

        DB::transaction(function () use ($kode) {
            DB::table('data_tracking')->where('pengajuan_kode', $kode)->update(['analisa_kredit' => now()]);
            DataPengajuan::where('kode_pengajuan', $kode)->update([
                'tracking' => 'Persetujuan Komite',
                'status'   => 'Sudah Otorisasi',
            ]);
        });

        return redirect()->route('komite.kredit')->with('success', 'Konfirmasi berhasil');
    }

    /**
     * Unggah dokumen pendukung. Satu file per jenis; unggah ulang menggantikan file lama.
     * Disimpan di storage lokal dengan nama berawalan kode pengajuan: {kode}_{jenis}.{ekstensi}
     */
    public function dokumenSimpan(Request $request, string $kode_pengajuan, string $jenis)
    {
        $pengajuan = $this->ambilPengajuan($kode_pengajuan);
        $this->pastikanJenisDokumen($jenis);
        $kode = $pengajuan->kode_pengajuan;
        $dokumen = $this->dokumenPendukung();

        $request->validate([
            'file' => ['required', 'file', 'mimes:' . implode(',', $dokumen['jenis']), 'max:' . ($dokumen['maks_mb'] * 1024)],
        ], [
            'file.required' => 'File belum dipilih.',
            'file.file'     => 'File tidak valid.',
            'file.uploaded' => 'Unggah gagal. Ukuran file mungkin melebihi batas server.',
            'file.mimes'    => 'Format tidak didukung. Gunakan ' . strtoupper(implode(', ', $dokumen['jenis'])) . '.',
            'file.max'      => 'Ukuran melebihi batas ' . $dokumen['maks_mb'] . ' MB.',
        ]);

        $file = $request->file('file');
        $ekstensi = strtolower($file->guessExtension() ?: $file->getClientOriginalExtension());
        $ekstensi = $ekstensi === 'jpeg' ? 'jpg' : $ekstensi;

        $disk  = Storage::disk('local');
        $path  = "kpmi/amaterasu/{$kode}/{$kode}_{$jenis}.{$ekstensi}";
        $lama  = DataDokumen::where('pengajuan_kode', $kode)->where('jenis', $jenis)->first();

        if (! $disk->putFileAs(dirname($path), $file, basename($path))) {
            return response()->json(['message' => 'File gagal disimpan.'], 500);
        }

        try {
            $dokumenBaru = DataDokumen::updateOrCreate(
                ['pengajuan_kode' => $kode, 'jenis' => $jenis],
                [
                    'nama_asli'  => $file->getClientOriginalName(),
                    'path'       => $path,
                    'tipe'       => $file->getMimeType(),
                    'ukuran'     => $file->getSize(),
                    'input_user' => auth()->user()?->username,
                ]
            );
        } catch (\Throwable $e) {
            if (! $lama || $lama->path !== $path) {
                $disk->delete($path);
            }
            throw $e;
        }

        // file lama dengan ekstensi berbeda (misalnya pdf diganti png) dihapus agar tidak menumpuk
        if ($lama && $lama->path !== $path) {
            $disk->delete($lama->path);
        }

        return response()->json([
            'nama'   => $dokumenBaru->nama_asli,
            'ukuran' => $dokumenBaru->ukuran,
            'ext'    => $ekstensi,
        ]);
    }

    public function dokumenLihat(string $kode_pengajuan, string $jenis)
    {
        $pengajuan = $this->ambilPengajuan($kode_pengajuan);
        $this->pastikanJenisDokumen($jenis);

        $dokumen = DataDokumen::where('pengajuan_kode', $pengajuan->kode_pengajuan)->where('jenis', $jenis)->firstOrFail();
        $disk = Storage::disk('local');

        abort_unless($disk->exists($dokumen->path), 404);

        $respons = response()->file($disk->path($dokumen->path), [
            'Content-Type'           => $dokumen->tipe ?: 'application/octet-stream',
            'Content-Disposition'    => 'inline; filename="' . basename($dokumen->path) . '"',
            'X-Content-Type-Options' => 'nosniff',
        ]);

        // dokumen berisi data pribadi: jangan disimpan cache bersama
        $respons->setPrivate();
        $respons->headers->set('Cache-Control', 'private, no-store');

        return $respons;
    }

    public function dokumenHapus(string $kode_pengajuan, string $jenis)
    {
        $pengajuan = $this->ambilPengajuan($kode_pengajuan);
        $this->pastikanJenisDokumen($jenis);
        $kode = $pengajuan->kode_pengajuan;

        $dokumen = DataDokumen::where('pengajuan_kode', $kode)->where('jenis', $jenis)->first();

        if ($dokumen) {
            $disk = Storage::disk('local');
            $dokumen->delete();
            $disk->delete($dokumen->path);

            // folder pengajuan dihapus bila sudah kosong
            if (empty($disk->allFiles(dirname($dokumen->path)))) {
                $disk->deleteDirectory(dirname($dokumen->path));
            }
        }

        return response()->json(['ok' => true]);
    }

    private function pastikanJenisDokumen(string $jenis): void
    {
        abort_unless(collect($this->dokumenPendukung()['daftar'])->contains('name', $jenis), 404);
    }

    /**
     * Simpan biaya administrasi ke tabel administrasi alur lama (satu baris per pengajuan).
     * Tanpa agunan, APHT dan fiducia selalu 0. Persen administrasi dan provisi pada pengajuan ikut disinkronkan
     * dari nominal dibagi plafon, sama seperti perubahan biaya pada alur lama.
     */
    public function administrasiSimpan(Request $request, string $kode_pengajuan)
    {
        $pengajuan = $this->ambilPengajuan($kode_pengajuan);
        $kode = $pengajuan->kode_pengajuan;
        $kodeUser = Auth::user()->code_user;

        if (empty($kodeUser)) {
            return redirect()
                ->to(route('kpmi.amaterasi.show', $kode_pengajuan) . '#tab-administrasi')
                ->withErrors(['administrasi' => 'Kode pengguna tidak ditemukan, biaya administrasi tidak dapat disimpan.']);
        }

        $kolom = collect($this->administrasi()['biaya']);
        $bisaDiisi = $kolom->reject(fn($f) => ! empty($f['readonly']))->pluck('name')->all();

        // isian rupiah dikirim dengan pemisah ribuan ("1.750.000"); ambil angkanya, kosong dianggap 0
        $angka = [];
        foreach ($bisaDiisi as $nama) {
            $digit = preg_replace('/\D/', '', (string) $request->input($nama));
            $angka[$nama] = $digit === '' ? '0' : $digit;
        }
        $request->merge($angka);

        $nama = $kolom->pluck('label', 'name')->all();
        $input = $request->validate(
            array_fill_keys($bisaDiisi, ['required', 'integer', 'min:0', 'max:2147483647']),
            [
                'integer' => ':attribute harus berupa angka.',
                'max'     => ':attribute terlalu besar.',
            ],
            $nama
        );

        // kolom baca-saja (APHT, fiducia) tidak dipercaya dari kiriman halaman
        $nilai = array_map('intval', $input) + ['proses_apht' => 0];

        DB::transaction(function () use ($kode, $nilai, $kodeUser, $pengajuan) {
            $ada = DataAdministrasi::where('pengajuan_kode', $kode)->first();

            if ($ada) {
                $ada->update($nilai + ['input_user' => $kodeUser]);
            } else {
                DataAdministrasi::create($nilai + [
                    'kode_analisa'   => Midle::kodeacak_adm('ADM', 5),
                    'pengajuan_kode' => $kode,
                    'lainnya'        => 0,
                    'input_user'     => $kodeUser,
                ]);
            }

            // alur lama ikut menyimpan biaya fiducia pada memorandum; KPMI tanpa agunan sehingga 0
            DataMemorandum::where('pengajuan_kode', $kode)->update(['by_fiducia' => 0]);

            $plafon = (float) $pengajuan->plafon;
            if ($plafon > 0) {
                DataPengajuan::where('kode_pengajuan', $kode)->update([
                    'b_admin'   => number_format($nilai['administrasi'] / $plafon * 100, 2, '.', ''),
                    'b_provisi' => number_format($nilai['provisi'] / $plafon * 100, 2, '.', ''),
                ]);
            }
        });

        return redirect()
            ->to(route('kpmi.amaterasi.show', $kode_pengajuan) . '#tab-administrasi')
            ->with('success', 'Biaya administrasi berhasil disimpan.');
    }

    /**
     * Kode pengguna 3 huruf (users.code_user) untuk kolom input_user pada tabel alur lama.
     */
    private function kodeUser(): ?string
    {
        $kode = Auth::user()->code_user;

        return empty($kode) ? null : $kode;
    }

    private function kembaliKeMemorandum(string $kode_pengajuan, string $subtab, string $pesan, bool $galat = false)
    {
        $alihan = redirect()->to(route('kpmi.amaterasi.show', $kode_pengajuan) . '#' . $subtab);

        return $galat ? $alihan->withErrors(['memorandum' => $pesan]) : $alihan->with('success', $pesan);
    }

    /**
     * Memorandum - kebutuhan dana. Disimpan ke tabel kebutuhan dana alur lama (satu baris per pengajuan);
     * jumlah kebutuhan dana dihitung di server dari lima kebutuhan.
     */
    public function memorandumKebutuhan(Request $request, string $kode_pengajuan)
    {
        $pengajuan = $this->ambilPengajuan($kode_pengajuan);
        $kode = $pengajuan->kode_pengajuan;
        $kodeUser = $this->kodeUser();

        if (! $kodeUser) {
            return $this->kembaliKeMemorandum($kode_pengajuan, 'mm-kebutuhan', 'Kode pengguna tidak ditemukan, kebutuhan dana tidak dapat disimpan.', true);
        }

        $kolom = collect($this->memorandum()['kebutuhan']);
        $nama = $kolom->pluck('label', 'name')->all();

        $angka = [];
        foreach ($kolom->pluck('name') as $n) {
            $digit = preg_replace('/\D/', '', (string) $request->input($n));
            $angka[$n] = $digit === '' ? '0' : $digit;
        }
        $request->merge($angka);

        $aturan = [];
        foreach ($kolom->pluck('name') as $n) {
            $aturan[$n] = ['required', 'integer', 'min:0', 'max:99999999999'];
            $aturan['ket_' . $n] = ['nullable', 'string', 'max:255'];
            $nama['ket_' . $n] = 'Keterangan ' . strtolower($nama[$n]);
        }

        $input = $request->validate($aturan, [
            'integer' => ':attribute harus berupa angka.',
            'max'     => ':attribute terlalu besar atau terlalu panjang.',
        ], $nama);

        $data = ['kebutuhan_dana' => 0];
        foreach ($kolom->pluck('name') as $n) {
            $data[$n] = (int) $input[$n];
            $data['ket_' . $n] = strtoupper((string) ($input['ket_' . $n] ?? ''));
            $data['kebutuhan_dana'] += $data[$n];
        }

        DB::transaction(function () use ($kode, $data, $kodeUser) {
            $ada = DataKebutuhanDana::where('pengajuan_kode', $kode)->first();

            if ($ada) {
                $ada->update($data + ['input_user' => $kodeUser]);
            } else {
                DataKebutuhanDana::create($data + [
                    'kode_analisa'   => Tambahan::kodeacak('AUT', 5),
                    'pengajuan_kode' => $kode,
                    'input_user'     => $kodeUser,
                ]);
            }
        });

        return $this->kembaliKeMemorandum($kode_pengajuan, 'mm-kebutuhan', 'Kebutuhan dana berhasil disimpan.');
    }

    /**
     * Memorandum - usulan kredit. Seperti alur lama, usulan mengubah plafon, tenor, bunga, dan biaya pada data
     * pengajuan (plafon awal dicatat sekali di temp_plafon) dan disimpan di tabel memorandum alur lama.
     * Max plafond dihitung dari kemampuan angsuran pada analisa kredit (lihat kemampuanAngsuran) dan menjadi batas
     * usulan plafond; repayment capacity hanya ditampilkan. Taksasi agunan tidak dipakai karena KPMI tanpa agunan.
     */
    public function memorandumUsulan(Request $request, string $kode_pengajuan)
    {
        $pengajuan = $this->ambilPengajuan($kode_pengajuan);
        $kode = $pengajuan->kode_pengajuan;
        $kodeUser = $this->kodeUser();

        if (! $kodeUser) {
            return $this->kembaliKeMemorandum($kode_pengajuan, 'mm-usulan', 'Kode pengguna tidak ditemukan, usulan kredit tidak dapat disimpan.', true);
        }

        $digit = preg_replace('/\D/', '', (string) $request->input('usulan_plafond'));
        $persen = [];
        foreach (['suku_bunga', 'biaya_admin', 'biaya_provisi', 'biaya_penalti'] as $n) {
            $persen[$n] = str_replace(',', '.', trim((string) $request->input($n)));
        }
        $request->merge(['usulan_plafond' => $digit === '' ? null : $digit] + $persen);

        $input = $request->validate([
            'usulan_plafond'    => ['required', 'integer', 'min:500000', 'max:99999999999'],
            'jangka_waktu'      => ['required', 'integer', 'between:1,99'],
            'suku_bunga'        => ['required', 'numeric', 'between:0,99.99'],
            'biaya_admin'       => ['required', 'numeric', 'between:0,99.99'],
            'biaya_provisi'     => ['required', 'numeric', 'between:0,99.99'],
            'biaya_penalti'     => ['required', 'numeric', 'between:0,99.99'],
            'pengikatan'        => ['required', Rule::in(array_keys($this->opsiPengikatan()))],
            'sebelum_realisasi' => ['nullable', 'string', 'max:512'],
            'syarat_tambahan'   => ['nullable', 'string', 'max:512'],
            'syarat_lainnya'    => ['nullable', 'string', 'max:512'],
        ], [
            'required'            => ':attribute belum diisi.',
            'integer'             => ':attribute harus berupa angka.',
            'numeric'             => ':attribute harus berupa angka.',
            'usulan_plafond.min'  => 'Usulan plafond tidak boleh kurang dari Rp 500.000.',
            'between'             => ':attribute harus antara :min dan :max.',
            'in'                  => ':attribute tidak valid.',
            'max'                 => ':attribute terlalu besar atau terlalu panjang.',
        ], [
            'usulan_plafond' => 'Usulan plafond', 'jangka_waktu' => 'Jangka waktu', 'suku_bunga' => 'S.Bunga',
            'biaya_admin' => 'Biaya admin', 'biaya_provisi' => 'B.Provisi', 'biaya_penalti' => 'B.Penalti',
            'pengikatan' => 'Pengikatan asuransi', 'sebelum_realisasi' => 'Sebelum realisasi',
            'syarat_tambahan' => 'Syarat tambahan', 'syarat_lainnya' => 'Syarat lainnya',
        ]);

        // usulan plafond tidak boleh melebihi max plafond (dari kemampuan angsuran pada analisa kredit)
        $kemampuan = $this->kemampuanAngsuran($pengajuan->analisa);
        $maxPlafond = $kemampuan !== null && $kemampuan > 0
            ? $this->maxPlafond($kemampuan, (float) $input['suku_bunga'], (int) $input['jangka_waktu'])
            : null;

        if ($maxPlafond !== null && (int) $input['usulan_plafond'] > $maxPlafond) {
            throw ValidationException::withMessages([
                'usulan_plafond' => 'Usulan plafond Rp ' . number_format((int) $input['usulan_plafond'], 0, ',', '.')
                    . ' melebihi Max Plafond Rp ' . number_format($maxPlafond, 0, ',', '.')
                    . ' (kemampuan angsuran Rp ' . number_format($kemampuan, 0, ',', '.') . ' per bulan, jangka waktu '
                    . (int) $input['jangka_waktu'] . ' bulan, bunga ' . (float) $input['suku_bunga'] . '%).',
            ]);
        }

        $dua = fn($v) => number_format((float) $v, 2, '.', '');

        DB::transaction(function () use ($kode, $input, $kodeUser, $pengajuan, $dua, $maxPlafond) {
            $dataPengajuan = [
                'plafon'       => (string) (int) $input['usulan_plafond'],
                'jangka_waktu' => (string) (int) $input['jangka_waktu'],
                'suku_bunga'   => rtrim(rtrim($dua($input['suku_bunga']), '0'), '.'),
                'b_admin'      => $dua($input['biaya_admin']),
                'b_provisi'    => $dua($input['biaya_provisi']),
                'b_penalti'    => $dua($input['biaya_penalti']),
            ];

            if (empty($pengajuan->temp_plafon)) {
                $dataPengajuan['temp_plafon'] = (string) (int) $pengajuan->plafon;
            }

            DataPengajuan::where('kode_pengajuan', $kode)->update($dataPengajuan);

            $data = [
                'max_plafond'       => $maxPlafond,
                'usulan_plafond'    => (int) $input['usulan_plafond'],
                'jangka_waktu'      => (string) (int) $input['jangka_waktu'],
                'pengikatan'        => (string) $input['pengikatan'],
                'sebelum_realisasi' => $input['sebelum_realisasi'] ?? null,
                'syarat_tambahan'   => $input['syarat_tambahan'] ?? null,
                'syarat_lainnya'    => $input['syarat_lainnya'] ?? null,
            ];

            $ada = DataMemorandum::where('pengajuan_kode', $kode)->first();

            if ($ada) {
                $ada->update($data + ['input_user' => $kodeUser]);
            } else {
                DataMemorandum::create($data + [
                    'kode_analisa'   => Midle::kodeacak_memorandum('AMO', 5),
                    'pengajuan_kode' => $kode,
                    'by_fiducia'     => 0,
                    'input_user'     => $kodeUser,
                ]);
            }
        });

        return $this->kembaliKeMemorandum($kode_pengajuan, 'mm-usulan', 'Usulan kredit berhasil disimpan.');
    }

    /**
     * Memorandum - sandi lapbul. Seperti alur lama, usulan kredit harus tersimpan lebih dulu karena sandi
     * disimpan pada baris memorandum yang sama.
     */
    public function memorandumSandi(Request $request, string $kode_pengajuan)
    {
        $pengajuan = $this->ambilPengajuan($kode_pengajuan);
        $kode = $pengajuan->kode_pengajuan;
        $kodeUser = $this->kodeUser();

        if (! $kodeUser) {
            return $this->kembaliKeMemorandum($kode_pengajuan, 'mm-sandi', 'Kode pengguna tidak ditemukan, sandi lapbul tidak dapat disimpan.', true);
        }

        $memorandum = DataMemorandum::where('pengajuan_kode', $kode)->first();

        if (! $memorandum) {
            return $this->kembaliKeMemorandum($kode_pengajuan, 'mm-sandi', 'Lengkapi usulan kredit terlebih dahulu.', true);
        }

        $sandi = collect($this->sandiLapbul());
        $input = $request->validate(
            $sandi->mapWithKeys(fn($s) => [$s['name'] => ['required', Rule::exists($s['tabel'], 'sandi')]])->all(),
            ['required' => ':attribute belum dipilih.', 'exists' => ':attribute tidak valid.'],
            $sandi->pluck('label', 'name')->all()
        );

        $data = [];
        foreach ($sandi as $s) {
            $data[$s['kolom']] = (string) $input[$s['name']];
        }

        $memorandum->update($data + ['input_user' => $kodeUser]);

        return $this->kembaliKeMemorandum($kode_pengajuan, 'mm-sandi', 'Sandi lapbul berhasil disimpan.');
    }

    public function cetak(string $kode_pengajuan)
    {
        $pengajuan = $this->ambilPengajuan($kode_pengajuan);
        $kode = $pengajuan->kode_pengajuan;
        $analisa = $pengajuan->analisa;

        if (! $analisa) {
            return redirect()
                ->route('kpmi.amaterasi.show', $kode_pengajuan)
                ->withErrors(['analisa' => 'Analisa belum disimpan, simpan dulu sebelum mencetak.']);
        }

        return view('kpmi.amaterasu.cetak', $this->dataCetak($pengajuan, $analisa));
    }

    /**
     * Data halaman cetak. Urutan kelompok mengikuti file Excel konsep.
     */
    private function dataCetak(DataPengajuan $pengajuan, DataAnalisa $analisa): array
    {
        $konfigurasi = $this->konfigurasi($this->syaratWajib(), $this->syaratPendukung(), $this->penilaian());

        $label = [
            'dsr'    => 'DSR setelah kredit',
            'stress' => 'Sisa penghasilan saat stress test',
        ];
        foreach (array_merge($konfigurasi['syarat'], $konfigurasi['penilaian']) as $k) {
            $label[$k['name']] = $k['label'];
        }

        $kelompok = [
            'Character & SLIK'         => ['identitas_alamat', 'slik', 'keterbukaan', 'dukungan_keluarga'],
            'Kesiapan Siswa'           => ['interview', 'motivasi', 'kognitif', 'disiplin', 'kesehatan'],
            'Kemampuan Bayar Keluarga' => ['pembayar_primer', 'bukti_penghasilan', 'utang_terverifikasi', 'dsr', 'stress'],
            'Program & LPK'            => ['rekam_jejak_lpk', 'paham_program', 'milestone_refund', 'penempatan'],
            'Struktur & Mitigasi'      => ['debitur_bersama', 'mitigasi', 'pencairan_bertahap', 'monitoring', 'dokumen_kredit'],
        ];

        $detail = $analisa->details()->get()->keyBy('kriteria');

        return [
            'pengajuan'   => $pengajuan,
            'analisa'     => $analisa,
            'detail'      => $detail,
            'kelompok'    => $kelompok,
            'label'       => $label,
            'aturan'      => $konfigurasi['aturan'],
            'gateMinimal' => $konfigurasi['aturan']['gate_minimal'],
            'totalBobot'  => $this->totalBobot($konfigurasi),
            'belumTerpenuhi' => $detail->filter(fn($d) => $d->wajib && $d->nilai < $konfigurasi['aturan']['gate_minimal'])
                ->keys()->map(fn($k) => $label[$k])->values()->all(),
        ];
    }

    /**
     * Pengajuan dari kode terenkripsi pada URL. Hanya untuk resort LPK01 (Amaterasu).
     */
    private function ambilPengajuan(string $kode_pengajuan): DataPengajuan
    {
        try {
            $kode = Crypt::decrypt($kode_pengajuan);
        } catch (DecryptException $e) {
            abort(403, 'Permintaan anda di Tolak.');
        }

        $pengajuan = DataPengajuan::getPengajuan($kode);

        if (! $pengajuan || $pengajuan->resort_kode !== 'LPK01') {
            abort(404);
        }

        return $pengajuan;
    }

    /**
     * Angsuran per bulan, metode efektif anuitas, dari plafon, bunga, dan jangka waktu pengajuan.
     */
    private function hitungAngsuran(DataPengajuan $pengajuan): int
    {
        $plafon = (float) $pengajuan->plafon;
        $bunga  = (float) $pengajuan->suku_bunga / 100 / 12;
        $bulan  = (int) $pengajuan->jangka_waktu;

        if ($bunga == 0) {
            return (int) round($plafon / $bulan);
        }

        return (int) round($plafon * $bunga / (1 - pow(1 + $bunga, -$bulan)));
    }

    /**
     * Hitung ulang di server: nilai, skor, syarat wajib dan rekomendasi.
     * Aturannya sama persis dengan kpmi_amaterasu.js.
     */
    private function hitung(array $in, int $angsuran): array
    {
        $konfigurasi = $this->konfigurasi($this->syaratWajib(), $this->syaratPendukung(), $this->penilaian());
        $aturan      = $konfigurasi['aturan'];
        $eps         = 1e-9;

        $penghasilan = (int) $in['penghasilan'];
        $biayaHidup  = (int) $in['biaya_hidup'];
        $seluruhAngsuran = (int) $in['angsuran_berjalan'] + $angsuran;

        $dsr         = $seluruhAngsuran / $penghasilan;
        $sisa        = $penghasilan - $biayaHidup - $seluruhAngsuran;
        $penghasilanStress = $penghasilan * $aturan['stress_penghasilan'];
        $sisaStress  = $penghasilanStress - $biayaHidup * $aturan['stress_biaya'] - $seluruhAngsuran;

        $nilaiDsr = 1;
        foreach ($aturan['dsr'] as [$batas, $nilai]) {
            if ($dsr <= $batas + $eps) {
                $nilaiDsr = $nilai;
                break;
            }
        }

        if ($sisaStress <= 0) {
            $nilaiStress = 1;
        } else {
            $rasio       = $sisaStress / $penghasilanStress;
            $nilaiStress = $rasio >= $aturan['stress_rasio']['nilai_5'] - $eps ? 5
                : ($rasio >= $aturan['stress_rasio']['nilai_4'] - $eps ? 4 : 3);
        }

        $detail = [];
        $baris = function (string $kriteria, ?string $jawaban, int $nilai, int $bobot, bool $wajib) use (&$detail) {
            $detail[] = [
                'kriteria' => $kriteria,
                'jawaban'  => $jawaban,
                'nilai'    => $nilai,
                'bobot'    => $bobot,
                'skor'     => $bobot * $nilai / 5,
                'wajib'    => $wajib,
            ];
        };

        foreach ($konfigurasi['syarat'] as $s) {
            $baris($s['name'], $in[$s['name']], $in[$s['name']] === 'Ya' ? 5 : 0, $s['bobot'], $s['wajib']);
        }
        foreach ($konfigurasi['penilaian'] as $p) {
            $baris($p['name'], null, (int) $in[$p['name']], $p['bobot'], false);
        }
        $baris('dsr', null, $nilaiDsr, $konfigurasi['rc']['dsr']['bobot'], true);
        $baris('stress', null, $nilaiStress, $konfigurasi['rc']['stress']['bobot'], true);

        $skorTotal = round(array_sum(array_column($detail, 'skor')), 2);
        $totalWajib = count(array_filter($detail, fn($d) => $d['wajib']));
        $gagal = count(array_filter($detail, fn($d) => $d['wajib'] && $d['nilai'] < $aturan['gate_minimal']));

        if ($gagal > 0) {
            $rekomendasi = 'TUNDA';
        } elseif ($skorTotal >= $aturan['skor_layak']) {
            $rekomendasi = 'LAYAK DIREKOMENDASIKAN';
        } elseif ($skorTotal >= $aturan['skor_komite']) {
            $rekomendasi = 'DIPERTIMBANGKAN KOMITE';
        } else {
            $rekomendasi = 'TIDAK DIREKOMENDASIKAN';
        }

        return [
            'header' => [
                'dsr'                     => round($dsr, 4),
                'sisa_penghasilan'        => (int) round($sisa),
                'sisa_penghasilan_stress' => (int) round($sisaStress),
                'nilai_dsr'               => $nilaiDsr,
                'nilai_stress'            => $nilaiStress,
                'skor_total'              => $skorTotal,
                'syarat_terpenuhi'        => $totalWajib - $gagal,
                'syarat_total'            => $totalWajib,
                'rekomendasi'             => $rekomendasi,
            ],
            'detail' => $detail,
        ];
    }

    /**
     * Isian rupiah dikirim dengan pemisah ribuan ("9.000.000"); ambil angkanya saja.
     */
    private function rapikanAngka(Request $request): void
    {
        $angka = [];
        foreach (['penghasilan', 'biaya_hidup', 'angsuran_berjalan'] as $nama) {
            $digit = preg_replace('/\D/', '', (string) $request->input($nama));
            $angka[$nama] = $digit === '' ? null : $digit;
        }

        $request->merge($angka);
    }

    private function aturanValidasi(): array
    {
        $aturan = [
            'skor_lendana'      => ['nullable', 'numeric', 'between:0,100'],
            'penghasilan'       => ['required', 'integer', 'min:1'],
            'biaya_hidup'       => ['required', 'integer', 'min:0'],
            'angsuran_berjalan' => ['required', 'integer', 'min:0'],
            'catatan'           => ['nullable', 'string', 'max:2000'],
        ];

        foreach (array_merge($this->syaratWajib(), $this->syaratPendukung()) as $s) {
            $aturan[$s['name']] = ['required', 'in:Ya,Tidak'];
        }
        foreach (collect($this->penilaian())->flatten(1) as $p) {
            $aturan[$p['name']] = ['required', 'integer', 'between:0,5'];
        }

        return $aturan;
    }

    private function pesanValidasi(): array
    {
        return [
            'required' => ':attribute belum diisi.',
            'in'       => ':attribute harus Ya atau Tidak.',
            'integer'  => ':attribute harus berupa angka.',
            'numeric'  => ':attribute harus berupa angka.',
            'min'      => ':attribute tidak boleh kurang dari :min.',
            'between'  => ':attribute harus antara :min dan :max.',
            'max'      => ':attribute terlalu panjang.',
        ];
    }

    private function namaIsian(): array
    {
        $nama = [
            'skor_lendana'      => 'Skor Lendana',
            'penghasilan'       => 'Penghasilan bersih',
            'biaya_hidup'       => 'Biaya hidup',
            'angsuran_berjalan' => 'Angsuran kredit berjalan',
        ];

        foreach (array_merge($this->syaratWajib(), $this->syaratPendukung()) as $s) {
            $nama[$s['name']] = $s['label'];
        }
        foreach (collect($this->penilaian())->flatten(1) as $p) {
            $nama[$p['name']] = $p['label'];
        }

        return $nama;
    }

    /**
     * Nilai awal form: isian terakhir yang gagal divalidasi, atau analisa tersimpan, atau kosong.
     */
    private function isian(?DataAnalisa $analisa): array
    {
        $lama = session()->getOldInput();
        if (! empty($lama)) {
            foreach (['penghasilan', 'biaya_hidup', 'angsuran_berjalan'] as $nama) {
                $lama[$nama] = preg_replace('/\D/', '', (string) ($lama[$nama] ?? ''));
            }
            foreach (collect($this->penilaian())->flatten(1) as $p) {
                if (isset($lama[$p['name']])) {
                    $lama[$p['name']] = (int) $lama[$p['name']];
                }
            }

            return $lama;
        }

        if (! $analisa) {
            return [];
        }

        $isian = [
            'skor_lendana'      => $analisa->skor_lendana,
            'penghasilan'       => $analisa->penghasilan,
            'biaya_hidup'       => $analisa->biaya_hidup,
            'angsuran_berjalan' => $analisa->angsuran_berjalan,
            'catatan'           => $analisa->catatan,
        ];

        foreach ($analisa->details as $detail) {
            $isian[$detail->kriteria] = $detail->jawaban ?? $detail->nilai;
        }

        return $isian;
    }

    /**
     * Tahap yang diringkas pada tab Pengajuan Komite, berurutan seperti tab pada halaman.
     */
    private function tahapAnalisa(): array
    {
        return [
            ['id' => 'tab-analisa', 'nama' => 'Analisa Kredit'],
            ['id' => 'tab-dokumen', 'nama' => 'Analisa Dokumen'],
            ['id' => 'tab-memorandum', 'nama' => 'Memorandum'],
            ['id' => 'tab-administrasi', 'nama' => 'Biaya Administrasi'],
        ];
    }

    /**
     * Kolom tahap 3 (Memorandum). Pilihan sandi lapbul diambil dari tabel sandi bank Indonesia pada sistem lama.
     * tipe: rupiah, persen, angka, teks, area, select.
     */
    private function memorandum(): array
    {
        return [
            'kebutuhan' => [
                ['name' => 'modal_kerja', 'label' => 'Modal Kerja'],
                ['name' => 'investasi', 'label' => 'Investasi'],
                ['name' => 'konsumtif', 'label' => 'Konsumtif'],
                ['name' => 'pelunasan_kredit', 'label' => 'Pelunasan Kredit'],
                ['name' => 'take_over', 'label' => 'Take Over'],
            ],
            'usulan' => [
                'Plafon dan jangka waktu' => [
                    ['name' => 'kebutuhan_dana', 'label' => 'Kebutuhan Dana', 'tipe' => 'rupiah', 'readonly' => true],
                    ['name' => 'max_plafond', 'label' => 'Max Plafond', 'tipe' => 'rupiah', 'readonly' => true],
                    ['name' => 'usulan_plafond', 'label' => 'Usulan Plafond', 'tipe' => 'rupiah'],
                    ['name' => 'jangka_waktu', 'label' => 'Jangka Waktu', 'tipe' => 'angka', 'satuan' => 'bulan'],
                    ['name' => 'repayment_capacity', 'label' => 'Repayment Capacity', 'tipe' => 'persen', 'readonly' => true],
                ],
                'Bunga dan biaya' => [
                    ['name' => 'suku_bunga', 'label' => 'S.Bunga', 'tipe' => 'persen'],
                    ['name' => 'biaya_admin', 'label' => 'Biaya Admin', 'tipe' => 'persen'],
                    ['name' => 'biaya_provisi', 'label' => 'B.Provisi', 'tipe' => 'persen'],
                    ['name' => 'biaya_penalti', 'label' => 'B.Penalti', 'tipe' => 'persen'],
                    ['name' => 'pengikatan', 'label' => 'Pengikatan Asuransi', 'tipe' => 'select', 'opsi' => $this->opsiPengikatan()],
                ],
                'Syarat' => [
                    ['name' => 'sebelum_realisasi', 'label' => 'Sebelum Realisasi', 'tipe' => 'area', 'kelas' => 'text-uppercase'],
                    ['name' => 'syarat_tambahan', 'label' => 'Syarat Tambahan', 'tipe' => 'area', 'kelas' => 'text-uppercase'],
                    ['name' => 'syarat_lainnya', 'label' => 'Syarat Lainnya', 'tipe' => 'area', 'kelas' => 'text-uppercase'],
                ],
            ],
            'sandi' => array_map(fn($s) => [
                'name'  => $s['name'],
                'label' => $s['label'],
                'tipe'  => 'select',
                'kelas' => 'kpmi-select2',
                'opsi'  => $this->opsiSandi($s['tabel']),
            ], $this->sandiLapbul()),
        ];
    }

    /**
     * Sembilan sandi lapbul: nama isian, tabel sandi, dan kolom tujuan pada tabel memorandum lama.
     */
    private function sandiLapbul(): array
    {
        return [
            ['name' => 'sifat', 'label' => 'Sifat', 'tabel' => 'bi_sifat', 'kolom' => 'bi_sifat_kode'],
            ['name' => 'penggunaan_debitur', 'label' => 'Penggunaan Debitur', 'tabel' => 'bi_penggunaan_debitur', 'kolom' => 'bi_penggunaan_kode'],
            ['name' => 'golongan_penjamin', 'label' => 'Golongan Penjamin', 'tabel' => 'bi_golongan_penjamin', 'kolom' => 'bi_gol_penjamin_kode'],
            ['name' => 'sektor_ekonomi', 'label' => 'Sektor Ekonomi', 'tabel' => 'bi_sektor_ekonomi', 'kolom' => 'bi_sek_ekonomi_kode'],
            ['name' => 'sumber_dana_pelunasan', 'label' => 'Sumber Dana Pelunasan', 'tabel' => 'bi_sumber_dana_pelunasan', 'kolom' => 'bi_sumber_pelunasan_kode'],
            ['name' => 'jenis_usaha', 'label' => 'Jenis Usaha', 'tabel' => 'bi_jenis_usaha', 'kolom' => 'bi_jenis_usaha_kode'],
            ['name' => 'sektor_ekonomi_slik', 'label' => 'Sektor Ekonomi (SLIK)', 'tabel' => 'bi_sektor_ekonomi_slik', 'kolom' => 'bi_sek_ekonomi_slik'],
            ['name' => 'golongan_debitur', 'label' => 'Golongan Debitur', 'tabel' => 'bi_golongan_debitur', 'kolom' => 'bi_gol_debitur_kode'],
            ['name' => 'golongan_debitur_slik', 'label' => 'Golongan Debitur (SLIK)', 'tabel' => 'bi_golongan_debitur_slik', 'kolom' => 'bi_gol_debitur_slik'],
        ];
    }

    /** Pilihan "sandi - keterangan" dari tabel sandi sistem lama; kunci berupa teks sandi. */
    private function opsiSandi(string $tabel): array
    {
        $opsi = [];
        foreach (DB::table($tabel)->orderBy('sandi')->get(['sandi', 'keterangan']) as $baris) {
            $opsi[(string) $baris->sandi] = $baris->sandi . ' - ' . $baris->keterangan;
        }

        return $opsi;
    }

    private function opsiPengikatan(): array
    {
        return [1 => 'Tanpa Pengikatan', 2 => 'APHT', 3 => 'Fiducia', 4 => 'APHT dan Fiducia'];
    }

    /**
     * Kolom tahap 4 (Biaya Administrasi). Nama kolom mengikuti tabel administrasi pada alur lama.
     */
    private function administrasi(): array
    {
        return [
            'biaya' => [
                ['name' => 'administrasi', 'label' => 'Administrasi'],
                ['name' => 'provisi', 'label' => 'Provisi'],
                ['name' => 'asuransi_jiwa', 'label' => 'Pertanggungan Asr. Jiwa'],
                ['name' => 'asuransi_kendaraan_motor', 'label' => 'Asr. Kendaraan Bermotor'],
                ['name' => 'materai', 'label' => 'Materai'],
                ['name' => 'asuransi_jiwa_menurun1', 'label' => 'Asr. Jiwa Menurun 1'],
                ['name' => 'transaksi_kredit', 'label' => 'Transaksi Kredit'],
                ['name' => 'proses_shm', 'label' => 'Proses SHM'],
                ['name' => 'asuransi_jiwa_menurun2', 'label' => 'Asr. Jiwa Menurun 2'],
                ['name' => 'asuransi_jiwa_menurun3', 'label' => 'Asr. Jiwa Menurun 3'],
                ['name' => 'polis_materai', 'label' => 'Polis dan Materai'],
                ['name' => 'pajak_stnk', 'label' => 'Pajak STNK'],
                ['name' => 'asuransi_jiwa_tetap1', 'label' => 'Asr. Jiwa Tetap 1'],
                ['name' => 'asuransi_jiwa_tetap2', 'label' => 'Asr. Jiwa Tetap 2'],
                ['name' => 'proses_apht', 'label' => 'Proses APHT', 'readonly' => true],
                ['name' => 'biaya_fiducia', 'label' => 'Biaya Fiducia', 'readonly' => true],
            ],
        ];
    }

    /**
     * Nilai awal kolom tahap 3 dan 4 dari data pengajuan. Biaya administrasi dan provisi dihitung
     * dari plafon x persen, mengikuti pola contoh layar sistem lama (40 juta: 2,5% = 1.000.000; 1% = 400.000).
     */
    private function nilaiAwalTahap(DataPengajuan $pengajuan, ?DataAnalisa $analisa, int $angsuran): array
    {
        $plafon = (float) $pengajuan->plafon;

        $awal = [
            'usulan_plafond' => (int) $plafon,
            'jangka_waktu'   => (int) $pengajuan->jangka_waktu,
            'suku_bunga'     => (float) $pengajuan->suku_bunga,
            'biaya_admin'    => (float) $pengajuan->b_admin,
            'biaya_provisi'  => (float) $pengajuan->b_provisi,
            'biaya_penalti'  => (float) $pengajuan->b_penalti,
            'administrasi'   => (int) round($plafon * (float) $pengajuan->b_admin / 100),
            'provisi'        => (int) round($plafon * (float) $pengajuan->b_provisi / 100),
        ];

        // max plafond dan repayment capacity dari kemampuan angsuran pada analisa kredit
        $kemampuan = $this->kemampuanAngsuran($analisa);
        if ($kemampuan !== null && $kemampuan > 0) {
            $awal['max_plafond'] = $this->maxPlafond($kemampuan, (float) $pengajuan->suku_bunga, (int) $pengajuan->jangka_waktu);
            $awal['repayment_capacity'] = number_format($angsuran / $kemampuan * 100, 2, '.', '');
        }

        // biaya administrasi yang sudah disimpan menggantikan nilai awal
        if ($pengajuan->administrasi) {
            foreach ($this->administrasi()['biaya'] as $f) {
                if (isset($pengajuan->administrasi->{$f['name']})) {
                    $awal[$f['name']] = (int) $pengajuan->administrasi->{$f['name']};
                }
            }
        }

        // kebutuhan dana yang sudah disimpan
        if ($pengajuan->kebutuhanDana) {
            $dana = $pengajuan->kebutuhanDana;
            foreach ($this->memorandum()['kebutuhan'] as $f) {
                $awal[$f['name']] = (int) $dana->{$f['name']};
                $awal['ket_' . $f['name']] = (string) $dana->{'ket_' . $f['name']};
            }
            $awal['kebutuhan_dana'] = (int) $dana->kebutuhan_dana;
            $awal['jumlah_kebutuhan_dana'] = (int) $dana->kebutuhan_dana;
        }

        // usulan kredit dan sandi lapbul yang sudah disimpan
        $awal['pengikatan'] = '1';
        if ($pengajuan->memorandum) {
            $memo = $pengajuan->memorandum;
            $awal['usulan_plafond'] = (int) $memo->usulan_plafond;
            $awal['pengikatan'] = (string) ($memo->pengikatan ?: '1');
            foreach (['sebelum_realisasi', 'syarat_tambahan', 'syarat_lainnya'] as $nama) {
                $awal[$nama] = (string) $memo->{$nama};
            }
            foreach ($this->sandiLapbul() as $s) {
                $awal[$s['name']] = (string) $memo->{$s['kolom']};
            }
        }

        return $awal;
    }

    /**
     * Kemampuan angsuran per bulan untuk KPMI: penghasilan dikurangi biaya hidup dan angsuran kredit berjalan,
     * dari analisa kredit yang tersimpan. Ini pengganti "keuangan per bulan" pada alur lama. Null jika analisa belum ada.
     */
    private function kemampuanAngsuran(?DataAnalisa $analisa): ?int
    {
        if (! $analisa) {
            return null;
        }

        return (int) $analisa->penghasilan - (int) $analisa->biaya_hidup - (int) $analisa->angsuran_berjalan;
    }

    /**
     * Max plafond metode efektif anuitas, sama dengan alur lama: kemampuan angsuran dibagi faktor anuitas.
     */
    private function maxPlafond(int $kemampuan, float $bungaTahun, int $tenor): int
    {
        $bunga = $bungaTahun / 100 / 12;

        if ($tenor <= 0) {
            return 0;
        }

        if ($bunga == 0) {
            return (int) round($kemampuan * $tenor);
        }

        return (int) round($kemampuan * (1 - pow(1 + $bunga, -$tenor)) / $bunga);
    }

    /** Kelengkapan tiga bagian memorandum untuk resume tab Pengajuan Komite. */
    private function statusMemorandum(DataPengajuan $pengajuan): array
    {
        $memo = $pengajuan->memorandum;

        return [
            'kebutuhan' => (bool) $pengajuan->kebutuhanDana,
            'usulan'    => (bool) $memo,
            'sandi'     => $memo && $memo->bi_sifat_kode !== null && $memo->bi_sifat_kode !== '',
        ];
    }

    /**
     * Dokumen pendukung yang diunggah analis.
     * Format dan batas ukuran adalah angka awal, sesuaikan jika kebijakan berbeda.
     */
    private function dokumenPendukung(): array
    {
        return [
            'jenis'  => ['pdf', 'jpg', 'jpeg', 'png'],
            'maks_mb' => 5,
            'daftar' => [
                ['name' => 'ktp', 'label' => 'KTP'],
                ['name' => 'surat_rekomendasi', 'label' => 'Surat Rekomendasi Amaterasu'],
                ['name' => 'hasil_mcu', 'label' => 'Hasil MCU'],
                ['name' => 'parameter_scoring_lendana', 'label' => 'Dokumen parameter scoring Lendana'],
                ['name' => 'hasil_slik', 'label' => 'Hasil SLIK (CLIK)'],
                ['name' => 'hasil_tes_psikologi', 'label' => 'Hasil tes psikologi'],
                ['name' => 'scoring_interview', 'label' => 'Scoring interview'],
            ],
        ];
    }

    private function syaratWajib(): array
    {
        return [
            ['name' => 'pembayar_primer', 'label' => 'Pihak pembayar primer sudah ditetapkan', 'bobot' => 5],
            ['name' => 'bukti_penghasilan', 'label' => 'Bukti penghasilan sudah diverifikasi', 'bobot' => 8],
            ['name' => 'utang_terverifikasi', 'label' => 'Pengeluaran dan seluruh utang sudah diverifikasi', 'bobot' => 6],
            ['name' => 'debitur_bersama', 'label' => 'Debitur Bersama bersedia tanggung renteng', 'bobot' => 5],
            ['name' => 'paham_program', 'label' => 'Orang tua/siswa memahami program dan dana talang', 'bobot' => 4],
        ];
    }

    private function syaratPendukung(): array
    {
        return [
            ['name' => 'milestone_refund', 'label' => 'Tahapan pencairan dan refund sudah disepakati', 'bobot' => 3],
            ['name' => 'pencairan_bertahap', 'label' => 'Pencairan bertahap sudah disetujui', 'bobot' => 3],
        ];
    }

    private function penilaian(): array
    {
        return [
            'Character & SLIK' => [
                ['name' => 'identitas_alamat', 'label' => 'Konsistensi identitas dan alamat', 'bobot' => 3],
                ['name' => 'slik', 'label' => 'Riwayat pembayaran/SLIK siswa', 'bobot' => 5],
                ['name' => 'keterbukaan', 'label' => 'Keterbukaan informasi dan pemahaman awal', 'bobot' => 4],
                ['name' => 'dukungan_keluarga', 'label' => 'Dukungan keluarga yang dikonfirmasi', 'bobot' => 3],
            ],
            'Kesiapan Siswa' => [
                ['name' => 'interview', 'label' => 'Hasil interview', 'bobot' => 4],
                ['name' => 'motivasi', 'label' => 'Motivasi dan komitmen', 'bobot' => 4],
                ['name' => 'kognitif', 'label' => 'Kemampuan kognitif dan belajar', 'bobot' => 4],
                ['name' => 'disiplin', 'label' => 'Disiplin, ketekunan dan daya tahan', 'bobot' => 5],
                ['name' => 'kesehatan', 'label' => 'Kesehatan untuk bekerja', 'bobot' => 3],
            ],
            'Program & LPK' => [
                ['name' => 'rekam_jejak_lpk', 'label' => 'Kualitas/rekam jejak LPK', 'bobot' => 5],
                ['name' => 'penempatan', 'label' => 'Kejelasan penempatan/refinancing', 'bobot' => 3],
            ],
            'Struktur & Mitigasi' => [
                ['name' => 'mitigasi', 'label' => 'Agunan, asuransi atau dana darurat', 'bobot' => 3],
                ['name' => 'monitoring', 'label' => 'Monitoring kemajuan siswa', 'bobot' => 2],
                ['name' => 'dokumen_kredit', 'label' => 'Kelengkapan dokumen kredit', 'bobot' => 2],
            ],
        ];
    }

    private function totalBobot(array $konfigurasi): int
    {
        return collect($konfigurasi['syarat'])->sum('bobot')
            + collect($konfigurasi['penilaian'])->sum('bobot')
            + $konfigurasi['rc']['dsr']['bobot']
            + $konfigurasi['rc']['stress']['bobot'];
    }

    /**
     * Konfigurasi hitung yang dibaca JavaScript (bobot, syarat wajib, ambang nilai dan keputusan).
     */
    private function konfigurasi(array $syaratWajib, array $syaratPendukung, array $penilaian): array
    {
        $syarat = [];
        foreach ($syaratWajib as $s) {
            $syarat[] = $s + ['wajib' => true];
        }
        foreach ($syaratPendukung as $s) {
            $syarat[] = $s + ['wajib' => false];
        }

        $aturan = [
            'gate_minimal'       => 3,
            'nilai_maks'         => 5,
            // [nilai, arti]; ditampilkan sebagai petunjuk pengisian nilai
            'skala'              => [[0, 'data belum tersedia'], [3, 'cukup'], [5, 'sangat baik']],
            'skor_layak'         => 80,
            'skor_komite'        => 70,
            'stress_penghasilan' => 0.7,
            'stress_biaya'       => 1.1,
            // [batas DSR maksimal, nilai]; di atas batas terakhir bernilai 1
            'dsr'                => [[0.35, 5], [0.40, 4], [0.45, 3], [0.50, 2]],
            'stress_rasio'       => ['nilai_5' => 0.20, 'nilai_4' => 0.10],
        ];

        // DSR tertinggi yang masih memenuhi syarat wajib, untuk teks syarat
        $batasDsr = collect($aturan['dsr'])->firstWhere(1, $aturan['gate_minimal'])[0] * 100;

        return [
            'syarat'    => $syarat,
            'penilaian' => collect($penilaian)->flatten(1)->values()->all(),
            'rc' => [
                'dsr'    => ['label' => 'DSR setelah kredit maksimal ' . round($batasDsr) . '%', 'bobot' => 8],
                'stress' => ['label' => 'Sisa penghasilan saat keadaan memburuk harus positif', 'bobot' => 8],
            ],
            'aturan' => $aturan,
        ];
    }
}
