@extends('errors::minimal')

@section('title', __('Forbidden'))
@section('code', '403')
{{-- @section('message', __($exception->getMessage() ?: 'Forbidden')) --}}
@section('message',
    'Anda tidak memiliki izin untuk mengakses halaman ini. Pastikan Anda memiliki hak akses yang sesuai
    atau hubungi IT.')
