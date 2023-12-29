@extends('staff.analisa.u-perdagangan.menu', [$data, 'kode_usaha' => $perdagangan->kd_usaha, 'pengajuan' => $data->kd_pengajuan])
@section('title', 'Analisa Usaha Perdagangan')

@section('content')
    <div class="tab-content">
        <div class="tab-pane active">

            <form action="{{ route('perdagangan.simpanidentitas', ['id' => $perdagangan->id]) }}" method="post">
                @csrf
                <div class="box-body" style="margin-top: -10px;font-size:12px;">

                    <div class="div-left">
                        <div>
                            <span class="fw-bold">KODE USAHA</span>
                            <input class="form-control input-sm form-border" type="text" name="kode_usaha"
                                value="{{ $perdagangan->kode_usaha }}" readonly>
                        </div>
                        <div style="margin-top: 5px;">
                            <span class="fw-bold">LAMA USAHA</span>
                            <select class="form-control input-sm form-border" name="lama_usaha" id="">
                                @if (is_null($perdagangan->lama_usaha))
                                    <option value="">--Pilih Waktu--</option>
                                @else
                                    <option value="{{ $perdagangan->lama_usaha }}" selected>{{ $perdagangan->lama_usaha }}
                                    </option>
                                @endif
                                <option value="1 Tahun" {{ old('lama_usaha') == '1 Tahun' ? 'selected' : '' }}>
                                    1 Tahun</option>
                                <option value="2 Tahun" {{ old('lama_usaha') == '2 Tahun' ? 'selected' : '' }}>
                                    2 Tahun</option>
                                <option value="3 Tahun" {{ old('lama_usaha') == '3 Tahun' ? 'selected' : '' }}>
                                    3 Tahun</option>
                                <option value="4 Tahun" {{ old('lama_usaha') == '4 Tahun' ? 'selected' : '' }}>
                                    4 Tahun</option>
                                <option value=">5 Tahun" {{ old('lama_usaha') == '5 Tahun' ? 'selected' : '' }}>
                                    >5 Tahun</option>
                            </select>
                        </div>
                    </div>

                    <div class="div-right">
                        <div>
                            <span class="fw-bold">NAMA USAHA</span>
                            <input class="form-control input-sm form-border" type="text" name="nama_usaha"
                                value="{{ old('nama_usaha') ?? $perdagangan->nama_usaha }}" required>
                        </div>
                        <div style="margin-top: 5px;">
                            <span class="fw-bold">ALAMAT USAHA</span>
                            <input class="form-control input-sm form-border" type="text" name="lokasi_usaha"
                                value="{{ old('nama_usaha') ?? ($perdagangan->lokasi_usaha ?? $perdagangan->alamat) }}"
                                required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-sm btn-primary" style="margin-top:10px;width:100%">SIMPAN</button>
                </div>
            </form>


        </div>
    </div>
@endsection

@push('myscript')
    <script src="{{ asset('assets/js/myscript/perdagangan.js') }}"></script>
@endpush
