<div class="col-3 d-none d-md-block border-end">
    <div class="card-body">
        <div class="list-group list-group-transparent">
            <a href="{{ route('tambah.index', ['pengajuan' => $pengajuan]) }}"
                class="list-group-item list-group-item-action d-flex align-items-center {{ request()->is('analisa/usaha/perdagangan', 'analisa/usaha/perdagangan/detail') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chevrons-right" width="24"
                    height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M7 7l5 5l-5 5"></path>
                    <path d="M13 7l5 5l-5 5"></path>
                </svg> &nbsp;Usaha Perdagangan
            </a>
            <a href="{{ route('pertanian.index', ['pengajuan' => $pengajuan]) }}"
                class="list-group-item list-group-item-action d-flex align-items-center {{ request()->is('analisa/usaha/pertanian', 'analisa/usaha/pertanian/detail') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chevrons-right"
                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M7 7l5 5l-5 5"></path>
                    <path d="M13 7l5 5l-5 5"></path>
                </svg> &nbsp;Usah Pertanian
            </a>
            <a href="{{ route('jasa.index', ['pengajuan' => $pengajuan]) }}"
                class="list-group-item list-group-item-action d-flex align-items-center {{ request()->is('analisa/usaha/jasa', 'analisa/usaha/jasa/detail') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chevrons-right"
                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M7 7l5 5l-5 5"></path>
                    <path d="M13 7l5 5l-5 5"></path>
                </svg> &nbsp;Usaha Jasa
            </a>
            <a href="{{ route('analisa.usaha.lainnya', ['pengajuan' => $pengajuan]) }}"
                class="list-group-item list-group-item-action d-flex align-items-center {{ request()->is('analisa/usaha/lainnya', 'analisa/usaha/lainnya/detail') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chevrons-right"
                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M7 7l5 5l-5 5"></path>
                    <path d="M13 7l5 5l-5 5"></path>
                </svg> &nbsp;Usaha Lainnya
            </a>
            <a href="{{ route('analisa.keuangan', ['pengajuan' => $pengajuan]) }}"
                class="list-group-item list-group-item-action d-flex align-items-center {{ request()->is('analisa/keuangan') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chevrons-right"
                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M7 7l5 5l-5 5"></path>
                    <path d="M13 7l5 5l-5 5"></path>
                </svg> &nbsp;Kemampuan Keuangan</a>
            {{-- <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chevrons-right" width="24"
          height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
          stroke-linejoin="round">
          <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
          <path d="M7 7l5 5l-5 5"></path>
          <path d="M13 7l5 5l-5 5"></path>
        </svg> &nbsp;Harta Kepemilikan</a> --}}
        </div>
    </div>
</div>
