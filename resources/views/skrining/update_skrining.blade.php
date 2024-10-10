@extends('skrining.menu_analisa')
@section('title', 'Update Screening')

@section('content')
    <div class="tab-content">
        <div class="tab-pane active">

            <div class="box-body table-responsive" style="overflow: auto; width: 100%; height:425px;">
                <form action="{{ route('update.data.skrining') }}" method="get">
                    @csrf
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <td>
                                    <span class="fw-bold fs-4">NIK</span>
                                    <input type="hidden" value="{{ $no }}" name="no">
                                    <input type="text" value="{{ $nik }}" class="form-control" name="nik"
                                        readonly>
                                </td>
                                <td>
                                    <span class="fw-bold">NAMA CALON NASABAH</span>
                                    <input type="text" value="{{ $nama }}" class="form-control" name="nama"
                                        readonly>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <span class="fw-bold fs-4">CATATAN</span>
                                    <textarea name="catatan" id="" cols="30" rows="5" style="width: 100%;">{{ $catatan }}</textarea>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <button type="submit" class="btn btn-sm btn-primary"
                                        style="margin-top:20px;width:100%">PROSES</button>
                                </td>
                            </tr>
                        </thead>
                    </table>
                </form>
            </div>
        </div>

    </div>
    </div>
@endsection
