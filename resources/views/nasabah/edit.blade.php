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
                                                class="icon icon-tabler icon-tabler-arrow-left" width="24" height="24"
                                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                                stroke-linecap="round" stroke-linejoin="round">
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
                                        @include('templates.menu-pendaftaran')
                                    </div>
                                </div>

                                <div class="col d-flex flex-column">
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md">
                                                <div class="form-label">No CIF</div>
                                                <input type="text" class="form-control" name="no_cif" id="no_cif"
                                                    placeholder="00133323711" disabled>
                                            </div>
                                            <div class="col-md">
                                                <div class="form-label">Jenis ID</div>
                                                <select type="text" class="form-select" placeholder="Pilih Identitas"
                                                    name="identitas" id="select-identitas">
                                                    <option value="">Pilih Identitas</option>
                                                    <option value="1">KTP</option>
                                                    <option value="2">SIM</option>
                                                    <option value="3">Pasport</option>
                                                    <option value="9">Lainnya</option>
                                                </select>
                                            </div>
                                            <div class="col-md">
                                                <div class="form-label">No Identitas</div>
                                                <input type="text" class="form-control" name="no_identitas"
                                                    id="no_identitas" placeholder="3213XXXXX">
                                            </div>
                                            <div class="col-md">
                                                <div class="form-label">Masa Identitas</div>
                                                <input type="date" class="form-control" name="masa_identitas"
                                                    id="masa_identitas">
                                            </div>
                                        </div>
                                        <p></p>
                                        <div class="row g-3">
                                            <div class="col-md">
                                                <div class="form-label">Nama Panggilan</div>
                                                <input type="text" class="form-control" name="nama_panggilan"
                                                    id="nama_panggilan" placeholder="Nama Panggilan">
                                            </div>
                                            <div class="col-md">
                                                <div class="form-label">Nama Lengkap</div>
                                                <input type="text" class="form-control" name="nama_nasabah"
                                                    id="nama_nasabah" placeholder="Nama Lengkap">
                                            </div>
                                            <div class="col-md">
                                                <div class="form-label">Tempat Lahir</div>
                                                <input type="text" class="form-control" name="tempat_lahir"
                                                    id="tempat_lahir" placeholder="Tempat Lahir">
                                            </div>
                                            <div class="col-md">
                                                <div class="form-label">Tanggal Lahir</div>
                                                <input type="date" class="form-control" name="tempat_lahir"
                                                    id="tempat_lahir">
                                            </div>
                                        </div>
                                        <p></p>
                                        <div class="row g-3">
                                            <div class="col-md">
                                                <div class="form-label">Kabupaten</div>
                                                <select type="text" class="form-select" placeholder="Pilih Kabupaten"
                                                    name="kode_dati" id="select-kabupaten">
                                                    <option value="">Pilih Kabupaten</option>
                                                    <option value="Subang">Subang</option>
                                                    <option value="dll">Dll</option>
                                                </select>
                                            </div>
                                            <div class="col-md">
                                                <div class="form-label">Kecamatan</div>
                                                <select type="text" class="form-select" placeholder="Pilih Kecamatan"
                                                    name="kode_kecamatan" id="select-kecamatan">
                                                    <option value="">Pilih Kecamatan</option>
                                                    <option value="Pagaden">Pagaden</option>
                                                    <option value="dll">Dll</option>
                                                </select>
                                            </div>
                                            <div class="col-md">
                                                <div class="form-label">Kelurahan</div>
                                                <select type="text" class="form-select" placeholder="Pilih Kelurahan"
                                                    name="kode_kelurahan" id="select-kelurahan">
                                                    <option value="">Pilih Kelurahan</option>
                                                    <option value="Sukamulya">Sukamulya</option>
                                                    <option value="dll">Dll</option>
                                                </select>
                                            </div>
                                            <div class="col-md">
                                                <div class="form-label">Kota</div>
                                                <input class="form-control" type="text" name="kota" id="kota"
                                                    placeholder="Kota">
                                            </div>
                                        </div>
                                        <p></p>
                                        <div class="row g-3">
                                            <div class="col-md">
                                                <div class="form-label">Alamat KTP</div>
                                                <textarea class="form-control" name="alamat_ktp" id="alamat_ktp"
                                                    placeholder="Alamat Lengkap"></textarea>
                                            </div>
                                            <div class="col-md">
                                                <div class="form-label">Alamat Sekarang</div>
                                                <textarea class="form-control" name="alamat_sekarang"
                                                    id="alamat_sekarang" placeholder="Alamat Lengkap"></textarea>
                                            </div>
                                        </div>
                                        <p></p>
                                        <div class="row g-3">
                                            <div class="col-md">
                                                <div class="form-label">Agama</div>
                                                <select type="text" class="form-select" placeholder="Pilih Agama"
                                                    name="agama" id="select-agama">
                                                    <option value="">Pilih Agama</option>
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
                                                <select type="text" class="form-select" placeholder="Pilih Kelamin"
                                                    name="jenis_kelamin" id="select-kelamin">
                                                    <option value="">Pilih Kalamin</option>
                                                    <option value="1">Pria</option>
                                                    <option value="2">Wanita</option>
                                                </select>
                                            </div>
                                            <div class="col-md">
                                                <div class="form-label">Kewarganegaraan</div>
                                                <select type="text" class="form-select" placeholder="Kewarganegaraan"
                                                    name="kewarganegaraan" id="select-kewarganegaraan">
                                                    <option value="">Pilih Kewarganegaraan</option>
                                                    <option value="WNI">Warga Negara Indonesia</option>
                                                    <option value="WNA">Warga Negara Asing</option>
                                                </select>
                                            </div>
                                        </div>
                                        <p></p>
                                        <div class="row g-3">
                                            <div class="col-md">
                                                <div class="form-label">Gelar</div>
                                                <select type="text" class="form-select" placeholder="Pilih Pendidikan"
                                                    name="pendidikan_kode" id="select-pendidikan">
                                                    <option value="">Pilih Pendidikan</option>
                                                    <option value="0100">Tanpa Gelar</option>
                                                    <option value="0299">Lainnya - Perusahaan</option>
                                                </select>
                                            </div>
                                            <div class="col-md">
                                                <div class="form-label">Status</div>
                                                <select type="text" class="form-select" placeholder="Pilih Status"
                                                    name="status_pernikahan" id="select-status">
                                                    <option value="">Pilih Status</option>
                                                    <option value="M">Menikah</option>
                                                    <option value="L">Lajang</option>
                                                    <option value="D">Duda</option>
                                                    <option value="J">Janda</option>
                                                </select>
                                            </div>
                                            <div class="col-md">
                                                <div class="form-label">Pekerjaan</div>
                                                <select type="text" class="form-select" placeholder="Pilih Pekerjaan"
                                                    name="perkerjaan_kode" id="select-pekerjaan">
                                                    <option value="">Pilih Pekerjaan</option>
                                                    <option value="001">Akunting</option>
                                                    <option value="099">Lain-lain</option>
                                                </select>
                                            </div>
                                        </div>
                                        <p></p>
                                        <div class="row g-3">
                                            <div class="col-md">
                                                <div class="form-label">Ibu Kandung</div>
                                                <input type="text" class="form-control" name="nama_ibu_kandung"
                                                    id="nama_ibu_kandung" placeholder="Nama Ibu Kandung">
                                            </div>
                                            <div class="col-md">
                                                <div class="form-label">Nomor Rekening</div>
                                                <input type="number" class="form-control" name="no_rekening"
                                                    id="no_rekening" placeholder="No Rekening">
                                            </div>
                                            <div class="col-md">
                                                <div class="form-label">Nomor NPWP</div>
                                                <input type="number" class="form-control" name="no_npwp" id="no_npwp"
                                                    placeholder="No NPWP">
                                            </div>
                                        </div>
                                        <p></p>
                                        <div class="row g-3">
                                            <div class="col-md">
                                                <div class="form-label">Nomor Telp</div>
                                                <input type="number" class="form-control" name="no_telp" id="no_telp"
                                                    placeholder="0823XXXXX">
                                            </div>
                                            <div class="col-md">
                                                <div class="form-label">No Telp Darurat</div>
                                                <input type="number" class="form-control" name="no_telp_darurat"
                                                    id="no_telp_darurat" placeholder="0823XXXXX">
                                            </div>
                                            <div class="col-md">
                                                <div class="form-label">Alamat Email</div>
                                                <input type="email" class="form-control" name="email" id="email"
                                                    placeholder="namalengkap@gmail.com">
                                            </div>
                                        </div>
                                        <p></p>
                                        <div class="row g-3">
                                            <div class="col-md">
                                                <div class="form-label">Sumber Dana</div>
                                                <select type="text" class="form-select" placeholder="Sumber Dana"
                                                    name="sumber_dana" id="select-sumber-dana">
                                                    <option value="">Sumber Dana</option>
                                                    <option value="Hibah">Hibah</option>
                                                    <option value="Lain2">Lain2</option>
                                                    <option value="Penghasilan">Penghasilan</option>
                                                    <option value="Warisan">Warisan</option>
                                                </select>
                                            </div>
                                            <div class="col-md">
                                                <div class="form-label">Penghasilan Utama</div>
                                                <select type="text" class="form-select" placeholder="Penghasilan Utama"
                                                    name="penghasilan_utama" id="select-penghasilan-utama">
                                                    <option value="">Penghasilan Utama</option>
                                                    <option value="1">s/d 2,5 jt</option>
                                                    <option value="2">s/d 2,5 - 5 jt</option>
                                                    <option value="3">s/d 5 - 7,5 jt</option>
                                                    <option value="4">s/d 7,5 - 10 jt</option>
                                                    <option value="5">> 10 jt</option>
                                                </select>
                                            </div>
                                            <div class="col-md">
                                                <div class="form-label">Penghasilan Lainnya</div>
                                                <select type="text" class="form-select"
                                                    placeholder="Penghasilan Lainnya" name="penghasilan_lainnya"
                                                    id="select-penghasilan-lainnya">
                                                    <option value="">Penghasilan Lainnya</option>
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
                                                <div class="form-label">Photo Formal</div>
                                                <input type="file" class="form-control" class="photo" id="photo">
                                            </div>
                                            <div class="col-md">
                                                <div class="form-label">Photo Selfie</div>
                                                <input type="file" class="form-control" class="photo_selfie"
                                                    id="photo_selfie">
                                            </div>
                                        </div>

                                        <hr style="margin-top: 25px;">
                                        <div class="row g-3" style="margin-top: -30px;">
                                            <div class="col-md">
                                                <div class="form-label">Tempat Kerja</div>
                                                <input type="text" class="form-control" name="tempat_kerja"
                                                    id="tempat_kerja" placeholder="PT. BPR Bangunarta">
                                            </div>
                                            <div class="col-md">
                                                <div class="form-label">No Telp Kantor</div>
                                                <input type="number" class="form-control" name="no_telp_kantor"
                                                    id="no_telp_kantor" placeholder="(0260) 550888">
                                            </div>
                                            <div class="col-md">
                                                <div class="form-label">Nomor Karyawan</div>
                                                <input type="text" class="form-control" name="no_karyawan"
                                                    id="no_karyawan" placeholder="NIK Karyawan">
                                            </div>
                                        </div>

                                    </div>

                                    <div class="card-footer bg-transparent mt-auto">
                                        <div class="btn-list justify-content-end">
                                            <a href="#" class="btn btn-primary">
                                                Simpan
                                            </a>
                                        </div>
                                    </div>
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
    document.addEventListener("DOMContentLoaded", function () {
    	var el;
    	window.TomSelect && (new TomSelect(el = document.getElementById('select-identitas'), {
    		copyClassesToDropdown: false,
    		dropdownClass: 'dropdown-menu ts-dropdown',
    		optionClass:'dropdown-item',
    		controlInput: '<input>',
    		render:{
    			item: function(data,escape) {
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    			option: function(data,escape){
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    		},
    	}));
    });

    document.addEventListener("DOMContentLoaded", function () {
    	var el;
    	window.TomSelect && (new TomSelect(el = document.getElementById('select-kabupaten'), {
    		copyClassesToDropdown: false,
    		dropdownClass: 'dropdown-menu ts-dropdown',
    		optionClass:'dropdown-item',
    		controlInput: '<input>',
    		render:{
    			item: function(data,escape) {
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    			option: function(data,escape){
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    		},
    	}));
    });

    document.addEventListener("DOMContentLoaded", function () {
    	var el;
    	window.TomSelect && (new TomSelect(el = document.getElementById('select-kecamatan'), {
    		copyClassesToDropdown: false,
    		dropdownClass: 'dropdown-menu ts-dropdown',
    		optionClass:'dropdown-item',
    		controlInput: '<input>',
    		render:{
    			item: function(data,escape) {
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    			option: function(data,escape){
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    		},
    	}));
    });

    document.addEventListener("DOMContentLoaded", function () {
    	var el;
    	window.TomSelect && (new TomSelect(el = document.getElementById('select-kelurahan'), {
    		copyClassesToDropdown: false,
    		dropdownClass: 'dropdown-menu ts-dropdown',
    		optionClass:'dropdown-item',
    		controlInput: '<input>',
    		render:{
    			item: function(data,escape) {
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    			option: function(data,escape){
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    		},
    	}));
    });

    document.addEventListener("DOMContentLoaded", function () {
    	var el;
    	window.TomSelect && (new TomSelect(el = document.getElementById('select-status'), {
    		copyClassesToDropdown: false,
    		dropdownClass: 'dropdown-menu ts-dropdown',
    		optionClass:'dropdown-item',
    		controlInput: '<input>',
    		render:{
    			item: function(data,escape) {
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    			option: function(data,escape){
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    		},
    	}));
    });

    document.addEventListener("DOMContentLoaded", function () {
    	var el;
    	window.TomSelect && (new TomSelect(el = document.getElementById('select-kelamin'), {
    		copyClassesToDropdown: false,
    		dropdownClass: 'dropdown-menu ts-dropdown',
    		optionClass:'dropdown-item',
    		controlInput: '<input>',
    		render:{
    			item: function(data,escape) {
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    			option: function(data,escape){
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    		},
    	}));
    });

    document.addEventListener("DOMContentLoaded", function () {
    	var el;
    	window.TomSelect && (new TomSelect(el = document.getElementById('select-agama'), {
    		copyClassesToDropdown: false,
    		dropdownClass: 'dropdown-menu ts-dropdown',
    		optionClass:'dropdown-item',
    		controlInput: '<input>',
    		render:{
    			item: function(data,escape) {
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    			option: function(data,escape){
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    		},
    	}));
    });

    document.addEventListener("DOMContentLoaded", function () {
    	var el;
    	window.TomSelect && (new TomSelect(el = document.getElementById('select-pendidikan'), {
    		copyClassesToDropdown: false,
    		dropdownClass: 'dropdown-menu ts-dropdown',
    		optionClass:'dropdown-item',
    		controlInput: '<input>',
    		render:{
    			item: function(data,escape) {
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    			option: function(data,escape){
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    		},
    	}));
    });

    document.addEventListener("DOMContentLoaded", function () {
    	var el;
    	window.TomSelect && (new TomSelect(el = document.getElementById('select-sumber-dana'), {
    		copyClassesToDropdown: false,
    		dropdownClass: 'dropdown-menu ts-dropdown',
    		optionClass:'dropdown-item',
    		controlInput: '<input>',
    		render:{
    			item: function(data,escape) {
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    			option: function(data,escape){
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    		},
    	}));
    });

    document.addEventListener("DOMContentLoaded", function () {
    	var el;
    	window.TomSelect && (new TomSelect(el = document.getElementById('select-penghasilan-utama'), {
    		copyClassesToDropdown: false,
    		dropdownClass: 'dropdown-menu ts-dropdown',
    		optionClass:'dropdown-item',
    		controlInput: '<input>',
    		render:{
    			item: function(data,escape) {
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    			option: function(data,escape){
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    		},
    	}));
    });

    document.addEventListener("DOMContentLoaded", function () {
    	var el;
    	window.TomSelect && (new TomSelect(el = document.getElementById('select-penghasilan-lainnya'), {
    		copyClassesToDropdown: false,
    		dropdownClass: 'dropdown-menu ts-dropdown',
    		optionClass:'dropdown-item',
    		controlInput: '<input>',
    		render:{
    			item: function(data,escape) {
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    			option: function(data,escape){
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    		},
    	}));
    });

    document.addEventListener("DOMContentLoaded", function () {
    	var el;
    	window.TomSelect && (new TomSelect(el = document.getElementById('select-pekerjaan'), {
    		copyClassesToDropdown: false,
    		dropdownClass: 'dropdown-menu ts-dropdown',
    		optionClass:'dropdown-item',
    		controlInput: '<input>',
    		render:{
    			item: function(data,escape) {
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    			option: function(data,escape){
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    		},
    	}));
    });

    document.addEventListener("DOMContentLoaded", function () {
    	var el;
    	window.TomSelect && (new TomSelect(el = document.getElementById('select-kewarganegaraan'), {
    		copyClassesToDropdown: false,
    		dropdownClass: 'dropdown-menu ts-dropdown',
    		optionClass:'dropdown-item',
    		controlInput: '<input>',
    		render:{
    			item: function(data,escape) {
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    			option: function(data,escape){
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    		},
    	}));
    });
</script>
@endpush