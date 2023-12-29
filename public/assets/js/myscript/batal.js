$(document).ready(function () {
    $(".confirmbatal").click(function (event) {
        event.preventDefault();
        var form = $(this).closest("form");

        Swal.fire({
            title: "Apakah anda yakin?",
            text: "Ingin Melakukan Pembatalan",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya!",
            cancelButtonText: "Batal",
        }).then((willDelete) => {
            if (willDelete.isConfirmed) {
                form.submit();
            }
        });
    });
});
