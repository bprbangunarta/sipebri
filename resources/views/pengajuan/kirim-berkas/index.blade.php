@extends('theme.app')
@section('title', 'Pelaksanaan Survei')
@yield('jquery')
@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-3">
                    <div class="box box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title"><b>Informasi</b></h3>

                            <div class="box-tools">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                    <i class="fa fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="box-body no-padding">
                            <ul class="nav nav-pills nav-stacked">
                                <li>
                                    <a href="#">
                                        <p>
                                            1. Pilih kantor yang sama jika berkas <br>&nbsp;&nbsp;&nbsp;&nbsp; tidak
                                            dikirim. <br>
                                            2. Data harus diisi semua ya.
                                        </p>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-file" aria-hidden="true"></i>
                            <h3 class="box-title">KIRIM BERKAS</h3>
                        </div>

                        <div class="box-body" style="overflow: auto;white-space: nowrap;width: 100%;">
                            <form action="{{ route('kirim.berkas.simpan') }}" method="post">
                                @csrf
                                <div class="box-body" style="margin-top: -10px;font-size:12px;">
                                    <div class="div-left">
                                        <div style="width: 100%;float:left;">
                                            <span class="fw-bold">KODE PENGAJUAN</span>
                                            <input type="text" class="form-control text-uppercase" name="kode_pengajuan"
                                                required>
                                        </div>

                                        <div style="margin-top:5px;width: 100%;float:left;">
                                            <label for="">KE KANTOR</label>
                                            <select class="form-control kantor" name="ke_kantor" id="ke_kantor"
                                                style="width: 100%;margin-top:-5px;" required>
                                                <option value="">--PILIH--</option>
                                                @foreach ($kantor as $item)
                                                    <option value="{{ $item->kode_kantor }}">{{ $item->nama_kantor }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="div-right">
                                        <div style="width: 100%;float:left;">
                                            <label for="">DARI KANTOR</label>
                                            <select class="form-control kantor" name="dari_kantor" id="dari_kantor"
                                                style="width: 100%;margin-top:-5px;" required>
                                                <option value="">--PILIH--</option>
                                                @foreach ($kantor as $item)
                                                    <option value="{{ $item->kode_kantor }}">{{ $item->nama_kantor }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div style="margin-top:15px;width: 100%;float:left;">
                                            <button type="submit" class="btn btn-sm btn-primary"
                                                style="margin-top:10px;width:100%">SIMPAN</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
