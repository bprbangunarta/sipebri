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
        <main class="flex min-h-screen items-center justify-center p-4">
            <div class="w-full max-w-sm">
                <div class="mb-4 flex flex-col items-center gap-1">
                    <span class="flex size-9 items-center justify-center rounded-lg bg-primary text-base font-bold text-white">S</span>
                    <p class="text-base font-semibold">{{ config('app.name') }}</p>
                </div>

                <div class="flex flex-col items-center gap-2 rounded-lg border border-line bg-surface p-6 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted/60" aria-hidden="true"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>
                    <p class="text-xs font-medium text-muted">{{ $code }}</p>
                    <h1 class="text-base font-semibold">{{ $title }}</h1>
                    <p class="text-sm text-muted">{{ $description }}</p>

                    <div class="mt-2 flex flex-wrap justify-center gap-2">
                        @if ($reload ?? false)
                            <a href="{{ url()->current() }}" class="inline-flex h-8 items-center justify-center gap-1.5 rounded-md bg-primary px-3 text-sm font-medium text-white transition-colors hover:bg-primary-hover">Try again</a>
                        @else
                            <a href="{{ url('/') }}" class="inline-flex h-8 items-center justify-center gap-1.5 rounded-md bg-primary px-3 text-sm font-medium text-white transition-colors hover:bg-primary-hover">Go to home</a>
                        @endif
                    </div>
                </div>
            </div>
        </main>
    </body>
</html>
