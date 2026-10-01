<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $code }} {{ $title }} - {{ config('app.name') }}</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">

        @fonts
        @vite(['resources/css/app.css'])
    </head>
    <body class="font-sans antialiased">
        <main class="flex min-h-screen flex-col items-center justify-center bg-canvas px-4 py-10 text-center">
            <div class="mb-8 flex items-center gap-2">
                <span class="flex size-8 items-center justify-center rounded-lg bg-primary text-sm font-bold text-white">S</span>
                <span class="text-base font-semibold">{{ config('app.name') }}</span>
            </div>

            <p class="text-8xl leading-none font-bold tracking-tight text-primary/20 select-none sm:text-9xl">{{ $code }}</p>
            <h1 class="mt-4 text-2xl font-semibold">{{ $title }}</h1>
            <p class="mt-2 max-w-md text-sm text-muted">{{ $description }}</p>

            <div class="mt-6 flex flex-wrap justify-center gap-2">
                @if ($reload ?? false)
                    <a href="{{ url()->current() }}" class="inline-flex h-8 items-center justify-center gap-1.5 rounded-md bg-primary px-3 text-sm font-medium text-white transition-colors hover:bg-primary-hover">Coba lagi</a>
                @else
                    <a href="{{ url('/') }}" class="inline-flex h-8 items-center justify-center gap-1.5 rounded-md bg-primary px-3 text-sm font-medium text-white transition-colors hover:bg-primary-hover">Ke beranda</a>
                @endif
            </div>
        </main>
    </body>
</html>
