@extends('pengajuan.kirim-berkas.menu_kirim')
@section('title', 'Serah Terima Berkas')

@section('content')
    <div class="tab-content">
        <div class="tab-pane active">
            <div class="box-body" style="overflow: auto;white-space: nowrap;width: 100%;">
                <form action="{{ route('serah.terima.simpan') }}" method="post">
                    @csrf
                    <div class="box-body" style="margin-top: -10px;font-size:12px;">
                        <div class="div-left">
                            <div style="width: 100%;float:left;">
                                <span class="fw-bold">KODE PENGAJUAN</span>
                                <input type="text" class="form-control text-uppercase"
                                    value="{{ old('kode_pengajuan') }}" name="kode_pengajuan" required>
                            </div>
                        </div>

                        <div class="div-right">
                            <div class="form-group" style="margin-top: 0px;">
                                <label for="">STAFF/KASI ANALIS</label>
                                <select class="form-control kantor" name="user" id="user"
                                    style="width: 100%;margin-top:-5px;" required>
                                    <option value="">--PILIH--</option>
                                    @foreach ($user as $item)
                                        <option value="{{ $item->code_user }}">{{ $item->role_name }} -
                                            {{ $item->nama_user }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div style="width: 100%;float:left;">
                            <button type="submit" class="btn btn-sm btn-primary"
                                style="margin-top:10px;width:100%">SIMPAN</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('myscript')
    {{-- <script>
        $('#user').select2()
    </script> --}}
@endpush
