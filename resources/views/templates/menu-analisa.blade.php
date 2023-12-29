<div class="col-3 d-none d-md-block border-end">
    <div class="card-body">
        <div class="list-group list-group-transparent mt-0 mb-0">
            <a href="{{ route('tambah.index', ['pengajuan' => $pengajuan]) }}"
                class="list-group-item list-group-item-action d-flex align-items-center {{ request()->is('analisa/usaha/perdagangan/tambah', 'analisa/usaha/perdagangan/*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chevrons-right" width="24"
                    height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M7 7l5 5l-5 5"></path>
                    <path d="M13 7l5 5l-5 5"></path>
                </svg> &nbsp;Usaha Perdagangan
            </a>
            <a href="{{ route('pertanian.index', ['pengajuan' => $pengajuan]) }}"
                class="list-group-item list-group-item-action d-flex align-items-center {{ request()->is('analisa/usaha/pertanian', 'analisa/usaha/pertanian/*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chevrons-right"
                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M7 7l5 5l-5 5"></path>
                    <path d="M13 7l5 5l-5 5"></path>
                </svg> &nbsp;Usah Pertanian
            </a>
            <a href="{{ route('jasa.index', ['pengajuan' => $pengajuan]) }}"
                class="list-group-item list-group-item-action d-flex align-items-center {{ request()->is('analisa/usaha/jasa', 'analisa/usaha/jasa/*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chevrons-right"
                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M7 7l5 5l-5 5"></path>
                    <path d="M13 7l5 5l-5 5"></path>
                </svg> &nbsp;Usaha Jasa
            </a>
            <a href="{{ route('lain.index', ['pengajuan' => $pengajuan]) }}"
                class="list-group-item list-group-item-action d-flex align-items-center {{ request()->is('analisa/usaha/lain', 'analisa/usaha/lain/*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chevrons-right"
                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M7 7l5 5l-5 5"></path>
                    <path d="M13 7l5 5l-5 5"></path>
                </svg> &nbsp;Usaha Lainnya
            </a>
            <a href="{{ route('keuangan.index', ['pengajuan' => $pengajuan]) }}"
                class="list-group-item list-group-item-action d-flex align-items-center {{ request()->is('analisa/keuangan') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chevrons-right"
                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M7 7l5 5l-5 5"></path>
                    <path d="M13 7l5 5l-5 5"></path>
                </svg> &nbsp;Kemampuan Keuangan</a>
            <a href="{{ route('kepemilikan.index', ['pengajuan' => $pengajuan]) }}"
                class="list-group-item list-group-item-action d-flex align-items-center {{ request()->is('analisa/harta/kepemilikan') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chevrons-right"
                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M7 7l5 5l-5 5"></path>
                    <path d="M13 7l5 5l-5 5"></path>
                </svg> &nbsp;Harta Kepemilikan</a>
            <a href="{{ route('jaminan.index', ['pengajuan' => $pengajuan]) }}"
                class="list-group-item list-group-item-action d-flex align-items-center {{ request()->is('analisa/taksasi/jaminan') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chevrons-right"
                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M7 7l5 5l-5 5"></path>
                    <path d="M13 7l5 5l-5 5"></path>
                </svg> &nbsp;Taksasi Jaminan</a>
            <a href="{{ route('a5c.index', ['pengajuan' => $pengajuan]) }}"
                class="list-group-item list-group-item-action d-flex align-items-center {{ request()->is('analisa/a5c') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chevrons-right"
                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M7 7l5 5l-5 5"></path>
                    <path d="M13 7l5 5l-5 5"></path>
                </svg> &nbsp;Analisa 5C</a>
            <a href="{{ route('kualitatif.index', ['pengajuan' => $pengajuan]) }}"
                class="list-group-item list-group-item-action d-flex align-items-center {{ request()->is('analisa/kualitatif') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chevrons-right"
                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M7 7l5 5l-5 5"></path>
                    <path d="M13 7l5 5l-5 5"></path>
                </svg> &nbsp;Analisa Kualitatif</a>
            <a href="{{ route('memorandum.index', ['pengajuan' => $pengajuan]) }}"
                class="list-group-item list-group-item-action d-flex align-items-center {{ request()->is('analisa/memorandum') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chevrons-right"
                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M7 7l5 5l-5 5"></path>
                    <path d="M13 7l5 5l-5 5"></path>
                </svg> &nbsp;Memorandum</a>
            <a href="{{ route('asuransi.index', ['pengajuan' => $pengajuan]) }}"
                class="list-group-item list-group-item-action d-flex align-items-center {{ request()->is('analisa/asuransi') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chevrons-right"
                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M7 7l5 5l-5 5"></path>
                    <path d="M13 7l5 5l-5 5"></path>
                </svg> &nbsp;Asuransi</a>
            <a href="{{ route('tambahan.index', ['pengajuan' => $pengajuan]) }}"
                class="list-group-item list-group-item-action d-flex align-items-center {{ request()->is('analisa/tambahan') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chevrons-right"
                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M7 7l5 5l-5 5"></path>
                    <path d="M13 7l5 5l-5 5"></path>
                </svg> &nbsp;Analisa Tambahan</a>
        </div>
    </div>
</div>
