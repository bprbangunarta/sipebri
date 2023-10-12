<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Data extends Model
{
    use HasFactory;

    public static function agama($cek)
    {
        if ($cek == "1") {
            return 'Islam';
        } elseif ($cek == "2") {
            return 'Katolik';
        } elseif ($cek == "3") {
            return 'Kristen';
        } elseif ($cek == "4") {
            return 'Hindu';
        } elseif ($cek == "5") {
            return 'Budha';
        } elseif ($cek == "6") {
            return 'Kong Hu Cu';
        }
    }

    public static function identitas($identitas)
    {
        if ($identitas == "1") {
            return 'KTP';
        } elseif ($identitas == "2") {
            return 'SIM';
        } elseif ($identitas == "3") {
            return 'Passport';
        } elseif ($identitas == "9") {
            return 'Lainnya';
        }
    }

    public static function jk($jk)
    {
        if ($jk == "1") {
            return 'Pria';
        } elseif ($jk == "2") {
            return 'Wanita';
        }
    }

    public static function warganegara($wn)
    {
        if ($wn == "WNI") {
            return 'Warga Negara Indonesia';
        } elseif ($wn == "WNA") {
            return 'Warga Negara Asing';
        }
    }

    public static function status($status)
    {
        if ($status == "M") {
            return 'Menikah';
        } elseif ($status == "L") {
            return 'Lajang';
        } elseif ($status == "D") {
            return 'Duda';
        } elseif ($status == "J") {
            return 'Janda';
        }
    }

    public static function dana($dana)
    {
        if ($dana == "1") {
            return 'Hibah';
        } elseif ($dana == "2") {
            return 'Lain2';
        } elseif ($dana == "3") {
            return 'Penghasilan';
        } elseif ($dana == "4") {
            return 'Warisan';
        }
    }

    public static function penghasilanutama($utama)
    {
        if ($utama == "1") {
            return 's/d 2,5 jt';
        } elseif ($utama == "2") {
            return 's/d 2,5 - 5 jt';
        } elseif ($utama == "3") {
            return 's/d 5 - 7,5 jt';
        } elseif ($utama == "4") {
            return 's/d 7,5 - 10 jt';
        } elseif ($utama == "5") {
            return '10 jt';
        }
    }

    public static function penghasilanlain($lain)
    {
        if ($lain == "1") {
            return 's/d 2,5 jt';
        } elseif ($lain == "2") {
            return 's/d 2,5 - 5 jt';
        } elseif ($lain == "3") {
            return 's/d 5 - 7,5 jt';
        } elseif ($lain == "4") {
            return 's/d 7,5 - 10 jt';
        } elseif ($lain == "5") {
            return '10 jt';
        }
    }

    public static function tanggungan($tanggungan)
    {
        if ($tanggungan == "0") {
            return 'Tidak Ada';
        } elseif ($tanggungan == "1") {
            return '1 Orang';
        } elseif ($tanggungan == "2") {
            return '2 Orang';
        } elseif ($tanggungan == "3") {
            return '3 Orang';
        } elseif ($tanggungan == "4") {
            return '4 Orang';
        } elseif ($tanggungan == "5") {
            return '5 Orang';
        }
    }

    public static function metode($metode)
    {
        if ($metode == "FLAT") {
            return 'Flat';
        } elseif ($metode == "PRK") {
            return 'PRK';
        } elseif ($metode == "Efektif") {
            return 'Efektif';
        } elseif ($metode == "Efektif Anuitas") {
            return 'Efektif Anuitas';
        } elseif ($metode == "Efektif Musiman") {
            return 'Efektif Musiman';
        }
    }

    public static function analisa5c_text($data)
    {
        if ($data == "Baik") {
            return  "3";
        } elseif ($data == "Cukup Baik") {
            return "2";
        } elseif ($data == "Kurang Baik") {
            return "1";
        }
    }

    public static function analisa5c_number($data)
    {
        if ($data == "3") {
            return  "Baik";
        } elseif ($data == "2") {
            return "Cukup Baik";
        } elseif ($data == "1") {
            return "Kurang Baik";
        }
    }

    public static function namaharta($data)
    {
        $harta = [
            'rumah' => 'Rumah',
            'mobil' => 'Mobil',
            'motor' => 'Motor',
            'televisi' => 'Televisi',
            'komputer' => 'Komputer',
            'mesin_cuci' => 'Mesin Cuci',
            'kursi_tamu' => 'Kursi Tamu',
            'lemari_panjang' => 'Lemari Panjang',
            'nama_lainnya1' => $data->nama_lainnya1 ?? null,
            'nama_lainnya2' => $data->nama_lainnya2 ?? null,
        ];

        return $harta;
    }

    public static function cetak_a5c_character($cek)
    {
        $data = [
            'gaya_hidup' => self::analisa5c_number($cek->gaya_hidup),
            'pengendalian_emosi' => self::analisa5c_number($cek->pengendalian_emosi),
            'perbuatan_tercela' => self::analisa5c_number($cek->perbuatan_tercela),
            'harmonis' => self::analisa5c_number($cek->harmonis),
            'konsisten' => self::analisa5c_number($cek->konsisten),
            'gaya_hidup' => self::analisa5c_number($cek->gaya_hidup),
            'kepatuhan' => self::analisa5c_number($cek->kepatuhan),
            'hubungan_sosial' => self::analisa5c_number($cek->hubungan_sosial),
            'nilai_karakter' => self::analisa5c_number($cek->nilai_karakter),
        ];

        return $data;
    }

    public static function data_cetak_a5c_capacity($data)
    {
        if ($data[0] == 'kontinuitas') {
            if ($data[1] == 1) {
                return 'Tidak Tentu';
            } else if ($data[1] == 2) {
                return 'Kadang-kadang';
            } else if ($data[1] == 3) {
                return 'Terus Menerus';
            }
        }

        if ($data[0] == 'pertumbuhan_usaha') {
            if ($data[1] == 1) {
                return 'Turun';
            } else if ($data[1] == 2) {
                return 'Tetap';
            } else if ($data[1] == 3) {
                return 'Meningkat';
            }
        }

        if ($data[0] == 'laporan_keuangan') {
            if ($data[1] == 1) {
                return 'Tidak Ada';
            } else if ($data[1] == 2) {
                return 'Transaksi Harian';
            } else if ($data[1] == 3) {
                return 'Mengumpulkan Bukti';
            }
        }

        if ($data[0] == 'catatan_kredit') {
            if ($data[1] == 1) {
                return 'Menunggak > 2 Bulan';
            } else if ($data[1] == 2) {
                return 'Lancar Menunggak 2 Bulan';
            } else if ($data[1] == 3) {
                return 'Lancar';
            }
        }

        if ($data[0] == 'kondisi_slik') {
            if ($data[1] == 1) {
                return 'Tidak Baik';
            } else if ($data[1] == 2) {
                return 'Tidak Ada';
            } else if ($data[1] == 3) {
                return 'Lancar';
            }
        }

        if ($data[0] == 'aset_diluar_usaha') {
            if ($data[1] == 1) {
                return 'Tidak Liquid';
            } else if ($data[1] == 2) {
                return 'Cukup Liquid';
            } else if ($data[1] == 3) {
                return 'Liquid';
            }
        }

        if ($data[0] == 'aset_terkait_usaha') {
            if ($data[1] == 1) {
                return 'Tidak Mengcover';
            } else if ($data[1] == 2) {
                return 'Cukup Mengcover';
            } else if ($data[1] == 3) {
                return 'Mengcover';
            }
        }

        if ($data[0] == 'capital_sumber_modal') {
            if ($data[1] == 1) {
                return 'Pihak Lain';
            } else if ($data[1] == 2) {
                return 'Kerjasama';
            } else if ($data[1] == 3) {
                return 'Modal Sendiri';
            }
        }

        if ($data[0] == 'pengalaman_usaha') {
            if ($data[1] == 1) {
                return '0 Tahun';
            } else if ($data[1] == 2) {
                return '< 1 Tahun';
            } else if ($data[1] == 3) {
                return '1 - 3 Tahun';
            } else if ($data[1] == 4) {
                return '> 3 - 5 Tahun';
            } else if ($data[1] == 5) {
                return '> 5 Tahun';
            }
        }
    }

    public static function cetak_a5c_capacity($cek)
    {

        if ($cek->pengalaman_usaha == 1) {
            $pu = 'Tidak Baik';
        } elseif ($cek->pengalaman_usaha == 2) {
            $pu = 'Kurang Baik';
        } elseif ($cek->pengalaman_usaha == 3) {
            $pu = 'Cukup Baik';
        } elseif ($cek->pengalaman_usaha == 4) {
            $pu = 'Baik';
        } elseif ($cek->pengalaman_usaha == 5) {
            $pu = 'Sangat Baik';
        }

        $data = [
            'kontinuitas' => [self::data_cetak_a5c_capacity(['kontinuitas', $cek->kontinuitas]), self::analisa5c_number($cek->kontinuitas)],
            'pengalaman_usaha' => [self::data_cetak_a5c_capacity(['pengalaman_usaha', $cek->pengalaman_usaha]), $pu],
            'pertumbuhan_usaha' => [self::data_cetak_a5c_capacity(['pertumbuhan_usaha', $cek->pertumbuhan_usaha]), self::analisa5c_number($cek->pertumbuhan_usaha)],
            'laporan_keuangan' => [self::data_cetak_a5c_capacity(['laporan_keuangan', $cek->laporan_keuangan]), self::analisa5c_number($cek->laporan_keuangan)],
            'catatan_kredit' => [self::data_cetak_a5c_capacity(['catatan_kredit', $cek->catatan_kredit]), self::analisa5c_number($cek->catatan_kredit)],
            'kondisi_slik' => [self::data_cetak_a5c_capacity(['kondisi_slik', $cek->kondisi_slik]), self::analisa5c_number($cek->kondisi_slik)],
            'aset_diluar_usaha' => [self::data_cetak_a5c_capacity(['aset_diluar_usaha', $cek->aset_diluar_usaha]), self::analisa5c_number($cek->aset_diluar_usaha)],
            'aset_terkait_usaha' => [self::data_cetak_a5c_capacity(['aset_terkait_usaha', $cek->aset_terkait_usaha]), self::analisa5c_number($cek->aset_terkait_usaha)],
            'capital_sumber_modal' => [self::data_cetak_a5c_capacity(['capital_sumber_modal', $cek->capital_sumber_modal]), self::analisa5c_number($cek->capital_sumber_modal)],
            'capital_evaluasi_capital' => self::analisa5c_number($cek->capital_evaluasi_capital),
            'rc' => $cek->rc,
            'evaluasi_capacity' => self::analisa5c_number($cek->evaluasi_capacity),
        ];
        return $data;
    }

    public static function data_cetak_a5c_collateral($data)
    {
        if ($data[0] == 'agunan_utama' || $data[0] == 'agunan_tambahan' || $data[0] == 'legalitas_agunan_utama' || $data[0] == 'legalitas_agunan_tambahan') {
            if ($data[1] == 1) {
                return 'Orang Lain/Milik Sendiri dan Orang Lain (Wariasan)';
            } else if ($data[1] == 3) {
                return 'Milik Sendiri';
            }
        }

        if ($data[0] == 'mudah_diuangkan') {
            if ($data[1] == 1) {
                return 'Lainnya';
            } else if ($data[1] == 2) {
                return 'BPKB, SHM';
            } else if ($data[1] == 3) {
                return 'Deposito, Tabungan, Emas';
            }
        }

        if ($data[0] == 'stabilitas_harga') {
            if ($data[1] == 1) {
                return 'BPKB';
            } else if ($data[1] == 2) {
                return 'Deposito, Tabungan, Emas';
            } else if ($data[1] == 3) {
                return 'SHM';
            }
        }

        if ($data[0] == 'kondisi_kendaraan') {
            if ($data[1] == 1) {
                return 'Tidak Original, Tidak Lengakp, Cacat';
            } else if ($data[1] == 2) {
                return 'Original, Tidak Lengakp';
            } else if ($data[1] == 3) {
                return 'Original, Lengakp, Tidak Cacat';
            }
        }

        if ($data[0] == 'lokasi_shm') {
            if ($data[1] == 1) {
                return 'Kurang Strategis dan Kurang Produktif';
            } else if ($data[1] == 2) {
                return 'Strategis dan Produktif (Atau Sebaliknya)';
            } else if ($data[1] == 3) {
                return 'Strategis dan atau Produktif';
            }
        }

        if ($data[0] == 'aspek_hukum') {
            if ($data[1] == 1) {
                return 'Agunan lain yang tidak memenuhisyarat';
            } else if ($data[1] == 2) {
                return 'AJB/ SPOP (dilengkapi dengan SPPT tahun berjalan atau 1 tahun yang lalu) tanpa pengikatan hak';
            } else if ($data[1] == 3) {
                return 'SHM (dilengkapi dengan SPPT tahun berjalan atau 1 tahun yang lalu) / BPKB tanpa pengikatan';
            } else if ($data[1] == 4) {
                return 'SHM (dilengkapi dengan SPPT tahun berjalan atau 1 tahun yang lalu) diikat dengan hak tanggungan / BPKB (Kendaraan) diikat dengan fidusia';
            } else if ($data[1] == 5) {
                return 'Emas dan deposito/tabungan yang saldonya di blokir dan dilengkapi dengan surat kuasa pencairan';
            }
        }
    }

    public static function cetak_a5c_collateral($cek)
    {

        if ($cek->aspek_hukum == 1) {
            $ah = 'Tidak Baik';
        } else if ($cek->aspek_hukum == 2) {
            $ah = 'Kurang Baik';
        } else if ($cek->aspek_hukum == 3) {
            $ah = 'Cukup Baik';
        } else if ($cek->aspek_hukum == 4) {
            $ah = 'Baik';
        } else if ($cek->aspek_hukum == 5) {
            $ah = 'Sangat Baik';
        }

        $data = [
            'agunan_utama' => [self::data_cetak_a5c_collateral(['agunan_utama', $cek->agunan_utama]) ?? null, self::analisa5c_number($cek->agunan_utama) ?? null] ?? null,
            'agunan_tambahan' => [self::data_cetak_a5c_collateral(['agunan_tambahan', $cek->agunan_tambahan]), self::analisa5c_number($cek->agunan_tambahan)],
            'legalitas_agunan_utama' => [self::data_cetak_a5c_collateral(['legalitas_agunan_utama', $cek->legalitas_agunan]), self::analisa5c_number($cek->legalitas_agunan)],
            'legalitas_agunan_tambahan' => [self::data_cetak_a5c_collateral(['legalitas_agunan_tambahan', $cek->legalitas_agunan_tambahan]), self::analisa5c_number($cek->legalitas_agunan_tambahan)],
            'mudah_diuangkan' => [self::data_cetak_a5c_collateral(['mudah_diuangkan', $cek->mudah_diuangkan]), self::analisa5c_number($cek->mudah_diuangkan)],
            'stabilitas_harga' => [self::data_cetak_a5c_collateral(['stabilitas_harga', $cek->stabilitas_harga]), self::analisa5c_number($cek->stabilitas_harga)],
            'kondisi_kendaraan' => [self::data_cetak_a5c_collateral(['kondisi_kendaraan', $cek->kondisi_kendaraan]), self::analisa5c_number($cek->kondisi_kendaraan)],
            'lokasi_shm' => [self::data_cetak_a5c_collateral(['lokasi_shm', $cek->lokasi_shm]), self::analisa5c_number($cek->lokasi_shm)],
            'aspek_hukum' => [self::data_cetak_a5c_collateral(['aspek_hukum', $cek->aspek_hukum]), $ah],
            'taksasi_agunan' => $cek->taksasi_agunan,
        ];

        return $data;
    }

    public static function data_cetak_a5c_condition($data)
    {
        if ($data[0] == 'kondisi_alam') {
            if ($data[1] == 1) {
                return 'Resiko Sangat Rendah';
            } else if ($data[1] == 2) {
                return 'Resiko Sedang';
            } else if ($data[1] == 3) {
                return 'Resiko Rendah';
            } else if ($data[1] == 4) {
                return 'Resiko Tinggi';
            } else if ($data[1] == 5) {
                return 'Resiko Sangat Tinggi';
            }
        }

        if ($data[0] == 'persaingan_usaha') {
            if ($data[1] == 1) {
                return 'Persaingan Usaha Ketat';
            } else if ($data[1] == 2) {
                return 'Persaingan Usaha Kurang Ketat';
            } else if ($data[1] == 3) {
                return 'Persaingan Usaha Tidak Ketat';
            }
        }

        if ($data[0] == 'regulasi_pemerintah') {
            if ($data[1] == 1) {
                return 'Tidak Mendukung';
            } else if ($data[1] == 2) {
                return 'Mendukung';
            } else if ($data[1] == 3) {
                return 'Persaingan Usaha Tidak Ketat';
            }
        }
    }

    public static function cetak_a5c_condition($cek)
    {
        if ($cek->kondisi_alam == 1) {
            $ka = 'Tidak Baik';
        } else if ($cek->kondisi_alam == 2) {
            $ka = 'Kurang Baik';
        } else if ($cek->kondisi_alam == 3) {
            $ka = 'Cukup Baik';
        } else if ($cek->kondisi_alam == 4) {
            $ka = 'Baik';
        } else if ($cek->kondisi_alam == 5) {
            $ka = 'Sangat Baik';
        }

        $data = [
            'kondisi_alam' => [self::data_cetak_a5c_condition(['kondisi_alam', $cek->kondisi_alam]), $ka],
            'persaingan_usaha' => [self::data_cetak_a5c_condition(['persaingan_usaha', $cek->persaingan_usaha]), self::analisa5c_number($cek->persaingan_usaha)],
            'regulasi_pemerintah' => [self::data_cetak_a5c_condition(['regulasi_pemerintah', $cek->regulasi_pemerintah]), self::analisa5c_number($cek->regulasi_pemerintah)],
        ];
        return $data;
    }

    public static function cekkualitatif($cek)
    {
        if ($cek->bi_checking == 1) {
            $bi = 'Macet';
        } else if ($cek->bi_checking == 2) {
            $bi = 'Diragukan';
        } else if ($cek->bi_checking == 3) {
            $bi = 'Kurang Lancar';
        } else if ($cek->bi_checking == 4) {
            $bi = 'Lancar';
        }

        if ($cek->kewajiban_pihak_lain == 1) {
            $kwj = 'Lainnya';
        } else if ($cek->bi_checking == 2) {
            $kwj = 'Leasing';
        } else if ($cek->bi_checking == 3) {
            $kwj = 'Koperasi';
        } else if ($cek->bi_checking == 4) {
            $kwj = 'Bank Umum';
        }

        if ($cek->pihak_berwajib == 1 || $cek->pengalaman_tki == 1) {
            $jd = 'Tidak Pernah';
        } else if ($cek->bi_checking == 2) {
            $jd = 'Pernah';
        }

        if ($cek->hubungan_tetangga == 1) {
            $dt = 'Kurang Baik';
        } else if ($cek->hubungan_tetangga == 2) {
            $dt = 'Cukup Baik';
        } else if ($cek->hubungan_tetangga == 3) {
            $dt = 'Baik';
        }

        if ($cek->pengalaman_tki == 1) {
            $pt = 'Tidak Pernah';
        } else if ($cek->bi_checking == 2) {
            $pt = 'Pernah';
        }

        $data = [
            'bi_checking' => $bi,
            'kewajiban_pihak_lain' => $kwj,
            'pihak_berwajib' => $jd,
            'hubungan_tetangga' => $dt,
            'pengalaman_tki' => $pt,
        ];

        return $data;
    }
}
