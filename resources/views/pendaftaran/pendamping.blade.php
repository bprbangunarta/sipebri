@extends('templates.app')
@section('title', 'Data Pendamping')

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
                                        <a href="{{ route('pendaftaran.index') }}" class="btn btn-primary">
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
                                            <a href="/pendaftaran/edit"
                                                class="list-group-item list-group-item-action d-flex align-items-center">Data
                                                Pemohon</a>
                                            <a href="/pendaftaran/pendamping"
                                                class="list-group-item list-group-item-action d-flex align-items-center active">Data
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
                                                <div class="form-label">Nomor KTP</div>
                                                <input type="number" class="form-control">
                                            </div>
                                            <div class="col-md">
                                                <div class="form-label">Nama Lengkap</div>
                                                <input type="text" class="form-control">
                                            </div>
                                        </div>
                                        <p></p>
                                        <div class="row g-3">
                                            <div class="col-md">
                                                <div class="form-label">Tempat Lahir</div>
                                                <input type="text" class="form-control">
                                            </div>
                                            <div class="col-md">
                                                <div class="form-label">Tanggal Lahir</div>
                                                <input type="date" class="form-control">
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
                                        <p></p>
                                        <div class="row g-3">
                                            <div class="col-md">
                                                <div class="form-label">Status</div>
                                                <select class="form-control" name="" id="">
                                                    <option value="">--Pilih--</option>
                                                </select>
                                            </div>
                                            <div class="col-md">
                                                <div class="form-label">Tanggungan</div>
                                                <select class="form-control" name="" id="">
                                                    <option value="">--Pilih--</option>
                                                </select>
                                            </div>
                                            <div class="col-md">
                                                <div class="form-label">Pisah Harta</div>
                                                <select class="form-control" name="" id="">
                                                    <option value="">--Pilih--</option>
                                                </select>
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
</script>
@endpush