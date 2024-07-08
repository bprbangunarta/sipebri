<div class="box box-solid">
    <div class="box-header with-border">
        <h3 class="box-title">Data Nasabah RSC</h3>

        <div class="box-tools">
            <button type="button" class="btn btn-box-tool" data-widget="collapse">
                <i class="fa fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="box-body no-padding">
        <ul class="nav nav-pills nav-stacked">
            <li>
                <a href="#"><i class="fa fa-user"></i> {{ $data->nama_nasabah }}</a>
            </li>
            <li>
                <a href="#">
                    <i class="fa fa-gear"></i>
                    {{ $data->produk_kode . ' ' . '-' . ' ' . $data->metode_rps }}
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fa fa-bitcoin"></i>
                    {{ 'Rp.' . ' ' . number_format($data->plafon, 0, ',', '.') . ' ' . '(' . $data->jangka_waktu . ' ' . 'BULAN)' }}
                </a>
            </li>
        </ul>
    </div>
</div>

<div class="box box-solid">
    <div class="box-header with-border">
        <h3 class="box-title">Penjadwalan RSC</h3>

        <div class="box-tools">
            <button type="button" class="btn btn-box-tool" data-widget="collapse">
                <i class="fa fa-minus"></i>
            </button>
        </div>
    </div>

    <div class="box-body no-padding">
        <ul class="nav nav-pills nav-stacked">

            <li class="{{ request()->is('themes/rsc/penjadwalan/tambah') ? 'active' : '' }}">
                <a href="{{ route('rsc.penjadwalan.index', ['rsc' => $data->rsc]) }}">
                    <i class="fa fa-folder-o"></i> Penjadwalan
                </a>
            </li>

        </ul>
    </div>
</div>
