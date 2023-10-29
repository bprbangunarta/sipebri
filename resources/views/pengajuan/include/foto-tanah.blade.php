<div class="modal fade" id="modal-foto-tanah">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-blue">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">FOTO AGUNAN TANAH</h4>
            </div>
            <form action="{{ route('taksasi.fhotokendaraan') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">

                    <div class="box-body">
                        <div class="row">
                            <div class="div-left">
                                <div style="margin-top: -15px;">
                                    <span class="fw-bold">
                                        TAMPAK DEPAN
                                        <a href="#" class="pull-right" id="prevdepant"
                                            data-target="depan">PREVIEW</a>
                                    </span>
                                    <input type="text" id="nidt" name="id" hidden>
                                    <input type="text" id="ats_namat" name="nama" hidden>
                                    <input type="text" name="jenis" value="tanah" hidden>
                                    <input type="text" name="name_img_1" id="name_img_1" hidden>
                                    <input class="form-control input-sm form-border text-uppercase" type="file"
                                        name="foto1" accept="image/*">
                                </div>
                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">
                                        TAMPAK KIRI
                                        <a href="#" class="pull-right" id="prevkirit"
                                            data-target="kiri">PREVIEW</a>
                                    </span>
                                    <input type="text" name="name_img_2" id="name_img_2" hidden>
                                    <input class="form-control input-sm form-border text-uppercase" type="file"
                                        name="foto3" accept="image/*">
                                </div>
                            </div>

                            <div class="div-right">
                                <div style="margin-top: -15px;">
                                    <span class="fw-bold">
                                        TAMPAK BELAKANG
                                        <a href="#" class="pull-right" id="prevbelakangt"
                                            data-target="belakang">PREVIEW</a>
                                    </span>
                                    <input type="text" name="name_img_3" id="name_img_3" hidden>
                                    <input class="form-control input-sm form-border text-uppercase" type="file"
                                        name="foto2" accept="image/*">
                                </div>

                                <div style="margin-top: 5px;">
                                    <span class="fw-bold">
                                        TAMPAK KANAN
                                        <a href="#" class="pull-right" id="prevkanant"
                                            data-target="kanan">PREVIEW</a>
                                    </span>
                                    <input type="text" name="name_img_4" id="name_img_4" hidden>
                                    <input class="form-control input-sm form-border text-uppercase" type="file"
                                        name="foto4" accept="image/*">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="margin-top: -10px;">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">BATAL</button>
                    <button type="submit" class="btn btn-primary">SIMPAN</button>
                </div>
            </form>
        </div>
    </div>
</div>
@push('myscript')
    <script>
        $("#modal-foto-tanah").on("show.bs.modal", function(event) {
            $("#prevdepant")
                .off("click")
                .on("click", function(e) {
                    e.preventDefault();
                    var button = $(event.relatedTarget); // Tombol yang membuka modal
                    var token = $('meta[name="csrf-token"]').attr("content");
                    var id = button.data("id").split(","); // Ambil data-id dari tombol

                    var data = {
                        iddata: id[0],
                        no: "foto1",
                        _token: token,
                    };
                    $.ajax({
                        url: "/themes/analisa/jaminan/fhoto/kendaraan/prev",
                        type: "POST",
                        data: data,
                        dataType: "json",
                        success: function(response) {
                            if ($.isEmptyObject(response)) {
                                Swal.fire({
                                    title: "",
                                    text: "Tidak Ada Gambar",
                                    icon: "error",
                                    confirmButtonText: "Ok",
                                });
                            } else {
                                window.open(response, "_blank");
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error:", xhr.responseText);
                        },
                    });
                });

            $("#prevbelakangt")
                .off("click")
                .on("click", function(e) {
                    e.preventDefault();
                    var button = $(event.relatedTarget); // Tombol yang membuka modal
                    var token = $('meta[name="csrf-token"]').attr("content");
                    var id = button.data("id"); // Ambil data-id dari tombol
                    var dataId = id.split(",");
                    var data = {
                        iddata: dataId[0],
                        no: "foto2",
                        _token: token,
                    };
                    $.ajax({
                        url: "/themes/analisa/jaminan/fhoto/kendaraan/prev",
                        type: "POST",
                        data: data,
                        dataType: "json",
                        success: function(response) {
                            if ($.isEmptyObject(response)) {
                                Swal.fire({
                                    title: "",
                                    text: "Tidak Ada Gambar",
                                    icon: "error",
                                    confirmButtonText: "Ok",
                                });
                            } else {
                                window.open(response, "_blank");
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error:", xhr.responseText);
                        },
                    });
                });

            $("#prevkirit")
                .off("click")
                .on("click", function(e) {
                    e.preventDefault();
                    var button = $(event.relatedTarget); // Tombol yang membuka modal
                    var token = $('meta[name="csrf-token"]').attr("content");
                    var id = button.data("id"); // Ambil data-id dari tombol
                    var dataId = id.split(",");
                    var data = {
                        iddata: dataId[0],
                        no: "foto3",
                        _token: token,
                    };
                    $.ajax({
                        url: "/themes/analisa/jaminan/fhoto/kendaraan/prev",
                        type: "POST",
                        data: data,
                        dataType: "json",
                        success: function(response) {
                            if ($.isEmptyObject(response)) {
                                Swal.fire({
                                    title: "",
                                    text: "Tidak Ada Gambar",
                                    icon: "error",
                                    confirmButtonText: "Ok",
                                });
                            } else {
                                window.open(response, "_blank");
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error:", xhr.responseText);
                        },
                    });
                });

            $("#prevkanant")
                .off("click")
                .on("click", function(e) {
                    e.preventDefault();
                    var button = $(event.relatedTarget); // Tombol yang membuka modal
                    var token = $('meta[name="csrf-token"]').attr("content");
                    var id = button.data("id"); // Ambil data-id dari tombol
                    var dataId = id.split(",");
                    var data = {
                        iddata: dataId[0],
                        no: "foto4",
                        _token: token,
                    };
                    $.ajax({
                        url: "/themes/analisa/jaminan/fhoto/kendaraan/prev",
                        type: "POST",
                        data: data,
                        dataType: "json",
                        success: function(response) {
                            if ($.isEmptyObject(response)) {
                                Swal.fire({
                                    title: "",
                                    text: "Tidak Ada Gambar",
                                    icon: "error",
                                    confirmButtonText: "Ok",
                                });
                            } else {
                                window.open(response, "_blank");
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error:", xhr.responseText);
                        },
                    });
                });
        });
    </script>
@endpush
