@extends('templates.auth')
@section('title', 'Sistem Pemberian Kredit')
@section('bg', 'bg-white')

@section('content')
<div class="row g-0 flex-fill">
    <div class="col-12 col-lg-6 col-xl-4 border-top-wide border-primary d-flex flex-column justify-content-center">
        <div class="container container-tight my-5 px-lg-5">
            <div class="text-center mb-4">
                <a href="/" class="navbar-brand navbar-brand-autodark">
                    <img src="assets/img/logo.svg" height="36" alt="">
                </a>
            </div>

            <form method="POST" action="{{ route('login') }}" novalidate>
                @csrf

                <div class="mb-2">
                    <label class="form-label">Username</label>
                    <input type="text"
                        class="form-control @error('username') is-invalid @enderror @error('email') is-invalid @enderror"
                        name="username" value="{{ old('username') }}" placeholder="Username atau Email">

                    @error('username')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-2">
                    <label class="form-label">
                        Password
                        <span class="form-label-description">
                            <a href="/forgot-password">Lupa Password</a>
                        </span>
                    </label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" name="password"
                        value="{{ old('password') }}" placeholder="********">

                    @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-2">
                    <label class="form-check">
                        <input type="checkbox" class="form-check-input" />
                        <span class="form-check-label">Ingat saya</span>
                    </label>
                </div>
                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100">Masuk</button>
                </div>
            </form>
            <div class="text-center text-muted mt-3">
                Belum mempunyai akun? <a href="/register" tabindex="-1">Buat akun</a>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6 col-xl-8 d-none d-lg-block">
        <div class="bg-cover h-100 min-vh-100"
            style="background-image: url(tabler/static/photos/finances-us-dollars-and-bitcoins-currency-money-2.jpg)">
        </div>
    </div>
</div>
@endsection