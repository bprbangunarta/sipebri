@extends('templates.app')
@section('title', 'Data Nasabah')

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
                                            Pendaftaran
                                        </div>
                                        <h2 class="page-title">
                                            Data Nasabah
                                        </h2>
                                    </div>
                                    <!-- Page title actions -->
                                    <div class="col-auto ms-auto d-print-none">
                                        <div class="btn-list">
                                            <a href="{{ route('pengajuan.index') }}" class="btn btn-primary">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="icon icon-tabler icon-tabler-arrow-left" width="24"
                                                    height="24" viewBox="0 0 24 24" stroke-width="2"
                                                    stroke="currentColor" fill="none" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <path d="M5 12l14 0"></path>
                                                    <path d="M5 12l6 6"></path>
                                                    <path d="M5 12l6 -6"></path>
                                                </svg>
                                                Kembali
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body border-bottom py-3">

                            <div class="card">
                                <div class="row g-0">
                                    <div class="col-3 d-none d-md-block border-end">
                                        <div class="card-body">
                                            @include('templates.menu-pendaftaran', [
                                                'nasabah' => $nasabah->kode_nasabah,
                                            ])
                                        </div>
                                    </div>

                                    <div class="col d-flex flex-column">
                                        <form action="{{ route('nasabah.update', ['nasabah' => $nasabah->kode_nasabah]) }}"
                                            method="POST" enctype="multipart/form-data">
                                            @method('PUT')
                                            @csrf
                                            <div class="card-body">
                                                <div class="row g-3">
                                                    <div class="col-md">
                                                        <div class="form-label">No CIF</div>
                                                        <input type="text" value="{{ $nasabah->kode_user }}"
                                                            name="input_user" hidden>
                                                        <input type="text" class="form-control" name="no_cif"
                                                            id="no_cif" value="{{ $nasabah->nocif }}" disabled>
                                                    </div>

                                                    <div class="col-md">
                                                        <div class="form-label">Jenis ID</div>
                                                        <select type="text" class="form-select"
                                                            placeholder="Pilih Identitas" name="identitas"
                                                            id="select-identitas">
                                                            <option value="{{ $nasabah->identitas }}" selected>
                                                                {{ $nasabah->iden }}</option>
                                                            <option value="1">KTP</option>
                                                            <option value="2">SIM</option>
                                                            <option value="3">Pasport</option>
                                                            <option value="9">Lainnya</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md">
                                                        <div class="form-label">No Identitas</div>
                                                        <input type="text" class="form-control" name="no_identitas"
                                                            id="no_identitas" placeholder="3213XXXXX"
                                                            value="{{ $nasabah->no_identitas }}">
                                                    </div>
                                                    <div class="col-md">
                                                        <div class="form-label">Masa Identitas</div>
                                                        @if (is_null($nasabah->masa_identitas))
                                                            <input class="form-control mb-2" placeholder="Pilih Tanggal"
                                                                name="masa_identitas" id="datepicker-masa-identitas" />
                                                        @else
                                                            <input class="form-control mb-2" placeholder="Pilih Tanggal"
                                                                name="masa_identitas" id="datepicker-masa-identitas-old"
                                                                value="{{ $nasabah->masa_identitas }}" />
                                                        @endif
                                                    </div>
                                                </div>
                                                <p></p>
                                                <div class="row g-3">
                                                    <div class="col-md">
                                                        <div class="form-label">Nama Panggilan</div>
                                                        <input type="text" class="form-control" name="nama_panggilan"
                                                            id="nama_panggilan" placeholder="Nama Panggilan"
                                                            value="{{ $nasabah->sname }}" required>
                                                    </div>
                                                    <div class="col-md">
                                                        <div class="form-label">Nama Lengkap</div>
                                                        <input type="text" class="form-control" name="nama_nasabah"
                                                            id="nama_nasabah" placeholder="Nama Lengkap"
                                                            value="{{ $nasabah->nama_nasabah }}">
                                                    </div>
                                                    <div class="col-md">
                                                        <div class="form-label">Tempat Lahir</div>
                                                        <input type="text" class="form-control" name="tempat_lahir"
                                                            id="tempat_lahir" placeholder="Tempat Lahir"
                                                            value="{{ $nasabah->tempat_lahir }}" required>
                                                    </div>
                                                    <div class="col-md">
                                                        <div class="form-label">Tanggal Lahir</div>
                                                        @if (is_null($nasabah->tanggal_lahir))
                                                            <input class="form-control mb-2" placeholder="Pilih Tanggal"
                                                                name="tanggal_lahir" id="datepicker-tanggal-lahir" />
                                                        @else
                                                            <input class="form-control mb-2" placeholder="Pilih Tanggal"
                                                                name="tanggal_lahir" id="datepicker-tanggal-lahir-old"
                                                                value="{{ $nasabah->tanggal_lahir }}" />
                                                        @endif
                                                    </div>
                                                </div>
                                                <p></p>
                                                <div class="row g-3">
                                                    <div class="col-md">
                                                        <div class="form-label">Kabupaten</div>
                                                        <select type="text" class="form-select"
                                                            placeholder="Pilih Kabupaten" name="kode_dati"
                                                            id="select-kabupaten">
                                                            @if (is_null($nasabah->kode_dati))
                                                                <option value="">Pilih Kabupaten</option>
                                                            @else
                                                                <option value="{{ $nasabah->kode_dati }}">
                                                                    {{ $nasabah->nm_dati }}</option>
                                                            @endif

                                                            @foreach ($kab as $item)
                                                                <option value="{{ $item->kode_dati }}">
                                                                    {{ $item->nama_dati }}
                                                                </option>
                                                            @endforeach

                                                        </select>
                                                    </div>
                                                    <div class="col-md">
                                                        <div class="form-label">Kecamatan</div>
                                                        <select type="text" class="form-select kecamatan"
                                                            placeholder="Pilih Kecamatan" name="kecamatan"
                                                            id="select-kecamatan">
                                                            @if (is_null($nasabah->kecamatan))
                                                                <option value="">Pilih Kecamatan</option>
                                                            @else
                                                                <option value="{{ $nasabah->kecamatan }}">
                                                                    {{ $nasabah->kecamatan }}</option>
                                                            @endif

                                                        </select>
                                                    </div>
                                                    <div class="col-md">
                                                        <div class="form-label">Kelurahan</div>
                                                        <select type="text" class="form-select kelurahan"
                                                            placeholder="Pilih Kelurahan" name="kelurahan"
                                                            id="select-kelurahan">
                                                            @if (is_null($nasabah->kelurahan))
                                                                <option value="">Pilih Kecamatan</option>
                                                            @else
                                                                <option value="{{ $nasabah->kelurahan }}">
                                                                    {{ $nasabah->kelurahan }}</option>
                                                            @endif

                                                        </select>
                                                    </div>
                                                    <div class="col-md">
                                                        <div class="form-label">Kota</div>
                                                        @if (is_null($nasabah->kota))
                                                            <input class="form-control" type="text" name="kota"
                                                                id="kota" placeholder="Kota">
                                                        @else
                                                            <input class="form-control" type="text" name="kota"
                                                                id="kota" placeholder="Kota"
                                                                value="{{ $nasabah->kota }}">
                                                        @endif

                                                    </div>
                                                </div>
                                                <p></p>
                                                <div class="row g-3">
                                                    <div class="col-md">
                                                        <div class="form-label">Alamat KTP</div>
                                                        @if (is_null($nasabah->alamat_ktp))
                                                            <textarea class="form-control" name="alamat_ktp" id="alamat_ktp" placeholder="Alamat Lengkap" required></textarea>
                                                        @else
                                                            <textarea class="form-control" name="alamat_ktp" id="alamat_ktp" placeholder="Alamat Lengkap" required>{{ $nasabah->alamat_ktp }}</textarea>
                                                        @endif

                                                    </div>
                                                    <div class="col-md">
                                                        <div class="form-label">Alamat Sekarang</div>
                                                        @if (is_null($nasabah->alamat_sekarang))
                                                            <textarea class="form-control" name="alamat_sekarang" id="alamat_sekarang" placeholder="Alamat Lengkap" required></textarea>
                                                        @else
                                                            <textarea class="form-control" name="alamat_sekarang" id="alamat_sekarang" placeholder="Alamat Lengkap" required>{{ $nasabah->alamat_sekarang }}</textarea>
                                                        @endif

                                                    </div>
                                                </div>
                                                <p></p>
                                                <div class="row g-3">
                                                    <div class="col-md">
                                                        <div class="form-label">Agama</div>
                                                        <select type="text" class="form-select"
                                                            placeholder="Pilih Agama" name="agama" id="select-agama">
                                                            @if (is_null($nasabah->religi))
                                                                <option value="">Pilih Agama</option>
                                                            @else
                                                                <option value="{{ $nasabah->agama }}">
                                                                    {{ $nasabah->religi }}</option>
                                                            @endif

                                                            <option value="1">Islam</option>
                                                            <option value="2">Katolik</option>
                                                            <option value="3">Kristen</option>
                                                            <option value="4">Hindu</option>
                                                            <option value="5">Budha</option>
                                                            <option value="6">Kong Hu Cu</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md">
                                                        <div class="form-label">Kalamin</div>
                                                        <select type="text" class="form-select"
                                                            placeholder="Pilih Kelamin" name="jenis_kelamin"
                                                            id="select-kelamin">
                                                            @if (is_null($nasabah->jk))
                                                                <option value="">Pilih Kalamin</option>
                                                            @else
                                                                <option value="{{ $nasabah->jenis_kelamin }}">
                                                                    {{ $nasabah->jk }}</option>
                                                            @endif

                                                            <option value="1">Pria</option>
                                                            <option value="2">Wanita</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md">
                                                        <div class="form-label">Kewarganegaraan</div>
                                                        <select type="text" class="form-select"
                                                            placeholder="Kewarganegaraan" name="kewarganegaraan"
                                                            id="select-kewarganegaraan">
                                                            @if (is_null($nasabah->kn))
                                                                <option value="WNI">Warga Negara Indonesia</option>
                                                                <option value="WNA">Warga Negara Asing</option>
                                                            @else
                                                                <option value="{{ $nasabah->kewarganegaraan }}">
                                                                    {{ $nasabah->kn }}</option>
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                                <p></p>
                                                <div class="row g-3">
                                                    <div class="col-md">
                                                        <div class="form-label">Gelar</div>
                                                        <select type="text" class="form-select"
                                                            placeholder="Pilih Pendidikan" name="pendidikan_kode"
                                                            id="select-pendidikan">
                                                            @if (is_null($nasabah->pendidikan_kode))
                                                                <option value="">Pilih Pendidikan</option>
                                                            @else
                                                                <option value="{{ $nasabah->pendidikan_kode }}">
                                                                    {{ $nasabah->std }}
                                                                </option>
                                                            @endif

                                                            @foreach ($pend as $item)
                                                                <option value="{{ $item->kode_pendidikan }}">
                                                                    {{ $item->nama_pendidikan }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md">
                                                        <div class="form-label">Status</div>
                                                        <select type="text" class="form-select"
                                                            placeholder="Pilih Status" name="status_pernikahan"
                                                            id="select-status">
                                                            @if (is_null($nasabah->st))
                                                                <option value="">Pilih Status</option>
                                                            @else
                                                                <option value="{{ $nasabah->status_pernikahan }}">
                                                                    {{ $nasabah->st }}</option>
                                                            @endif

                                                            <option value="M">Menikah</option>
                                                            <option value="L">Lajang</option>
                                                            <option value="D">Duda</option>
                                                            <option value="J">Janda</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md">
                                                        <div class="form-label">Pekerjaan</div>
                                                        <select type="text" class="form-select"
                                                            placeholder="Pilih Pekerjaan" name="pekerjaan_kode"
                                                            id="select-pekerjaan">
                                                            @if (is_null($nasabah->jo))
                                                                <option value="" selected>Pilih Pekerjaan</option>
                                                            @else
                                                                <option value="{{ $nasabah->pekerjaan_kode }}" selected>
                                                                    {{ $nasabah->jo }}
                                                                </option>
                                                            @endif

                                                            @foreach ($job as $item)
                                                                <option value="{{ $item->kode_pekerjaan }}">
                                                                    {{ $item->nama_pekerjaan }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <p></p>
                                                <div class="row g-3">
                                                    <div class="col-md">
                                                        <div class="form-label">Ibu Kandung</div>
                                                        <input type="text" class="form-control"
                                                            name="nama_ibu_kandung" id="nama_ibu_kandung"
                                                            placeholder="Nama Ibu Kandung"
                                                            value="{{ $nasabah->nama_ibu_kandung }}" required>
                                                    </div>
                                                    <div class="col-md">
                                                        <div class="form-label">Nomor Rekening</div>
                                                        <input type="number" class="form-control" name="no_rekening"
                                                            id="no_rekening" placeholder="No Rekening"
                                                            value="{{ $nasabah->no_rekening }}" required>
                                                    </div>
                                                    <div class="col-md">
                                                        <div class="form-label">Nomor NPWP</div>
                                                        <input type="number" class="form-control" name="no_npwp"
                                                            id="no_npwp" placeholder="No NPWP"
                                                            value="{{ $nasabah->no_npwp }}" required>
                                                    </div>
                                                </div>
                                                <p></p>
                                                <div class="row g-3">
                                                    <div class="col-md">
                                                        <div class="form-label">Nomor Telp</div>
                                                        <input type="text" class="form-control" name="no_telp"
                                                            id="no_telp" placeholder="0823XXXXX"
                                                            value="{{ $nasabah->no_telp }}" required>
                                                    </div>
                                                    <div class="col-md">
                                                        <div class="form-label">No Telp Darurat</div>
                                                        <input type="text" class="form-control" name="no_telp_darurat"
                                                            id="no_telp_darurat" placeholder="0823XXXXX"
                                                            value="{{ $nasabah->no_telp_darurat }}" required>
                                                    </div>
                                                    <div class="col-md">
                                                        <div class="form-label">Alamat Email</div>
                                                        <input type="email" class="form-control" name="email"
                                                            id="email" placeholder="namalengkap@gmail.com"
                                                            value="{{ $nasabah->email }}" required>
                                                    </div>
                                                </div>
                                                <p></p>
                                                <div class="row g-3">
                                                    <div class="col-md">
                                                        <div class="form-label">Sumber Dana</div>
                                                        <select type="text" class="form-select"
                                                            placeholder="Sumber Dana" name="sumber_dana"
                                                            id="select-sumber-dana">
                                                            @if (is_null($nasabah->sumber_dana))
                                                                <option value="">Sumber Dana</option>
                                                            @else
                                                                <option value="{{ $nasabah->sumber_dana }}">
                                                                    {{ $nasabah->dana }}</option>
                                                            @endif

                                                            <option value="1">Hibah</option>
                                                            <option value="2">Lain2</option>
                                                            <option value="3">Penghasilan</option>
                                                            <option value="4">Warisan</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md">
                                                        <div class="form-label">Penghasilan Utama</div>
                                                        <select type="text" class="form-select"
                                                            placeholder="Penghasilan Utama" name="penghasilan_utama"
                                                            id="select-penghasilan-utama">
                                                            @if (is_null($nasabah->penghasilan_utama))
                                                                <option value="">Penghasilan Utama</option>
                                                            @else
                                                                <option value="{{ $nasabah->penghasilan_utama }}">
                                                                    {{ $nasabah->hasil }}</option>
                                                            @endif

                                                            <option value="1">s/d 2,5 jt</option>
                                                            <option value="2">s/d 2,5 - 5 jt</option>
                                                            <option value="3">s/d 5 - 7,5 jt</option>
                                                            <option value="4">s/d 7,5 - 10 jt</option>
                                                            <option value="5"> 10 jt</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md">
                                                        <div class="form-label">Penghasilan Lainnya</div>
                                                        <select type="text" class="form-select"
                                                            placeholder="Penghasilan Lainnya" name="penghasilan_lainnya"
                                                            id="select-penghasilan-lainnya">
                                                            @if (is_null($nasabah->penghasilan_utama))
                                                                <option value="">Penghasilan Lainnya</option>
                                                            @else
                                                                <option value="{{ $nasabah->penghasilan_lainnya }}">
                                                                    {{ $nasabah->lain }}</option>
                                                            @endif

                                                            <option value="1">s/d 2,5 jt</option>
                                                            <option value="2">s/d 2,5 - 5 jt</option>
                                                            <option value="3">s/d 5 - 7,5 jt</option>
                                                            <option value="4">s/d 7,5 - 10 jt</option>
                                                            <option value="5">> 10 jt</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <p></p>
                                                <div class="row g-3">
                                                    <div class="col-md">
                                                        <div class="form-label">Photo KTP</div>
                                                        <input type="text" name="oldphotoktp"
                                                            value="{{ $nasabah->photo_ktp }}" hidden>

                                                        <input type="file" class="form-control" class="photo_ktp"
                                                            name="photo_ktp" id="photo_ktp" onchange="previewPhotoKtp()">

                                                        <div class="accordion mt-2" id="accordion-ktp">
                                                            <div class="accordion-item">
                                                                <h2 class="accordion-header" id="heading-1"
                                                                    style="height: 50px;margin-top:-10px;">
                                                                    <button class="accordion-button " type="button"
                                                                        data-bs-toggle="collapse"
                                                                        data-bs-target="#collapse-1" aria-expanded="true">
                                                                        Preview
                                                                    </button>
                                                                </h2>
                                                                <div id="collapse-1" class="accordion-collapse collapse"
                                                                    data-bs-parent="#accordion-ktp">
                                                                    <div class="accordion-body pt-0">
                                                                        <img class="rounded-2 mb-2 img-preview-ktp"
                                                                            src="{{ asset('storage/image/photo_ktp/' . $nasabah->photo_ktp) }}"
                                                                            class="card-img-top">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md">
                                                        <div class="form-label">Photo KK</div>
                                                        <input type="text" name="oldphotokk"
                                                            value="{{ $nasabah->photo_kk }}" hidden>

                                                        <input type="file" class="form-control" class="photo_kk"
                                                            name="photo_kk" id="photo_kk" onchange="previewPhotoKk()">

                                                        <div class="accordion mt-2" id="accordion-kk">
                                                            <div class="accordion-item">
                                                                <h2 class="accordion-header" id="heading-1"
                                                                    style="height: 50px;margin-top:-10px;">
                                                                    <button class="accordion-button " type="button"
                                                                        data-bs-toggle="collapse"
                                                                        data-bs-target="#collapse-1" aria-expanded="true">
                                                                        Preview
                                                                    </button>
                                                                </h2>
                                                                <div id="collapse-1" class="accordion-collapse collapse"
                                                                    data-bs-parent="#accordion-kk">
                                                                    <div class="accordion-body pt-0">
                                                                        <img class="rounded-2 mb-2 img-preview-kk"
                                                                            src="{{ asset('storage/image/photo_kk/' . $nasabah->photo_kk) }}"
                                                                            class="card-img-top">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <p></p>
                                                <div class="row g-3">
                                                    <div class="col-md">
                                                        <div class="form-label">Photo Formal</div>
                                                        <input type="text" name="oldphoto"
                                                            value="{{ $nasabah->photo }}" hidden>

                                                        <input type="file" class="form-control" class="photo"
                                                            name="photo" id="photo" onchange="previewPhoto()">

                                                        <div class="accordion mt-2" id="accordion-photo">
                                                            <div class="accordion-item">
                                                                <h2 class="accordion-header" id="heading-1"
                                                                    style="height: 50px;margin-top:-10px;">
                                                                    <button class="accordion-button " type="button"
                                                                        data-bs-toggle="collapse"
                                                                        data-bs-target="#collapse-1" aria-expanded="true">
                                                                        Preview
                                                                    </button>
                                                                </h2>
                                                                <div id="collapse-1" class="accordion-collapse collapse"
                                                                    data-bs-parent="#accordion-photo">
                                                                    <div class="accordion-body pt-0">
                                                                        <img class="rounded-2 mb-2 img-preview"
                                                                            src="{{ asset('storage/image/photo/' . $nasabah->photo) }}"
                                                                            class="card-img-top">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md">
                                                        <div class="form-label">Photo Selfie</div>
                                                        <input type="text" name="oldphotoselfie"
                                                            value="{{ $nasabah->photo_selfie }}" hidden>

                                                        <input type="file" class="form-control" class="photo_selfie"
                                                            name="photo_selfie" id="photo_selfie"
                                                            onchange="previewPhotoSelfi()">

                                                        <div class="accordion mt-2" id="accordion-selfie">
                                                            <div class="accordion-item">
                                                                <h2 class="accordion-header" id="heading-1"
                                                                    style="height: 50px;margin-top:-10px;">
                                                                    <button class="accordion-button " type="button"
                                                                        data-bs-toggle="collapse"
                                                                        data-bs-target="#collapse-1" aria-expanded="true">
                                                                        Preview
                                                                    </button>
                                                                </h2>
                                                                <div id="collapse-1" class="accordion-collapse collapse"
                                                                    data-bs-parent="#accordion-selfie">
                                                                    <div class="accordion-body pt-0">
                                                                        <img class="rounded-2 mb-2 img-preview2"
                                                                            src="{{ asset('storage/image/photo_selfie/' . $nasabah->photo_selfie) }}"
                                                                            class="card-img-top">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <hr style="margin-top: 25px;">
                                                <div class="row g-3" style="margin-top: -30px;">
                                                    <div class="col-md">
                                                        <div class="form-label">Tempat Kerja</div>
                                                        <input type="text" class="form-control" name="tempat_kerja"
                                                            id="tempat_kerja" placeholder="PT. BPR Bangunarta"
                                                            value="{{ $nasabah->tempat_kerja }}" required>
                                                    </div>
                                                    <div class="col-md">
                                                        <div class="form-label">No Telp Kantor</div>
                                                        <input type="text" class="form-control" name="no_telp_kantor"
                                                            id="no_telp_kantor" placeholder="(0260) 550888"
                                                            value="{{ $nasabah->no_telp_kantor }}" required>
                                                    </div>
                                                    <div class="col-md">
                                                        <div class="form-label">Nomor Karyawan</div>
                                                        <input type="text" class="form-control" name="no_karyawan"
                                                            id="no_karyawan" placeholder="NIK Karyawan"
                                                            value="{{ $nasabah->no_karyawan }}" required>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="card-footer bg-transparent mt-auto">
                                                <div class="btn-list justify-content-end">
                                                    <button type="submit" class="btn btn-primary text-white ms-auto">
                                                        Simpan
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('myscript')
    <script>
        // JS Darepicker
        document.addEventListener("DOMContentLoaded", function() {
            window.Litepicker && (new Litepicker({
                element: document.getElementById('datepicker-masa-identitas'),
                buttonText: {
                    previousMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-left -->
    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 6l-6 6l6 6" /></svg>`,
                    nextMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-right -->
    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg>`,
                },
            }));
        });

        document.addEventListener("DOMContentLoaded", function() {
            window.Litepicker && (new Litepicker({
                element: document.getElementById('datepicker-masa-identitas-old'),
                buttonText: {
                    previousMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-left -->
    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 6l-6 6l6 6" /></svg>`,
                    nextMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-right -->
    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg>`,
                },
            }));
        });

        document.addEventListener("DOMContentLoaded", function() {
            window.Litepicker && (new Litepicker({
                element: document.getElementById('datepicker-tanggal-lahir'),
                buttonText: {
                    previousMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-left -->
    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 6l-6 6l6 6" /></svg>`,
                    nextMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-right -->
    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg>`,
                },
            }));
        });

        document.addEventListener("DOMContentLoaded", function() {
            window.Litepicker && (new Litepicker({
                element: document.getElementById('datepicker-tanggal-lahir-old'),
                buttonText: {
                    previousMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-left -->
    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 6l-6 6l6 6" /></svg>`,
                    nextMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-right -->
    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg>`,
                },
            }));
        });
    </script>

    <script>
        // JS TomSelect
        document.addEventListener("DOMContentLoaded", function() {
            var el;
            window.TomSelect && (new TomSelect(el = document.getElementById('select-identitas'), {
                copyClassesToDropdown: false,
                dropdownClass: 'dropdown-menu ts-dropdown',
                optionClass: 'dropdown-item',
                controlInput: '<input>',
                render: {
                    item: function(data, escape) {
                        if (data.customProperties) {
                            return '<div><span class="dropdown-item-indicator">' + data
                                .customProperties + '</span>' + escape(data.text) + '</div>';
                        }
                        return '<div>' + escape(data.text) + '</div>';
                    },
                    option: function(data, escape) {
                        if (data.customProperties) {
                            return '<div><span class="dropdown-item-indicator">' + data
                                .customProperties + '</span>' + escape(data.text) + '</div>';
                        }
                        return '<div>' + escape(data.text) + '</div>';
                    },
                },
            }));
        });

        document.addEventListener("DOMContentLoaded", function() {
            var el;
            window.TomSelect && (new TomSelect(el = document.getElementById('select-kabupaten'), {
                copyClassesToDropdown: false,
                dropdownClass: 'dropdown-menu ts-dropdown',
                optionClass: 'dropdown-item',
                controlInput: '<input>',
                render: {
                    item: function(data, escape) {
                        if (data.customProperties) {
                            return '<div><span class="dropdown-item-indicator">' + data
                                .customProperties + '</span>' + escape(data.text) + '</div>';
                        }
                        return '<div>' + escape(data.text) + '</div>';
                    },
                    option: function(data, escape) {
                        if (data.customProperties) {
                            return '<div><span class="dropdown-item-indicator">' + data
                                .customProperties + '</span>' + escape(data.text) + '</div>';
                        }
                        return '<div>' + escape(data.text) + '</div>';
                    },
                },
            }));
        });

        // document.addEventListener("DOMContentLoaded", function() {
        //     var el;
        //     window.TomSelect && (new TomSelect(el = document.getElementById('select-kecamatan'), {
        //         copyClassesToDropdown: false,
        //         dropdownClass: 'dropdown-menu ts-dropdown',
        //         optionClass: 'dropdown-item',
        //         controlInput: '<input>',
        //         render: {
        //             item: function(data, escape) {
        //                 if (data.customProperties) {
        //                     return '<div><span class="dropdown-item-indicator">' + data
        //                         .customProperties + '</span>' + escape(data.text) + '</div>';
        //                 }
        //                 return '<div>' + escape(data.text) + '</div>';
        //             },
        //             option: function(data, escape) {
        //                 if (data.customProperties) {
        //                     return '<div><span class="dropdown-item-indicator">' + data
        //                         .customProperties + '</span>' + escape(data.text) + '</div>';
        //                 }
        //                 return '<div>' + escape(data.text) + '</div>';
        //             },
        //         },
        //     }));
        // });

        // document.addEventListener("DOMContentLoaded", function() {
        //     var el;
        //     window.TomSelect && (new TomSelect(el = document.getElementById('select-kelurahan'), {
        //         copyClassesToDropdown: false,
        //         dropdownClass: 'dropdown-menu ts-dropdown',
        //         optionClass: 'dropdown-item',
        //         controlInput: '<input>',
        //         render: {
        //             item: function(data, escape) {
        //                 if (data.customProperties) {
        //                     return '<div><span class="dropdown-item-indicator">' + data
        //                         .customProperties + '</span>' + escape(data.text) + '</div>';
        //                 }
        //                 return '<div>' + escape(data.text) + '</div>';
        //             },
        //             option: function(data, escape) {
        //                 if (data.customProperties) {
        //                     return '<div><span class="dropdown-item-indicator">' + data
        //                         .customProperties + '</span>' + escape(data.text) + '</div>';
        //                 }
        //                 return '<div>' + escape(data.text) + '</div>';
        //             },
        //         },
        //     }));
        // });

        document.addEventListener("DOMContentLoaded", function() {
            var el;
            window.TomSelect && (new TomSelect(el = document.getElementById('select-status'), {
                copyClassesToDropdown: false,
                dropdownClass: 'dropdown-menu ts-dropdown',
                optionClass: 'dropdown-item',
                controlInput: '<input>',
                render: {
                    item: function(data, escape) {
                        if (data.customProperties) {
                            return '<div><span class="dropdown-item-indicator">' + data
                                .customProperties + '</span>' + escape(data.text) + '</div>';
                        }
                        return '<div>' + escape(data.text) + '</div>';
                    },
                    option: function(data, escape) {
                        if (data.customProperties) {
                            return '<div><span class="dropdown-item-indicator">' + data
                                .customProperties + '</span>' + escape(data.text) + '</div>';
                        }
                        return '<div>' + escape(data.text) + '</div>';
                    },
                },
            }));
        });

        document.addEventListener("DOMContentLoaded", function() {
            var el;
            window.TomSelect && (new TomSelect(el = document.getElementById('select-kelamin'), {
                copyClassesToDropdown: false,
                dropdownClass: 'dropdown-menu ts-dropdown',
                optionClass: 'dropdown-item',
                controlInput: '<input>',
                render: {
                    item: function(data, escape) {
                        if (data.customProperties) {
                            return '<div><span class="dropdown-item-indicator">' + data
                                .customProperties + '</span>' + escape(data.text) + '</div>';
                        }
                        return '<div>' + escape(data.text) + '</div>';
                    },
                    option: function(data, escape) {
                        if (data.customProperties) {
                            return '<div><span class="dropdown-item-indicator">' + data
                                .customProperties + '</span>' + escape(data.text) + '</div>';
                        }
                        return '<div>' + escape(data.text) + '</div>';
                    },
                },
            }));
        });

        document.addEventListener("DOMContentLoaded", function() {
            var el;
            window.TomSelect && (new TomSelect(el = document.getElementById('select-agama'), {
                copyClassesToDropdown: false,
                dropdownClass: 'dropdown-menu ts-dropdown',
                optionClass: 'dropdown-item',
                controlInput: '<input>',
                render: {
                    item: function(data, escape) {
                        if (data.customProperties) {
                            return '<div><span class="dropdown-item-indicator">' + data
                                .customProperties + '</span>' + escape(data.text) + '</div>';
                        }
                        return '<div>' + escape(data.text) + '</div>';
                    },
                    option: function(data, escape) {
                        if (data.customProperties) {
                            return '<div><span class="dropdown-item-indicator">' + data
                                .customProperties + '</span>' + escape(data.text) + '</div>';
                        }
                        return '<div>' + escape(data.text) + '</div>';
                    },
                },
            }));
        });

        document.addEventListener("DOMContentLoaded", function() {
            var el;
            window.TomSelect && (new TomSelect(el = document.getElementById('select-pendidikan'), {
                copyClassesToDropdown: false,
                dropdownClass: 'dropdown-menu ts-dropdown',
                optionClass: 'dropdown-item',
                controlInput: '<input>',
                render: {
                    item: function(data, escape) {
                        if (data.customProperties) {
                            return '<div><span class="dropdown-item-indicator">' + data
                                .customProperties + '</span>' + escape(data.text) + '</div>';
                        }
                        return '<div>' + escape(data.text) + '</div>';
                    },
                    option: function(data, escape) {
                        if (data.customProperties) {
                            return '<div><span class="dropdown-item-indicator">' + data
                                .customProperties + '</span>' + escape(data.text) + '</div>';
                        }
                        return '<div>' + escape(data.text) + '</div>';
                    },
                },
            }));
        });

        document.addEventListener("DOMContentLoaded", function() {
            var el;
            window.TomSelect && (new TomSelect(el = document.getElementById('select-sumber-dana'), {
                copyClassesToDropdown: false,
                dropdownClass: 'dropdown-menu ts-dropdown',
                optionClass: 'dropdown-item',
                controlInput: '<input>',
                render: {
                    item: function(data, escape) {
                        if (data.customProperties) {
                            return '<div><span class="dropdown-item-indicator">' + data
                                .customProperties + '</span>' + escape(data.text) + '</div>';
                        }
                        return '<div>' + escape(data.text) + '</div>';
                    },
                    option: function(data, escape) {
                        if (data.customProperties) {
                            return '<div><span class="dropdown-item-indicator">' + data
                                .customProperties + '</span>' + escape(data.text) + '</div>';
                        }
                        return '<div>' + escape(data.text) + '</div>';
                    },
                },
            }));
        });

        document.addEventListener("DOMContentLoaded", function() {
            var el;
            window.TomSelect && (new TomSelect(el = document.getElementById('select-penghasilan-utama'), {
                copyClassesToDropdown: false,
                dropdownClass: 'dropdown-menu ts-dropdown',
                optionClass: 'dropdown-item',
                controlInput: '<input>',
                render: {
                    item: function(data, escape) {
                        if (data.customProperties) {
                            return '<div><span class="dropdown-item-indicator">' + data
                                .customProperties + '</span>' + escape(data.text) + '</div>';
                        }
                        return '<div>' + escape(data.text) + '</div>';
                    },
                    option: function(data, escape) {
                        if (data.customProperties) {
                            return '<div><span class="dropdown-item-indicator">' + data
                                .customProperties + '</span>' + escape(data.text) + '</div>';
                        }
                        return '<div>' + escape(data.text) + '</div>';
                    },
                },
            }));
        });

        document.addEventListener("DOMContentLoaded", function() {
            var el;
            window.TomSelect && (new TomSelect(el = document.getElementById('select-penghasilan-lainnya'), {
                copyClassesToDropdown: false,
                dropdownClass: 'dropdown-menu ts-dropdown',
                optionClass: 'dropdown-item',
                controlInput: '<input>',
                render: {
                    item: function(data, escape) {
                        if (data.customProperties) {
                            return '<div><span class="dropdown-item-indicator">' + data
                                .customProperties + '</span>' + escape(data.text) + '</div>';
                        }
                        return '<div>' + escape(data.text) + '</div>';
                    },
                    option: function(data, escape) {
                        if (data.customProperties) {
                            return '<div><span class="dropdown-item-indicator">' + data
                                .customProperties + '</span>' + escape(data.text) + '</div>';
                        }
                        return '<div>' + escape(data.text) + '</div>';
                    },
                },
            }));
        });

        document.addEventListener("DOMContentLoaded", function() {
            var el;
            window.TomSelect && (new TomSelect(el = document.getElementById('select-pekerjaan'), {
                copyClassesToDropdown: false,
                dropdownClass: 'dropdown-menu ts-dropdown',
                optionClass: 'dropdown-item',
                controlInput: '<input>',
                render: {
                    item: function(data, escape) {
                        if (data.customProperties) {
                            return '<div><span class="dropdown-item-indicator">' + data
                                .customProperties + '</span>' + escape(data.text) + '</div>';
                        }
                        return '<div>' + escape(data.text) + '</div>';
                    },
                    option: function(data, escape) {
                        if (data.customProperties) {
                            return '<div><span class="dropdown-item-indicator">' + data
                                .customProperties + '</span>' + escape(data.text) + '</div>';
                        }
                        return '<div>' + escape(data.text) + '</div>';
                    },
                },
            }));
        });

        document.addEventListener("DOMContentLoaded", function() {
            var el;
            window.TomSelect && (new TomSelect(el = document.getElementById('select-kewarganegaraan'), {
                copyClassesToDropdown: false,
                dropdownClass: 'dropdown-menu ts-dropdown',
                optionClass: 'dropdown-item',
                controlInput: '<input>',
                render: {
                    item: function(data, escape) {
                        if (data.customProperties) {
                            return '<div><span class="dropdown-item-indicator">' + data
                                .customProperties + '</span>' + escape(data.text) + '</div>';
                        }
                        return '<div>' + escape(data.text) + '</div>';
                    },
                    option: function(data, escape) {
                        if (data.customProperties) {
                            return '<div><span class="dropdown-item-indicator">' + data
                                .customProperties + '</span>' + escape(data.text) + '</div>';
                        }
                        return '<div>' + escape(data.text) + '</div>';
                    },
                },
            }));
        });
    </script>
@endpush

<script>
    // JS Image Preview
    function previewPhotoKtp() {
        const image = document.getElementById('photo_ktp');
        const imgPreview = document.querySelector('.img-preview-ktp');

        imgPreview.style.display = 'block';

        const oFReader = new FileReader();
        oFReader.readAsDataURL(image.files[0]);

        oFReader.onload = function(oFREvent) {
            imgPreview.src = oFREvent.target.result;
        }
    }

    function previewPhotoKk() {
        const image = document.getElementById('photo_kk');
        const imgPreview = document.querySelector('.img-preview-kk');

        imgPreview.style.display = 'block';

        const oFReader = new FileReader();
        oFReader.readAsDataURL(image.files[0]);

        oFReader.onload = function(oFREvent) {
            imgPreview.src = oFREvent.target.result;
        }
    }

    function previewPhoto() {
        const image = document.getElementById('photo');
        const imgPreview = document.querySelector('.img-preview');

        imgPreview.style.display = 'block';

        const oFReader = new FileReader();
        oFReader.readAsDataURL(image.files[0]);

        oFReader.onload = function(oFREvent) {
            imgPreview.src = oFREvent.target.result;
        }
    }

    function previewPhotoSelfi() {
        const image = document.getElementById('photo_selfie');
        const imgPreview = document.querySelector('.img-preview2');

        imgPreview.style.display = 'block';

        const oFReader = new FileReader();
        oFReader.readAsDataURL(image.files[0]);

        oFReader.onload = function(oFREvent) {
            imgPreview.src = oFREvent.target.result;
        }
    }
</script>

@section('scr')
    <script>
        $(document).ready(function() {
            //Ambil data kabupaten
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
                        var ch = []
                        for (var i = 0; i < obj.length; i++) {
                            if (ch.indexOf(obj[i].kelurahan) === -1) {
                                ch.push(obj[i].kelurahan)

                            }
                        }

                        ch.sort()

                        ch.forEach(data => {
                            $('#select-kelurahan').append($('<option>', {
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
        });
    </script>
@endsection
