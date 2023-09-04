@extends('templates.app')
@section('title', 'Analisa Usaha Pergagangan')
@yield('jquery')
@section('content')
<div class="page-body">
  <div class="container-xl">
    <div class="row row-deck row-cards">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <div class="container-xl">
              <div class="row g-2 align-items-center">

                @include('templates.header-analisa', ['pengajuan' => $data->kd_pengajuan])

                <div class="col-auto ms-auto d-print-none">
                  <div class="btn-list">
                    <a href="{{ route('analisa.usaha.perdagangan') }}" class="btn btn-primary">                    
                      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left" width="24"
                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M5 12l14 0"></path>
                        <path d="M5 12l6 6"></path>
                        <path d="M5 12l6 -6"></path>
                      </svg>
                      Kembali
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="card-body border-bottom py-3">
            <div class="card">
              <div class="row g-0">

                @include('templates.menu-analisa', ['pengajuan' => $data->kd_pengajuan])

                <div class="col d-flex flex-column">
                  <div class="card-body">

                    <table class="table table-bordered table-vcenter fs-5">
                      <thead>
                        <tr>
                          <th class="text-center" colspan="6">Informasi Barang Dagang</th>
                        </tr>
                        <tr>
                          <th class="text-center">Nama Barang</th>
                          <th class="text-center">Harga Beli</th>
                          <th class="text-center">Harga Jual</th>                          
                          <th class="text-center">Laba</th>                          
                          <th class="text-center" width="15%">Stok</th>
                          <th class="text-center" width="15%">%</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td><input class="form-control" type="text" name="" id="" placeholder="Nama Item"></td>
                          <td><input class="form-control input-harga" type="text" name="" id="hrg1" placeholder="Nominal"></td>
                          <td><input class="form-control input-jual" type="text" name="" id="jual1" placeholder="Nominal"></td>
                          <td><input class="form-control" type="text" name="" id="laba1" placeholder="Nominal" readonly></td>
                          <td><input class="form-control text-center" type="number" name="" id="stock1"></td>
                          <td><input class="form-control text-center input-persen" type="text" name="" id="persen1" disabled=""></td>
                        </tr>

                        <tr>
                          <td><input class="form-control" type="text" name="" id="" placeholder="Nama Item"></td>
                          <td><input class="form-control" type="text" name="" id="hrg2" placeholder="Nominal"></td>
                          <td><input class="form-control" type="text" name="" id="jual2" placeholder="Nominal"></td>
                          <td><input class="form-control" type="text" name="" id="laba2" placeholder="Nominal" readonly></td>
                          <td><input class="form-control text-center" type="number" name="" id="stock2"></td>
                          <td><input class="form-control text-center" type="text" name="" id="persen2" disabled=""></td>
                        </tr>
                        <tr>
                          <td><input class="form-control" type="text" name="" id="" placeholder="Nama Item"></td>
                          <td><input class="form-control" type="text" name="" id="hrg3" placeholder="Nominal"></td>
                          <td><input class="form-control" type="text" name="" id="jual3" placeholder="Nominal"></td>
                          <td><input class="form-control" type="text" name="" id="laba3" placeholder="Nominal" readonly></td>
                          <td><input class="form-control text-center" type="number" name="" id="stock3"></td>
                          <td><input class="form-control text-center" type="text" name="" id="persen3" disabled=""></td>
                        </tr>
                        <tr>
                          <td><input class="form-control" type="text" name="" id="" placeholder="Nama Item"></td>
                          <td><input class="form-control" type="text" name="" id="hrg4" placeholder="Nominal"></td>
                          <td><input class="form-control" type="text" name="" id="jual4" placeholder="Nominal"></td>
                          <td><input class="form-control" type="text" name="" id="laba4" placeholder="Nominal" readonly></td>
                          <td><input class="form-control text-center" type="number" name="" id="stock4"></td>
                          <td><input class="form-control text-center" type="text" name="" id="persen4" disabled=""></td>
                        </tr>
                        <tr>
                          <td><input class="form-control" type="text" name="" id="" placeholder="Nama Item"></td>
                          <td><input class="form-control" type="text" name="" id="hrg5" placeholder="Nominal"></td>
                          <td><input class="form-control" type="text" name="" id="jual5" placeholder="Nominal"></td>
                          <td><input class="form-control" type="text" name="" id="laba5" placeholder="Nominal" readonly></td>
                          <td><input class="form-control text-center" type="number" name="" id="stock5"></td>
                          <td><input class="form-control text-center" type="text" name="" id="persen5" disabled=""></td>
                        </tr>
                        <tr>
                          <td><input class="form-control" type="text" name="" id="" placeholder="Nama Item"></td>
                          <td><input class="form-control" type="text" name="" id="hrg6" placeholder="Nominal"></td>
                          <td><input class="form-control" type="text" name="" id="jual6" placeholder="Nominal"></td>
                          <td><input class="form-control" type="text" name="" id="laba6" placeholder="Nominal" readonly></td>
                          <td><input class="form-control text-center" type="number" name="" id="stock6"></td>
                          <td><input class="form-control text-center" type="text" name="" id="persen6" disabled=""></td>
                        </tr>
                        <tr>
                          <td><input class="form-control" type="text" name="" id="" placeholder="Nama Item"></td>
                          <td><input class="form-control" type="text" name="" id="hrg7" placeholder="Nominal"></td>
                          <td><input class="form-control" type="text" name="" id="jual7" placeholder="Nominal"></td>
                          <td><input class="form-control" type="text" name="" id="laba7" placeholder="Nominal" readonly></td>
                          <td><input class="form-control text-center" type="number" name="" id="stock7"></td>
                          <td><input class="form-control text-center" type="text" name="" id="persen7" disabled=""></td>
                        </tr>
                        <tr>
                          <td><input class="form-control" type="text" name="" id="" placeholder="Nama Item"></td>
                          <td><input class="form-control" type="text" name="" id="hrg8" placeholder="Nominal"></td>
                          <td><input class="form-control" type="text" name="" id="jual8" placeholder="Nominal"></td>
                          <td><input class="form-control" type="text" name="" id="laba8" placeholder="Nominal" readonly></td>
                          <td><input class="form-control text-center" type="number" name="" id="stock8"></td>
                          <td><input class="form-control text-center" type="text" name="" id="persen8" disabled=""></td>
                        </tr>
                        <tr>
                          <td><input class="form-control" type="text" name="" id="" placeholder="Nama Item"></td>
                          <td><input class="form-control" type="text" name="" id="hrg9" placeholder="Nominal"></td>
                          <td><input class="form-control" type="text" name="" id="jual9" placeholder="Nominal"></td>
                          <td><input class="form-control" type="text" name="" id="laba9" placeholder="Nominal" readonly></td>
                          <td><input class="form-control text-center" type="number" name="" id="stock9"></td>
                          <td><input class="form-control text-center" type="text" name="" id="persen9" disabled=""></td>
                        </tr>
                        <tr>
                          <td><input class="form-control" type="text" name="" id="" placeholder="Nama Item"></td>
                          <td><input class="form-control" type="text" name="" id="hrg10" placeholder="Nominal"></td>
                          <td><input class="form-control" type="text" name="" id="jual10" placeholder="Nominal"></td>
                          <td><input class="form-control" type="text" name="" id="laba10" placeholder="Nominal" readonly></td>
                          <td><input class="form-control text-center" type="number" name="" id="stock10"></td>
                          <td><input class="form-control text-center" type="text" name="" id="persen10" disabled=""></td>
                        </tr>
                      </tbody>
                    </table>
                    <table class="table table-bordered table-vcenter fs-5">
                      <thead>
                        <tr>
                          <th class="text-center">Total Beli</th>
                          <th class="text-center">Total Jual</th>
                          <th class="text-center">Total Laba</th>
                          <th class="text-center" width="11%">Stok</th>
                          <th class="text-center" width="11%">%</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td><input class="form-control" type="text" name="" id="tbeli" disabled="" value="Rp."></td>
                          <td><input class="form-control" type="text" name="" id="tjual" disabled="" value="Rp."></td>
                          <td><input class="form-control" type="text" name="" id="tlaba" disabled="" value="Rp."></td>
                          <td><input class="form-control text-center" type="text" name="" id="tstock" disabled=""></td>
                          <td><input class="form-control text-center" type="text" name="" id="tpersen" disabled=""></td>
                        </tr>
                      </tbody>
                    </table>

                    <table class="table table-bordered table-vcenter fs-5">
                      <thead>
                        <tr>
                          <th class="text-center" colspan="2">Informasi Perdagangan</th>
                        </tr>
                      </thead>
                      <thead>
                        <tr>
                          <th class="text-center">Barang Dagang</th>
                          <th class="text-center">Pendapatan Harian</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td><input type="text" class="form-control" placeholder="Masukan Nominal" id="brdg"></td>
                          <td><input type="text" class="form-control" placeholder="Masukan Nominal" id="penhar"></td>
                        </tr>
                      </tbody>
                      <thead>
                        <tr>
                          <th class="text-center">Pokok Penjualan</th>
                          <th class="text-center">Laba Harian</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td><input type="text" class="form-control" placeholder="Masukan Nominal" id="popen"></td>
                          <td><input type="text" class="form-control" placeholder="Masukan Nominal" id="lahar"></td>
                        </tr>
                      </tbody>
                    </table>

                    <table class="table table-bordered table-vcenter fs-5">
                      <thead>
                        <tr>
                          <th class="text-center" colspan="2">Biaya Perdagangan</th>
                        </tr>
                      </thead>
                      <thead>
                        <tr>
                          <th class="text-center">Transportasi</th>
                          <th class="text-center">Bongkar Muat</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td><input type="text" class="form-control" placeholder="Masukan Nominal" id="transport"></td>
                          <td><input type="text" class="form-control" placeholder="Masukan Nominal" id="bongkar"></td>
                        </tr>
                      </tbody>
                      <thead>
                        <tr>
                          <th class="text-center">Pegawai</th>
                          <th class="text-center">Gatel</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td><input type="text" class="form-control" placeholder="Masukan Nominal" id="pegawai"></td>
                          <td><input type="text" class="form-control" placeholder="Masukan Nominal" id="gatel"></td>
                        </tr>
                      </tbody>
                      <thead>
                        <tr>
                          <th class="text-center">Retribusi</th>
                          <th class="text-center">Sewa Tempat</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td><input type="text" class="form-control" placeholder="Masukan Nominal" id="retri"></td>
                          <td><input type="text" class="form-control" placeholder="Masukan Nominal" id="sewa"></td>
                        </tr>
                      </tbody>
                    </table>

                    <table class="table table-bordered table-vcenter fs-5">
                      <thead>
                        <tr>
                          <th class="text-center" colspan="2">Analisa Usaha</th>
                        </tr>
                      </thead>
                      <thead>
                        <tr>
                          <th><input class="form-control" disabled="" value="Laba Bulanan"></th>
                          <td><input type="text" class="form-control" readonly="" value="Rp. " id="lbulan"></td>
                        </tr>
                        <tr>
                          <th><input class="form-control" disabled="" value="Biaya Perdagangan"></th>
                          <td><input type="text" class="form-control" readonly="" value="Rp. "></td>
                        </tr>
                        <tr>
                          <th><input class="form-control" disabled="" value="Proyeksi Penambahan"></th>
                          <td><input type="text" class="form-control" placeholder="Masukan Nominal"></td>
                        </tr>
                        <tr>
                          <th><input class="form-control fw-bold" disabled="" value="Hasil Bersih Usaha"></th>
                          <td><input type="text" class="form-control bg-primary fw-bold text-white" disabled=""
                              value="Rp. "></td>
                        </tr>
                      </thead>
                    </table>

                  </div>
                  <div class="card-footer bg-transparent mt-auto">
                    <div class="btn-list justify-content-end">
                      <a href="#" class="btn btn-primary">
                        Simpan
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>

        </div>
      </div>

    </div>
  </div>
</div>
<script src="{{ asset('assets/js/myscript/perdagangan.js') }}"></script>
@endsection