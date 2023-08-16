<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Data extends Model
{
    use HasFactory;

    public static function agama($cek){
        if ($cek == "1") {
            return 'Islam';
        }elseif ($cek == "2") {
            return 'Katolik';
        }elseif ($cek == "3"){
            return 'Kristen';
        }elseif ($cek == "4"){
            return 'Hindu';
        }elseif ($cek == "5"){
            return 'Budha';
        }elseif ($cek == "6"){
            return 'Kong Hu Cu';
        }
    }

    public static function identitas($identitas){
        if ($identitas == "1") {
            return 'KTP';
        }elseif ($identitas == "2") {
            return 'SIM';
        }elseif ($identitas == "3"){
            return 'Passport';
        }elseif ($identitas == "9"){
            return 'Lainnya';
        }
    }

    public static function jk($jk){
        if ($jk == "1") {
            return 'Pria';
        }elseif ($jk == "2") {
            return 'Wanita';
        }
    }

    public static function warganegara($wn){
        if ($wn == "1") {
            return 'Warga Negara Indonesia';
        }elseif ($wn == "2") {
            return 'Warga Negara Asing';
        }
    }

    public static function status($status){
        if ($status == "M") {
            return 'Menikah';
        }elseif ($status == "L") {
            return 'Lajang';
        }elseif ($status == "D") {
            return 'Duda';
        }elseif ($status == "J") {
            return 'Janda';
        }
    }

    public static function dana($dana){
        if ($dana == "1") {
            return 'Hibah';
        }elseif ($dana == "2") {
            return 'Lain2';
        }elseif ($dana == "3") {
            return 'Penghasilan';
        }elseif ($dana == "4") {
            return 'Warisan';
        }
    }

    public static function penghasilanutama($utama){
        if ($utama == "1") {
            return 's/d 2,5 jt';
        }elseif ($utama == "2") {
            return 's/d 2,5 - 5 jt';
        }elseif ($utama == "3"){
            return 's/d 5 - 7,5 jt';
        }elseif ($utama == "4"){
            return 's/d 7,5 - 10 jt';
        }elseif ($utama == "5"){
            return '10 jt';
        }
    }

    public static function penghasilanlain($lain){
        if ($lain == "1") {
            return 's/d 2,5 jt';
        }elseif ($lain == "2") {
            return 's/d 2,5 - 5 jt';
        }elseif ($lain == "3"){
            return 's/d 5 - 7,5 jt';
        }elseif ($lain == "4"){
            return 's/d 7,5 - 10 jt';
        }elseif ($lain == "5"){
            return '10 jt';
        }
    }

    public static function tanggungan($tanggungan){
        if ($tanggungan == "0") {
            return 'Tidak Ada';
        }elseif ($tanggungan == "1") {
            return '1 Orang';
        }elseif ($tanggungan == "2") {
            return '2 Orang';
        }elseif ($tanggungan == "3"){
            return '3 Orang';
        }elseif ($tanggungan == "4"){
            return '4 Orang';
        }elseif ($tanggungan == "5"){
            return '5 Orang';
        }
    }
}
