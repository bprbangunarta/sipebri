<?php

namespace App\Http\Controllers\KPMI;

use App\Http\Controllers\Controller;
use App\Models\KPMI\DataAnalisa;
use App\Models\KPMI\DataPengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class AmaterasuController extends Controller
{
    public function show(string $kode_pengajuan)
    {
        $kode = $this->kodePengajuan($kode_pengajuan);
        $pengajuan = DataPengajuan::getPengajuan($kode);
        // dd($pengajuan);

        $analisa = $pengajuan->analisa;

        $syaratWajib     = $this->syaratWajib();
        $syaratPendukung = $this->syaratPendukung();
        $penilaian       = $this->penilaian();

        return view('kpmi.amaterasu.show', [
            'pengajuan'       => $pengajuan,
            'analisa'         => $analisa,
            'kodeRoute'       => $kode_pengajuan,
            'syaratWajib'     => $syaratWajib,
            'syaratPendukung' => $syaratPendukung,
            'penilaian'       => $penilaian,
            'totalIsian'      => 3 + count($syaratWajib) + count($syaratPendukung) + collect($penilaian)->flatten(1)->count(),
            'angsuran'        => $this->hitungAngsuran($pengajuan),
            'konfigurasi'     => $this->konfigurasi($syaratWajib, $syaratPendukung, $penilaian),
            'isian'           => $this->isian($analisa),
        ]);
    }

    public function store(Request $request, string $kode_pengajuan)
    {
        $kode = $this->kodePengajuan($kode_pengajuan);
        $pengajuan = DataPengajuan::getPengajuan($kode);

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

    private function kodePengajuan(string $kode_pengajuan): string
    {
        // return Crypt::decrypt($kode_pengajuan);
        return '00361336';
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

        return [
            'syarat'    => $syarat,
            'penilaian' => collect($penilaian)->flatten(1)->values()->all(),
            'rc' => [
                'dsr'    => ['label' => 'DSR setelah kredit maksimal 45%', 'bobot' => 8],
                'stress' => ['label' => 'Sisa penghasilan saat keadaan memburuk harus positif', 'bobot' => 8],
            ],
            'aturan' => [
                'gate_minimal'      => 3,
                'skor_layak'        => 80,
                'skor_komite'       => 70,
                'stress_penghasilan' => 0.7,
                'stress_biaya'      => 1.1,
                // [batas DSR maksimal, nilai]; di atas batas terakhir bernilai 1
                'dsr'               => [[0.35, 5], [0.40, 4], [0.45, 3], [0.50, 2]],
                'stress_rasio'      => ['nilai_5' => 0.20, 'nilai_4' => 0.10],
            ],
        ];
    }
}
