@extends('skrining.menu')
@section('title', 'Screening')

@section('content')
    <div class="tab-content">
        <div class="tab-pane active">

            <div class="box-body table-responsive" style="overflow: auto; width: 100%; height:425px;">
                <form action="{{ route('skrining.nasabah') }}" method="get">
                    @csrf
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <td>
                                    <span class="fw-bold fs-4">NIK</span>
                                    <input type="text" class="form-control" name="nik">
                                </td>
                                <td>
                                    <span class="fw-bold">NAMA CALON NASABAH</span>
                                    <input type="text" class="form-control" name="nama">
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <span class="fw-bold fs-4">PEP</span>
                                    <select type="text" class="form-control" style="width: 50%; " name="pep"
                                        id="">
                                        <option value="">-- PILIH --</option>
                                        <option value="TERDAFTAR">TERDAFTAR</option>
                                        <option value="TIDAK TERDAFTAR">
                                            TIDAK TERDAFTAR
                                        </option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <button type="submit" class="btn btn-sm btn-primary"
                                        style="margin-top:20px;width:100%">CEK</button>
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
