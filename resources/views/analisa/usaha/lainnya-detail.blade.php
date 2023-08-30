@extends('templates.app')
@section('title', 'Analisa Usaha Lainnya')

@section('content')
<div class="page-body">
  <div class="container-xl">
    <div class="row row-deck row-cards">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <div class="container-xl">
              <div class="row g-2 align-items-center">

                @include('templates.header-analisa')

                <div class="col-auto ms-auto d-print-none">
                  <div class="btn-list">
                    <a href="{{ route('analisa.usaha.lainnya') }}" class="btn btn-primary">
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

                @include('templates.menu-analisa')

                <div class="col d-flex flex-column">
                  <div class="card-body">

                    <table class="table table-bordered table-vcenter fs-5">
                      <thead>
                        <tr>
                          <th class="text-center" colspan="5">Informasi Usaha</th>
                        </tr>
                        <tr>
                          <th class="text-center">Jenis Usaha</th>
                          <th class="text-center">Nama Usaha</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td><input class="form-control" type="text" name="" id="" value="Home Industri" readonly></td>
                          <td><input class="form-control" type="text" name="" id="" value="Pembuatan Boneka" readonly>
                          </td>
                        </tr>
                      </tbody>
                    </table>

                    <table class="table table-bordered table-vcenter fs-5">
                      <thead>
                        <tr>
                          <th class="text-center" colspan="2">Hasil Penjualan</th>
                        </tr>
                      </thead>
                      <thead>
                        <tr>
                          <th class="text-center">Nama Pendapatan</th>
                          <th class="text-center">Nominal Pendapatan</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td><input type="text" class="form-control" placeholder="Nama Pendapatan"></td>
                          <td><input type="text" class="form-control" placeholder="Masukan Nominal"></td>
                        </tr>
                        <tr>
                          <td><input type="text" class="form-control" placeholder="Nama Pendapatan"></td>
                          <td><input type="text" class="form-control" placeholder="Masukan Nominal"></td>
                        </tr>
                        <tr>
                          <td><input type="text" class="form-control" placeholder="Nama Pendapatan"></td>
                          <td><input type="text" class="form-control" placeholder="Masukan Nominal"></td>
                        </tr>
                        <tr>
                          <td><input type="text" class="form-control" placeholder="Nama Pendapatan"></td>
                          <td><input type="text" class="form-control" placeholder="Masukan Nominal"></td>
                        </tr>
                        <tr>
                          <td><input type="text" class="form-control" placeholder="Nama Pendapatan"></td>
                          <td><input type="text" class="form-control" placeholder="Masukan Nominal"></td>
                        </tr>
                      </tbody>
                    </table>

                    <table class="table table-bordered table-vcenter fs-5">
                      <thead>
                        <tr>
                          <th class="text-center" colspan="2">Biaya Operasional</th>
                        </tr>
                      </thead>
                      <thead>
                        <tr>
                          <th class="text-center">Pengeluaran Untuk</th>
                          <th class="text-center">Nominal Pengeluaran</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td><input type="text" class="form-control" placeholder="Nama Pengeluaran"></td>
                          <td><input type="text" class="form-control" placeholder="Masukan Nominal"></td>
                        </tr>
                        <tr>
                          <td><input type="text" class="form-control" placeholder="Nama Pengeluaran"></td>
                          <td><input type="text" class="form-control" placeholder="Masukan Nominal"></td>
                        </tr>
                        <tr>
                          <td><input type="text" class="form-control" placeholder="Nama Pengeluaran"></td>
                          <td><input type="text" class="form-control" placeholder="Masukan Nominal"></td>
                        </tr>
                        <tr>
                          <td><input type="text" class="form-control" placeholder="Nama Pengeluaran"></td>
                          <td><input type="text" class="form-control" placeholder="Masukan Nominal"></td>
                        </tr>
                        <tr>
                          <td><input type="text" class="form-control" placeholder="Nama Pengeluaran"></td>
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
                          <th><input class="form-control" disabled="" value="Pendapatan Usaha"></th>
                          <td><input type="text" class="form-control" readonly="" value="Rp. "></td>
                        </tr>
                        <tr>
                          <th><input class="form-control" disabled="" value="Biaya Operasional"></th>
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