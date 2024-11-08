@extends('errors::minimal')

@section('title', __('Page Expired'))
@section('code', '419')
@section('message',
    'Terjadi masalah dengan sesi Anda. Kemungkinan sesi telah kedaluwarsa,
    silahkan coba lagi setelah memuat ulang halaman atau login kembali untuk melanjutkan.')
