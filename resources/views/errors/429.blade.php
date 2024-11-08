@extends('errors::minimal')

@section('title', __('Too Many Requests'))
@section('code', '429')
@section('message',
    'Kami menerima terlalu banyak permintaan dalam waktu singkat. Harap tunggu beberapa saat dan coba
    lagi. Jika Anda mengalami masalah berkelanjutan, hubungi IT.')
