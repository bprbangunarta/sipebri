@extends('errors::minimal')

@section('title', __('Service Unavailable'))
@section('code', '503')
@section('message',
    'Saat ini server tidak dapat memproses permintaan Anda karena sedang dalam pemeliharaan. Silahkan
    coba lagi nanti. Kami mohon maaf atas ketidaknyamanannya.')
