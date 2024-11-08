@extends('errors::minimal')

@section('title', __('Server Error'))
@section('code', '500')
@section('message',
    'Kami mohon maaf, ada masalah di sisi server yang menyebabkan permintaan Anda tidak dapat diproses. Silahkan coba lagi
    beberapa saat atau hubungi IT.')
