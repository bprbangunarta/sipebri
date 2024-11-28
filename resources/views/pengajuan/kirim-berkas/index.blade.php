@extends('pengajuan.kirim-berkas.menu_kirim')
@section('title', 'Kirim Berkas')

@section('content')
    <div class="tab-content">
        <div class="tab-pane active">
            <div class="box-body" style="overflow: auto;white-space: nowrap;width: 100%;">
                <form action="{{ route('kirim.berkas.simpan') }}" method="post">
                    @csrf
                    <div class="box-body" style="margin-top: -10px;font-size:12px;">
                        <div class="div-left">
                            <div style="width: 100%;float:left;">
                                <span class="fw-bold">KODE PENGAJUAN</span>
                                <input type="text" class="form-control text-uppercase" name="kode_pengajuan" required>
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
@endsection
