@extends('templates.auth')
@section('title', 'Lupa Password')

@section('content')
<div class="page page-center">
    <div class="container container-tight py-4">
        <div class="text-center mb-4">
            <a href="/" class="navbar-brand navbar-brand-autodark">
                <img src="assets/img/logo.svg" height="36" alt="">
            </a>
        </div>

        <form class="card card-md" method="POST" action="{{ route('password.email') }}" novalidate>
            @csrf

            <div class="card-body">
                <p class="text-muted mb-4">
                    Masukkan alamat email Anda dan kata sandi Anda akan diatur ulang melalui email.
                </p>

                <div class="mb-2">
                    <label class="form-label">Email address</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                        value="{{ old('email') }}" placeholder="Masukan email">

                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M3 5m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" />
                            <path d="M3 7l9 6l9 -6" />
                        </svg>
                        Kirim password baru
                    </button>
                </div>
            </div>
        </form>

        <div class="text-center text-muted mt-3">
            Lupakan saja, <a href="/login">kirim saya kembali</a> ke layar masuk.
        </div>
    </div>
</div>
@endsection