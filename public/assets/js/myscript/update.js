$(document).ready(function () {
    $(".confirmupdate").click(function (event) {
        event.preventDefault();
        var form = $(this).closest("form");

        Swal.fire({
            title: "Apakah anda yakin?",
            text: "Akan mengubah password",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, Update",
            cancelButtonText: "Batal",
        }).then((update) => {
            if (update.isConfirmed) {
                form.submit();
            }
        });
    });
});
