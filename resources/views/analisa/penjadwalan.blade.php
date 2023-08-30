@extends('templates.app')
@section('title', 'Penjadwalan')

@section('content')
<div class="page-body">
  <div class="container-xl">
    <div class="row row-deck row-cards">

      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <div class="container-xl">
              <div class="row g-2 align-items-center">
                <div class="col">
                  <!-- Page pre-title -->
                  <div class="page-pretitle">
                    Analisa
                  </div>
                  <h2 class="page-title">
                    Penjadwalan Survey
                  </h2>
                </div>
              </div>
            </div>
          </div>

          <div class="card-body border-bottom py-3" style="margin-top:-7px;">

            <form action="/contoh" method="GET">
              <div class="input-group mb-2">
                <input type="text" class="form-control" name="" id="" placeholder="Cari Data">
                <button class="btn" type="submit">
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-filter" width="24"
                    height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path
                      d="M4 4h16v2.172a2 2 0 0 1 -.586 1.414l-4.414 4.414v7l-6 2v-8.5l-4.48 -4.928a2 2 0 0 1 -.52 -1.345v-2.227z">
                    </path>
                  </svg>
                  Filter
                </button>
              </div>
            </form>

            <div class="table-responsive">
              <table class="table table-bordered table-vcenter fs-5">
                <thead>
                  <tr>
                    <th class="text-center" width="3%">No</th>
                    <th class="text-center" width="11%">Kode Nasabah</th>
                    <th class="text-center">Nama Nasabah</th>
                    <th class="text-center">Plafon</th>
                    <th class="text-center">Kantor</th>
                    <th class="text-center" width="10%">Tgl. Survey</th>
                    <th class="text-center" width="10%">Tgl. Resurvey 1</th>
                    <th class="text-center" width="10%">Tgl. Resurvey 2</th>
                    <th class="text-center">Petugas</th>
                    <th class="text-center" width="8%">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td class="text-center">1</td>
                    <td class="text-center">00039366-001</td>
                    <td>MISNAN DENI</td>
                    <td>Rp. 50.000.000</td>
                    <td class="text-center">JALANCAGAK</td>
                    <td class="text-center">2023-07-12</td>
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                    <td class="text-center">ZULFADLI RIZAL</td>
                    <td class="text-center">
                      <a href="#">
                        <span class="badge bg-success">
                          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye" width="24"
                            height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"></path>
                            <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6">
                            </path>
                          </svg>
                        </span>
                      </a>

                      <a href="#" data-bs-toggle="modal" data-bs-target="#modal-penjadwalan">
                        <span class="badge bg-warning">
                          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar-stats"
                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M11.795 21h-6.795a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4"></path>
                            <path d="M18 14v4h4"></path>
                            <path d="M18 18m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"></path>
                            <path d="M15 3v4"></path>
                            <path d="M7 3v4"></path>
                            <path d="M3 11h16"></path>
                          </svg>
                        </span>
                      </a>
                    </td>
                  </tr>
                </tbody>
              </table>
              <p></p>

              <nav class="d-flex justify-items-center justify-content-between">
                <div class="d-flex justify-content-between flex-fill d-sm-none">
                  <ul class="pagination">
                    <li class="page-item disabled" aria-disabled="true">
                      <span class="page-link">« Previous</span>
                    </li>
                    <li class="page-item">
                      <a class="page-link" href="https://presensi.bprbangunarta.co.id/karyawan?page=2" rel="next">Next
                        »</a>
                    </li>
                  </ul>
                </div>

                <div class="d-none flex-sm-fill d-sm-flex align-items-sm-center justify-content-sm-between">
                  <div>
                    <p class="small text-muted">
                      Showing
                      <span class="fw-semibold">1</span>
                      to
                      <span class="fw-semibold">10</span>
                      of
                      <span class="fw-semibold">54</span>
                      results
                    </p>
                  </div>

                  <div>
                    <ul class="pagination">
                      <li class="page-item disabled" aria-disabled="true" aria-label="« Previous">
                        <span class="page-link" aria-hidden="true">‹</span>
                      </li>
                      <li class="page-item active" aria-current="page">
                        <span class="page-link">1</span>
                      </li>
                      <li class="page-item">
                        <a class="page-link" href="#">2</a>
                      </li>
                      <li class="page-item">
                        <a class="page-link" href="#" rel="next" aria-label="Next »">›</a>
                      </li>
                    </ul>
                  </div>
                </div>
              </nav>

            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<div class="modal modal-blur fade" id="modal-penjadwalan" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Penjadwalan Survey</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form action="#" method="POST">
        <input type="hidden" name="_token" value="VljQVhMAh1pJY7PbU2ZkAMuuhugnQYxcz72UVpvb">
        <div class="modal-body">
          <div class="row">

            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">Kode Nasabah</label>
                <input type="text" class="form-control" name="kode_nasabah" id="kode_nasabah" value="00039366-001"
                  readonly>
              </div>
            </div>

            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">Nama Nasabah</label>
                <input type="text" class="form-control" name="nama_nasabah" id="nama_nasabah" value="ZULFADLI RIZAL"
                  readonly>
              </div>
            </div>

            <div class="col-lg-12">
              <div class="mb-3">
                <label class="form-label">Alamat</label>
                <textarea class="form-control" name="alamat" id="alamat"
                  readonly>KP. SUKAGALIH NO.36 RT/RW 030/008 DESA. SUKAMULYA KECAMATAN. PAGADEN KABUPATEN. SUBANG</textarea>
              </div>
            </div>

            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">Petugas</label>
                <select class="form-control" name="kode_petugas" id="kode_petugas">
                  <option value="">--Pilih Petugas--</option>
                </select>
              </div>
            </div>

            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">Tgl. Survey</label>
                <input type="date" class="form-control" name="tgl_survey" id="tgl_survey" value="2023-07-12">
              </div>
            </div>

            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">Tgl. Resurvey 1</label>
                <input type="date" class="form-control" name="tgl_resurvey1" id="tgl_resurvey1">
              </div>
            </div>

            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">Tgl. Resurvey 2</label>
                <input type="date" class="form-control" name="tgl_resurvey2" id="tgl_resurvey2">
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">Batal</a>
          <a href="#" class="btn btn-warning ms-auto">Simpan</a>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection