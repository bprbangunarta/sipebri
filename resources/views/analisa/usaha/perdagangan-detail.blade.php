@extends('templates.app')
@section('title', 'Analisa Usaha Pergagangan')

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
                          <th class="text-center" colspan="5">Informasi Barang Dagang</th>
                        </tr>
                        <tr>
                          <th class="text-center">Nama Barang</th>
                          <th class="text-center">Harga Beli</th>
                          <th class="text-center">Harga Jual</th>
                          <th class="text-center" width="15%">Stok</th>
                          <th class="text-center" width="15%">%</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td><input class="form-control" type="text" name="" id="" placeholder="Nama Item"></td>
                          <td><input class="form-control" type="text" name="" id="" placeholder="Nominal"></td>
                          <td><input class="form-control" type="text" name="" id="" placeholder="Nominal"></td>
                          <td><input class="form-control text-center" type="number" name="" id=""></td>
                          <td><input class="form-control text-center" type="text" name="" id="" disabled=""></td>
                        </tr>

                        <tr>
                          <td><input class="form-control" type="text" name="" id="" placeholder="Nama Item"></td>
                          <td><input class="form-control" type="text" name="" id="" placeholder="Nominal"></td>
                          <td><input class="form-control" type="text" name="" id="" placeholder="Nominal"></td>
                          <td><input class="form-control text-center" type="number" name="" id=""></td>
                          <td><input class="form-control text-center" type="text" name="" id="" disabled=""></td>
                        </tr>
                        <tr>
                          <td><input class="form-control" type="text" name="" id="" placeholder="Nama Item"></td>
                          <td><input class="form-control" type="text" name="" id="" placeholder="Nominal"></td>
                          <td><input class="form-control" type="text" name="" id="" placeholder="Nominal"></td>
                          <td><input class="form-control text-center" type="number" name="" id=""></td>
                          <td><input class="form-control text-center" type="text" name="" id="" disabled=""></td>
                        </tr>
                        <tr>
                          <td><input class="form-control" type="text" name="" id="" placeholder="Nama Item"></td>
                          <td><input class="form-control" type="text" name="" id="" placeholder="Nominal"></td>
                          <td><input class="form-control" type="text" name="" id="" placeholder="Nominal"></td>
                          <td><input class="form-control text-center" type="number" name="" id=""></td>
                          <td><input class="form-control text-center" type="text" name="" id="" disabled=""></td>
                        </tr>
                        <tr>
                          <td><input class="form-control" type="text" name="" id="" placeholder="Nama Item"></td>
                          <td><input class="form-control" type="text" name="" id="" placeholder="Nominal"></td>
                          <td><input class="form-control" type="text" name="" id="" placeholder="Nominal"></td>
                          <td><input class="form-control text-center" type="number" name="" id=""></td>
                          <td><input class="form-control text-center" type="text" name="" id="" disabled=""></td>
                        </tr>
                        <tr>
                          <td><input class="form-control" type="text" name="" id="" placeholder="Nama Item"></td>
                          <td><input class="form-control" type="text" name="" id="" placeholder="Nominal"></td>
                          <td><input class="form-control" type="text" name="" id="" placeholder="Nominal"></td>
                          <td><input class="form-control text-center" type="number" name="" id=""></td>
                          <td><input class="form-control text-center" type="text" name="" id="" disabled=""></td>
                        </tr>
                        <tr>
                          <td><input class="form-control" type="text" name="" id="" placeholder="Nama Item"></td>
                          <td><input class="form-control" type="text" name="" id="" placeholder="Nominal"></td>
                          <td><input class="form-control" type="text" name="" id="" placeholder="Nominal"></td>
                          <td><input class="form-control text-center" type="number" name="" id=""></td>
                          <td><input class="form-control text-center" type="text" name="" id="" disabled=""></td>
                        </tr>
                        <tr>
                          <td><input class="form-control" type="text" name="" id="" placeholder="Nama Item"></td>
                          <td><input class="form-control" type="text" name="" id="" placeholder="Nominal"></td>
                          <td><input class="form-control" type="text" name="" id="" placeholder="Nominal"></td>
                          <td><input class="form-control text-center" type="number" name="" id=""></td>
                          <td><input class="form-control text-center" type="text" name="" id="" disabled=""></td>
                        </tr>
                        <tr>
                          <td><input class="form-control" type="text" name="" id="" placeholder="Nama Item"></td>
                          <td><input class="form-control" type="text" name="" id="" placeholder="Nominal"></td>
                          <td><input class="form-control" type="text" name="" id="" placeholder="Nominal"></td>
                          <td><input class="form-control text-center" type="number" name="" id=""></td>
                          <td><input class="form-control text-center" type="text" name="" id="" disabled=""></td>
                        </tr>
                        <tr>
                          <td><input class="form-control" type="text" name="" id="" placeholder="Nama Item"></td>
                          <td><input class="form-control" type="text" name="" id="" placeholder="Nominal"></td>
                          <td><input class="form-control" type="text" name="" id="" placeholder="Nominal"></td>
                          <td><input class="form-control text-center" type="number" name="" id=""></td>
                          <td><input class="form-control text-center" type="text" name="" id="" disabled=""></td>
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
                          <td><input class="form-control" type="text" name="" id="" disabled="" value="Rp. "></td>
                          <td><input class="form-control" type="text" name="" id="" disabled="" value="Rp. "></td>
                          <td><input class="form-control" type="text" name="" id="" disabled="" value="Rp. "></td>
                          <td><input class="form-control text-center" type="text" name="" id="" disabled=""></td>
                          <td><input class="form-control text-center" type="text" name="" id="" disabled=""></td>
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
                          <td><input type="text" class="form-control" placeholder="Masukan Nominal"></td>
                          <td><input type="text" class="form-control" placeholder="Masukan Nominal"></td>
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
                          <td><input type="text" class="form-control" placeholder="Masukan Nominal"></td>
                          <td><input type="text" class="form-control" placeholder="Masukan Nominal"></td>
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
                          <td><input type="text" class="form-control" placeholder="Masukan Nominal"></td>
                          <td><input type="text" class="form-control" placeholder="Masukan Nominal"></td>
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
                          <td><input type="text" class="form-control" placeholder="Masukan Nominal"></td>
                          <td><input type="text" class="form-control" placeholder="Masukan Nominal"></td>
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
                          <td><input type="text" class="form-control" placeholder="Masukan Nominal"></td>
                          <td><input type="text" class="form-control" placeholder="Masukan Nominal"></td>
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
                          <td><input type="text" class="form-control" readonly="" value="Rp. "></td>
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
@endsection