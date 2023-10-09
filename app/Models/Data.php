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
            'kontinuitas' => self::analisa5c_number($cek->kontinuitas),
            'pengalaman_usaha' => $pu,
            'pertumbuhan_usaha' => self::analisa5c_number($cek->pertumbuhan_usaha),
            'laporan_keuangan' => self::analisa5c_number($cek->laporan_keuangan),
            'catatan_kredit' => self::analisa5c_number($cek->catatan_kredit),
            'kondisi_slik' => self::analisa5c_number($cek->kondisi_slik),
            'aset_diluar_usaha' => self::analisa5c_number($cek->aset_diluar_usaha),
            'aset_terkait_usaha' => self::analisa5c_number($cek->aset_terkait_usaha),
            'capital_sumber_modal' => self::analisa5c_number($cek->capital_sumber_modal),
            'capital_evaluasi_capital' => self::analisa5c_number($cek->capital_evaluasi_capital),
            'rc' => $cek->rc,
            'evaluasi_capacity' => self::analisa5c_number($cek->evaluasi_capacity),
        ];
        dd($data);
        return $data;
    }
}
