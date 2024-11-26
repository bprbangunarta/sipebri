@extends('theme.app')
@section('title', 'Prosfek')
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
                                            1. Fhoto wajib diisi. <br>
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
                            <i class="fa fa-plus" aria-hidden="true"></i>
                            <h3 class="box-title">ADD PROSFEK</h3>
                        </div>

                        <div class="box-body" style="overflow: auto;white-space: nowrap;width: 100%;">
                            <form action="{{ route('prosfek.simpan') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="box-body" style="margin-top: -10px;font-size:12px;">
                                    <div class="div-left">
                                        <div style="width: 100%;float:left;">
                                            <label for="">NAMA CALON NASABAH</label>
                                            <input type="text" class="form-control text-uppercase"
                                                value="{{ old('calon_nasabah') }}" name="calon_nasabah" required>
                                        </div>

                                        <div
                                            style="margin-top:5px; width: 100%;float:left; display: flex; flex-direction: column;">
                                            <label for="">KABUPATEN</label>
                                            <select class="form-control text-uppercase kab" style="width: 100%;"
                                                name="kabupaten" id="select-kabupaten" required>
                                                <option value="">--PILIH--</option>
                                                @foreach ($kab as $item)
                                                    <option class="text-uppercase" value="{{ $item->kode_dati }}">
                                                        {{ $item->nama_dati }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div
                                            style="margin-top:5px; width: 100%;float:left; display: flex; flex-direction: column;">
                                            <label for="">KELURAHAN</label>
                                            <select class="form-control text-uppercase kel kelurahan" style="width: 100%;"
                                                placeholder="Pilih Kelurahan" name="kelurahan" id="select-kelurahan"
                                                required>
                                                <option value="">--PILIH--</option>
                                            </select>
                                        </div>

                                        <div
                                            style="margin-top:5px; width: 100%;float:left; display: flex; flex-direction: column;">
                                            <label for="">FHOTO PROSFEK</label>
                                            <input type="file" class="form-control" class="photo_prosfek"
                                                name="photo_prosfek" id="photo_prosfek">
                                        </div>

                                    </div>

                                    <div class="div-right">
                                        <div style="width: 100%;float:left;">
                                            <label for="">ALAMAT</label>
                                            <input type="text" class="form-control text-uppercase"
                                                value="{{ old('alamat') }}" name="alamat" required>
                                        </div>

                                        <div
                                            style="margin-top:5px; width: 100%;float:left; display: flex; flex-direction: column;">
                                            <label for="">KECAMATAN</label>
                                            <select class="form-control text-uppercase kec kecamatan" style="width: 100%;"
                                                name="kecamatan" id="select-kecamatan" required>
                                                <option value="">--PILIH--</option>
                                            </select>
                                        </div>

                                        <div style="margin-top:5px;width: 100%;float:left;">
                                            <label for="">NO HP</label>
                                            <input type="number" class="form-control text-uppercase" name="no_hp"
                                                value="{{ old('no_hp') }}" required>
                                        </div>

                                        <div
                                            style="margin-top:5px; width: 100%;float:left; display: flex; flex-direction: column;">
                                            <label for="">PROSFEK VIA</label>
                                            <select class="form-control text-uppercase via" style="width: 100%;"
                                                name="prosfek_via" required>
                                                <option value="">--PILIH--</option>
                                                <option value="VIDEO CALL">VIDEO CALL</option>
                                                <option value="DATANG LANGSUNG">DATANG LANGSUNG</option>
                                            </select>
                                        </div>

                                    </div>
                                    <div style="margin-top:5px;width: 100%;float:left;">
                                        <label for="">KETERANGAN</label>
                                        <input type="text" class="form-control text-uppercase" name="keterangan"
                                            value="{{ old('keterangan') }}" required>
                                    </div>

                                    <div style="margin-top:20px;width: 100%;float:left;">
                                        <button type="submit" class="btn btn-sm btn-primary"
                                            style="margin-top:10px;width:100%">SIMPAN</button>
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
@push('myscript')
    <script>
        $('.kab').select2()
        $('.kec').select2()
        $('.kel').select2()
        $('.via').select2()

        $(document).ready(function() {
            $("#select-kabupaten").on('change', function() {
                $('#select-kecamatan').empty()
                // $('#select-kelurahan').empty()
                var nama = $("#select-kabupaten").val();
                $.ajax({
                    url: "/nasabah/kabupaten",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        name: nama
                    },
                    success: function(response) {
                        let st = JSON.stringify(response);
                        let obj = Object.values(response);
                        var ch = []
                        for (var i = 0; i < obj.length; i++) {
                            if (ch.indexOf(obj[i].kecamatan) === -1) {
                                ch.push(obj[i].kecamatan)

                            }
                        }

                        ch.sort()

                        ch.forEach(data => {
                            $('#select-kecamatan').append($('<option>', {
                                value: data,
                                text: data
                            }));
                        });

                    },
                    error: function(xhr) {
                        // Tindakan jika terjadi error
                        console.log(xhr.responseText);
                    }
                });
            });

            //Ambil data kecamatan
            $("#select-kecamatan").on('change', function() {
                $('#select-kelurahan').empty()
                // $('#select-kelurahan').empty()
                var nama = $("#select-kecamatan").val();
                $.ajax({
                    url: "/nasabah/kecamatan",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        name: nama
                    },
                    success: function(response) {

                        let st = JSON.stringify(response);
                        let obj = Object.values(response);
                        let ch = [];

                        for (let i = 0; i < obj.length; i++) {
                            if (!ch.some(item => item.kelurahan === obj[i].kelurahan)) {
                                ch.push({
                                    kelurahan: obj[i].kelurahan,
                                    kode_pos: obj[i].kode_pos
                                });
                            }
                        }

                        ch.sort((a, b) => (a.kelurahan > b.kelurahan) ? 1 : -1);

                        ch.forEach(data => {
                            $('#select-kelurahan').append($('<option>', {
                                value: data.kelurahan,
                                text: data.kelurahan,
                                'data-kode-pos': JSON.stringify(
                                    data
                                ) // Simpan seluruh objek sebagai string di 'data-kode-pos'
                            }));
                        });

                        $('#select-kelurahan').on('change', function() {
                            let selectedKelurahan = $(this).val();
                            let selectedData = $(this).find('option:selected').data(
                                'kode-pos');
                            $('#kode_pos').val(selectedData.kode_pos);
                        });

                    },
                    error: function(xhr) {
                        // Tindakan jika terjadi error
                        console.log(xhr.responseText);
                    }
                });
            });
        })
    </script>
@endpush
