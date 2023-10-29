@extends('theme.app')
@section('title', 'Data Surveyor')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-3">
                    @include('theme.menu-pengajuan', ['nasabah' => $data->kd_pengajuan])
                </div>

                <div class="col-xs-9">
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#surveyor" data-toggle="tab">DATA SURVEYOR</a>
                            </li>
                        </ul>

                        <form action="{{ route('survei.update', ['survei' => $data->kode_pengajuan]) }}" method="POST">
                            @method('put')
                            @csrf
                            <div class="tab-content">
                                <div class="tab-pane active" id="surveyor">
                                    <div class="box-body" style="margin-top: -10px;font-size:12px;">
                                        <div class="row" style="margin-top:5px;">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>WILAYAH</label>
                                                    <input type="hidden" value="{{ $data->auth }}" name="input_user">
                                                    <input type="hidden" value="{{ $data->kode_pengajuan }}"
                                                        name="pengajuan_kode">

                                                    <select type="text" class="form-control wilayah" name="kantor_kode"
                                                        required>
                                                        @if (is_null($survey->kantor_kode))
                                                            <option value="">--PILIH--</option>
                                                        @else
                                                            <option value="{{ $survey->kantor_kode }}">
                                                                {{ $survey->nama_kantor }}</option>
                                                        @endif

                                                        @foreach ($kantor->sortBy('nama_kantor') as $item)
                                                            <option value="{{ $item->kode_kantor }}">
                                                                {{ $item->nama_kantor }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>KASI ANALIS</label>
                                                    <select type="text" class="form-control kasi" name="kasi_kode"
                                                        required>
                                                        @if (is_null($survey->kasi_kode))
                                                            <option value="">--PILIH--</option>
                                                        @else
                                                            <option value="{{ $survey->kasi_kode }}">
                                                                {{ $survey->nama_kasi }}</option>
                                                        @endif

                                                        @foreach ($kasi as $item)
                                                            <option value="{{ $item->code_user }}">{{ $item->nama }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>SURVEYOR</label>
                                                    <select type="text" class="form-control surveyor"
                                                        name="surveyor_kode" required>
                                                        @if (is_null($survey->surveyor_kode))
                                                            <option value="">--PILIH--</option>
                                                        @else
                                                            <option value="{{ $survey->surveyor_kode }}">
                                                                {{ $survey->nama_surveyor }}</option>
                                                        @endif

                                                        @foreach ($staff as $item)
                                                            <option value="{{ $item->code_user }}">{{ $item->nama }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @can('edit pengajuan kredit')
                                    <div class="box-body" style="margin-top:-20px;">
                                        <button type="submit" class="btn btn-sm btn-primary"
                                            style="margin-top:10px;width:100%">SIMPAN</button>
                                    </div>
                                @endcan
                            </div>
                        </form>

                        @can('otorisasi pengajuan kredit')
                            <form action="{{ route('otorsurvei', ['otorisasi' => $data->kd_pengajuan]) }}" method="POST">
                                @csrf
                                <div class="box-body" style="margin-top:-20px;">
                                    <input type="text" name='kode_pengajuan' value="{{ $data->kd_pengajuan }}" hidden>
                                    <button type="submit" class="btn btn-sm btn-primary"
                                        style="margin-top:10px;width:100%">OTORISASI</button>
                                </div>
                            </form>
                        @endcan

                    </div>
                </div>
        </section>
    </div>
@endsection

@push('myscript')
    <script>
        // Select2
        $('.wilayah').select2()
        $('.kasi').select2()
        $('.surveyor').select2()
    </script>
@endpush
