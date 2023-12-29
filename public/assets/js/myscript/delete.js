$(document).ready(function () {
    $(".confirmdelete").click(function (event) {
        event.preventDefault();
        var form = $(this).closest("form");

        Swal.fire({
            title: "Apakah anda yakin?",
            text: "Ingin menghapus data",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, Hapus!",
            cancelButtonText: "Batal",
        }).then((willDelete) => {
            if (willDelete.isConfirmed) {
                form.submit();
            }
        });
    });
});
