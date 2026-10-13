<?php

namespace App\Http\Controllers\KPMI;

use App\Http\Controllers\Controller;
use App\Models\KPMI\DataPengajuan;
use Illuminate\Support\Facades\Crypt;

class AmaterasuController extends Controller
{
    public function show(string $kode_pengajuan)
    {
        // $kode = Crypt::decrypt($kode_pengajuan);
        $kode = '00361336';
        $pengajuan = DataPengajuan::getPengajuan($kode);
        // dd($pengajuan);

        $syaratWajib     = $this->syaratWajib();
        $syaratPendukung = $this->syaratPendukung();
        $penilaian       = $this->penilaian();

        return view('kpmi.amaterasu.show', [
            'pengajuan'       => $pengajuan,
            'syaratWajib'     => $syaratWajib,
            'syaratPendukung' => $syaratPendukung,
            'penilaian'       => $penilaian,
            'totalIsian'      => 3 + count($syaratWajib) + count($syaratPendukung) + collect($penilaian)->flatten(1)->count(),
            'angsuran'        => $this->hitungAngsuran($pengajuan),
            'konfigurasi'     => $this->konfigurasi($syaratWajib, $syaratPendukung, $penilaian),
            'contoh'          => $this->contoh(),
        ]);
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

    /**
     * SEMENTARA: nilai statis untuk melihat hasil perhitungan (mengikuti contoh pada file Excel konsep).
     * Hapus saat form sudah memakai data tersimpan.
     */
    private function contoh(): array
    {
        return [
            'skor_lendana'      => 97.4,
            'penghasilan'       => 9000000,
            'biaya_hidup'       => 1000000,
            'angsuran_berjalan' => 0,

            'pembayar_primer'     => 'Ya',
            'bukti_penghasilan'   => 'Ya',
            'utang_terverifikasi' => 'Ya',
            'debitur_bersama'     => 'Ya',
            'paham_program'       => 'Ya',
            'milestone_refund'    => 'Ya',
            'pencairan_bertahap'  => 'Ya',

            'identitas_alamat'  => 3,
            'slik'              => 5,
            'keterbukaan'       => 2,
            'dukungan_keluarga' => 1,
            'interview'         => 4,
            'motivasi'          => 4,
            'kognitif'          => 3,
            'disiplin'          => 2,
            'kesehatan'         => 4,
            'rekam_jejak_lpk'   => 5,
            'penempatan'        => 2,
            'mitigasi'          => 0,
            'monitoring'        => 2,
            'dokumen_kredit'    => 2,
        ];
    }
}
