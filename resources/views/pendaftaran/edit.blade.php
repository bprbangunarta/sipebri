@extends('templates.app')
@section('title', 'Edit Pendaftaran')

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
                                        Master
                                    </div>
                                    <h2 class="page-title">
                                        Permohonan Kredit
                                    </h2>
                                </div>
                                <!-- Page title actions -->
                                <div class="col-auto ms-auto d-print-none">
                                    <div class="btn-list">
                                        <a href="/pendaftaran" class="btn btn-primary">
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
                                        <div class="list-group list-group-transparent">
                                            <a href="/pendaftaran/data/pemohon"
                                                class="list-group-item list-group-item-action d-flex align-items-center active">Data
                                                Pemohon</a>
                                            <a href="/pendaftaran/data/pendamping"
                                                class="list-group-item list-group-item-action d-flex align-items-center">Data
                                                Pendamping</a>
                                            <a href="/pendaftaran/data/kredit"
                                                class="list-group-item list-group-item-action d-flex align-items-center">Data
                                                Kredit</a>
                                            <a href="/pendaftaran/data/agunan"
                                                class="list-group-item list-group-item-action d-flex align-items-center">Data
                                                Agunan</a>
                                            <a href="/pendaftaran/data/survayor"
                                                class="list-group-item list-group-item-action d-flex align-items-center">Data
                                                Survayor</a>
                                            <a href="/pendaftaran/data/konfirmasi"
                                                class="list-group-item list-group-item-action d-flex align-items-center">Konfirmasi
                                                Data</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col d-flex flex-column">
                                    <div class="card-body">

                                        <div class="row g-3">
                                            <div class="col-md">
                                                <div class="form-label">No Identitas</div>
                                                <input type="text" class="form-control" name="no_identitas"
                                                    id="no_identitas">
                                            </div>
                                            <div class="col-md">
                                                <div class="form-label">Nama Lengkap</div>
                                                <input type="text" class="form-control" name="nama_lengkap"
                                                    id="nama_lengkap">
                                            </div>
                                            <div class="col-md">
                                                <div class="form-label">Tanggal Lahir</div>
                                                <input type="date" class="form-control" class="tempat_lahir"
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
                                            <div class="col-md-1">
                                                <div class="form-label">RT</div>
                                                <input type="text" class="form-control">
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-label">RW</div>
                                                <input type="text" class="form-control">
                                            </div>
                                        </div>
                                        <p></p>
                                        <div class="row g-3">
                                            <div class="col-md">
                                                <div class="form-label">Alamat</div>
                                                <textarea class="form-control" name="" id=""></textarea>
                                            </div>
                                        </div>
                                        <p></p>
                                        <div class="row g-3">
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
                                                <div class="form-label">Kalamin</div>
                                                <select type="text" class="form-select" placeholder="Pilih Kelamin"
                                                    name="jenis_kelamin" id="select-kelamin">
                                                    <option value="">Pilih Kalamin</option>
                                                    <option value="1">Pria</option>
                                                    <option value="2">Wanita</option>
                                                </select>
                                            </div>
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
                                                <div class="form-label">Ibu Kandung</div>
                                                <input type="text" class="form-control">
                                            </div>
                                            <div class="col-md">
                                                <div class="form-label">Nomor Rekening</div>
                                                <input type="number" class="form-control">
                                            </div>
                                        </div>
                                        <p></p>
                                        <div class="row g-3">
                                            <div class="col-md">
                                                <div class="form-label">Nomor NPWP</div>
                                                <input type="number" class="form-control">
                                            </div>
                                            <div class="col-md">
                                                <div class="form-label">Nomor HP</div>
                                                <input type="number" class="form-control">
                                            </div>
                                            <div class="col-md">
                                                <div class="form-label">Alamat Email</div>
                                                <input type="text" class="form-control">
                                            </div>
                                        </div>
                                        <p></p>
                                        <div class="row g-3">
                                            <div class="col-md">
                                                <div class="form-label">Sumber Dana</div>
                                                <select class="form-control" name="" id="">
                                                    <option value="">--Pilih--</option>
                                                </select>
                                            </div>
                                            <div class="col-md">
                                                <div class="form-label">Penghasilan Utama</div>
                                                <select class="form-control" name="" id="">
                                                    <option value="">--Pilih--</option>
                                                </select>
                                            </div>
                                            <div class="col-md">
                                                <div class="form-label">Penghasilan Lainnya</div>
                                                <select class="form-control" name="" id="">
                                                    <option value="">--Pilih--</option>
                                                </select>
                                            </div>
                                        </div>
                                        <p></p>
                                        <div class="row g-3">
                                            <div class="col-md">
                                                <div class="form-label">Photo Formal</div>
                                                <input type="file" class="form-control">
                                            </div>
                                            <div class="col-md">
                                                <div class="form-label">Photo Selfie</div>
                                                <input type="file" class="form-control">
                                            </div>
                                        </div>

                                        <hr style="margin-top: 25px;">
                                        <div class="row g-3" style="margin-top: -30px;">
                                            <div class="col-md">
                                                <div class="form-label">Tempat Kerja</div>
                                                <input type="text" class="form-control">
                                            </div>
                                            <div class="col-md">
                                                <div class="form-label">No Telp Kantor</div>
                                                <input type="number" class="form-control">
                                            </div>
                                            <div class="col-md">
                                                <div class="form-label">Nomor Karyawan</div>
                                                <input type="text" class="form-control">
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
</script>
@endpush