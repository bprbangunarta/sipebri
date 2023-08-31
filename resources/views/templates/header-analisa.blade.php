<div class="col">
    <h1 class="fw-bold">{{ $data->nama_nasabah }}</h1>
    <div class="my-2"></div>
    <div class="list-inline list-inline-dots text-muted">
        <div class="list-inline-item">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-database-dollar" width="24"
                height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M4 6c0 1.657 3.582 3 8 3s8 -1.343 8 -3s-3.582 -3 -8 -3s-8 1.343 -8 3"></path>
                <path d="M4 6v6c0 1.657 3.582 3 8 3c.415 0 .822 -.012 1.22 -.035"></path>
                <path d="M20 10v-4"></path>
                <path d="M4 12v6c0 1.657 3.582 3 8 3c.352 0 .698 -.009 1.037 -.025"></path>
                <path d="M21 15h-2.5a1.5 1.5 0 0 0 0 3h1a1.5 1.5 0 0 1 0 3h-2.5"></path>
                <path d="M19 21v1m0 -8v1"></path>
            </svg>
            {{ $data->plafon = 'Rp. ' . number_format($data->plafon, 0, ',', '.') }}
        </div>
        <div class="list-inline-item">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar-time" width="24"
                height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M11.795 21h-6.795a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4"></path>
                <path d="M18 18m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"></path>
                <path d="M15 3v4"></path>
                <path d="M7 3v4"></path>
                <path d="M3 11h16"></path>
                <path d="M18 16.496v1.504l1 1"></path>
            </svg>
            {{ $data->jangka_waktu }} Bulan
        </div>
    </div>
</div>
