@extends('rsc.usaha_lainnya.menu', [$data])
@section('title', 'Analisa Usaha Lainnya RSC')

@section('content')
    <div class="tab-content">
        <div class="tab-pane active">

            <form action="{{ route('rsc.usaha.lain.bahan.simpan', ['kode_usaha' => $data->kode_usaha]) }}" id="bb_lainnya"
                method="POST">
                @method('post')
                @csrf
                <div class="box-body table-responsive no-padding">
                    <div style="overflow: auto; width: 100%; height: 355px;">
                        <table class="table table-hover" style="font-size: 12px;">
                            <thead>
                                <tr>
                                    <th class="text-center" width="33%">BAHAN BAKU</th>
                                    <th class="text-center" width="7%">JUMLAH</th>
                                    <th class="text-center" width="30%">HARGA</th>
                                    <th class="text-center" width="30%">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <input class="form-control input-sm form-border" type="text" name="bahan_baku1"
                                            id="bahan_baku1" placeholder="ENTRI" value="{{ old('bahan_baku1') }}">
                                    </td>
                                    <td>
                                        <input class="form-control input-sm form-border text-center" type="text"
                                            name="jumlah1" id="jumlah1" value="{{ old('jumlah1') }}" placeholder="0">
                                    </td>
                                    <td>
                                        <input class="form-control input-sm form-border" type="text" name="hrg1"
                                            id="hrg1" placeholder="Rp. " value="{{ old('hrg1') }}">
                                    </td>
                                    <td>
                                        <input class="form-control input-sm form-border" type="text" name="total1"
                                            id="total1" placeholder="Rp. " readonly>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <input class="form-control input-sm form-border" type="text" name="bahan_baku2"
                                            id="bahan_baku2" placeholder="ENTRI" value="{{ old('bahan_baku2') }}">
                                    </td>
                                    <td>
                                        <input class="form-control input-sm form-border text-center" type="text"
                                            name="jumlah2" id="jumlah2" value="{{ old('jumlah2') }}" placeholder="0">
                                    </td>
                                    <td>
                                        <input class="form-control input-sm form-border" type="text" name="hrg2"
                                            id="hrg2" placeholder="Rp. " value="{{ old('hrg2') }}">
                                    </td>
                                    <td>
                                        <input class="form-control input-sm form-border" type="text" name="total2"
                                            id="total2" placeholder="Rp. " readonly>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <input class="form-control input-sm form-border" type="text" name="bahan_baku3"
                                            id="bahan_baku3" placeholder="ENTRI" value="{{ old('bahan_baku3') }}">
                                    </td>
                                    <td>
                                        <input class="form-control input-sm form-border text-center" type="text"
                                            name="jumlah3" id="jumlah3" value="{{ old('jumlah3') }}" placeholder="0">
                                    </td>
                                    <td>
                                        <input class="form-control input-sm form-border" type="text" name="hrg3"
                                            id="hrg3" placeholder="Rp. " value="{{ old('hrg3') }}">
                                    </td>
                                    <td>
                                        <input class="form-control input-sm form-border" type="text" name="total3"
                                            id="total3" placeholder="Rp. " readonly>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <input class="form-control input-sm form-border" type="text" name="bahan_baku4"
                                            id="bahan_baku4" placeholder="ENTRI" value="{{ old('bahan_baku4') }}">
                                    </td>
                                    <td>
                                        <input class="form-control input-sm form-border text-center" type="text"
                                            name="jumlah4" id="jumlah4" value="{{ old('jumlah4') }}" placeholder="0">
                                    </td>
                                    <td>
                                        <input class="form-control input-sm form-border" type="text" name="hrg4"
                                            id="hrg4" placeholder="Rp. " value="{{ old('hrg4') }}">
                                    </td>
                                    <td>
                                        <input class="form-control input-sm form-border" type="text" name="total4"
                                            id="total4" placeholder="Rp. " readonly>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <input class="form-control input-sm form-border" type="text"
                                            name="bahan_baku5" id="bahan_baku5" placeholder="ENTRI"
                                            value="{{ old('bahan_baku5') }}">
                                    </td>
                                    <td>
                                        <input class="form-control input-sm form-border text-center" type="text"
                                            name="jumlah5" id="jumlah5" value="{{ old('jumlah5') }}" placeholder="0">
                                    </td>
                                    <td>
                                        <input class="form-control input-sm form-border" type="text" name="hrg5"
                                            id="hrg5" placeholder="Rp. " value="{{ old('hrg5') }}">
                                    </td>
                                    <td>
                                        <input class="form-control input-sm form-border" type="text" name="total5"
                                            id="total5" placeholder="Rp. " readonly>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <input class="form-control input-sm form-border" type="text"
                                            name="bahan_baku6" id="bahan_baku6" placeholder="ENTRI"
                                            value="{{ old('bahan_baku6') }}">
                                    </td>
                                    <td>
                                        <input class="form-control input-sm form-border text-center" type="text"
                                            name="jumlah6" id="jumlah6" value="{{ old('jumlah6') }}" placeholder="0">
                                    </td>
                                    <td>
                                        <input class="form-control input-sm form-border" type="text" name="hrg6"
                                            id="hrg6" placeholder="Rp. " value="{{ old('hrg6') }}">
                                    </td>
                                    <td>
                                        <input class="form-control input-sm form-border" type="text" name="total6"
                                            id="total6" placeholder="Rp. " readonly>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <input class="form-control input-sm form-border" type="text"
                                            name="bahan_baku7" id="bahan_baku7" placeholder="ENTRI"
                                            value="{{ old('bahan_baku7') }}">
                                    </td>
                                    <td>
                                        <input class="form-control input-sm form-border text-center" type="text"
                                            name="jumlah7" id="jumlah7" value="{{ old('jumlah7') }}" placeholder="0">
                                    </td>
                                    <td>
                                        <input class="form-control input-sm form-border" type="text" name="hrg7"
                                            id="hrg7" placeholder="Rp. " value="{{ old('hrg7') }}">
                                    </td>
                                    <td>
                                        <input class="form-control input-sm form-border" type="text" name="total7"
                                            id="total7" placeholder="Rp. " readonly>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <input class="form-control input-sm form-border" type="text"
                                            name="bahan_baku8" id="bahan_baku8" placeholder="ENTRI"
                                            value="{{ old('bahan_baku8') }}">
                                    </td>
                                    <td>
                                        <input class="form-control input-sm form-border text-center" type="text"
                                            name="jumlah8" id="jumlah8" value="{{ old('jumlah8') }}" placeholder="0">
                                    </td>
                                    <td>
                                        <input class="form-control input-sm form-border" type="text" name="hrg8"
                                            id="hrg8" placeholder="Rp. " value="{{ old('hrg8') }}">
                                    </td>
                                    <td>
                                        <input class="form-control input-sm form-border" type="text" name="total8"
                                            id="total8" placeholder="Rp. " readonly>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <input class="form-control input-sm form-border" type="text"
                                            name="bahan_baku9" id="bahan_baku9" placeholder="ENTRI"
                                            value="{{ old('bahan_baku9') }}">
                                    </td>
                                    <td>
                                        <input class="form-control input-sm form-border text-center" type="text"
                                            name="jumlah9" id="jumlah9" value="{{ old('jumlah9') }}" placeholder="0">
                                    </td>
                                    <td>
                                        <input class="form-control input-sm form-border" type="text" name="hrg9"
                                            id="hrg9" placeholder="Rp. " value="{{ old('hrg9') }}">
                                    </td>
                                    <td>
                                        <input class="form-control input-sm form-border" type="text" name="total9"
                                            id="total9" placeholder="Rp. " readonly>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <input class="form-control input-sm form-border" type="text"
                                            name="bahan_baku10" id="bahan_baku10" placeholder="ENTRI"
                                            value="{{ old('bahan_baku10') }}">
                                    </td>
                                    <td>
                                        <input class="form-control input-sm form-border text-center" type="text"
                                            name="jumlah10" id="jumlah10" value="{{ old('jumlah10') }}"
                                            placeholder="0">
                                    </td>
                                    <td>
                                        <input class="form-control input-sm form-border" type="text" name="hrg10"
                                            id="hrg10" placeholder="Rp. " value="{{ old('hrg10') }}">
                                    </td>
                                    <td>
                                        <input class="form-control input-sm form-border" type="text" name="total10"
                                            id="total10" placeholder="Rp. " readonly>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <button type="submit" class="btn btn-sm btn-primary" style="margin-top:10px;width:100%;"
                    id="submit-bb-lainnya">SIMPAN</button>
            </form>

        </div>
    </div>
@endsection
@push('myscript')
    <script src="{{ asset('assets/js/myscript/bahan-baku.js') }}"></script>
    <script>
        //Hold Submit Ketika diklik 2X
        $('#bb_lainnya').submit(function(event) {
            var submitButton = $('#submit-bb-lainnya');
            submitButton.prop('disabled', true);
        });
    </script>
@endpush