$('#form-pengajuan').submit(function(event) {
    event.preventDefault();
    var no_id = $('#no_identitas').val();

    $.ajax({
        url: '/nasabah/cekdata/'+no_id,
        type: 'GET',
        dataType: "json",
        cache: false,
        success: function(response){
            
            if (Object.keys(response).length != 0) {
                Swal.fire({
                    title: 'Apakah Anda akan menambahkan data?',
                    html: 'Nama <strong>' + response.nama_nasabah + '</strong> '+'dengan NIK <strong>'+' '+response.no_identitas+' '+'</strong> sudah terdaftar',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya!!!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#form-pengajuan').unbind('submit').submit();
                    }   
                });
            }else{
                $('#form-pengajuan').unbind('submit').submit();
            }
        },
        error: function (xhr, status, error) {
            console.error("Error:", xhr.responseText);
        },
    })

});