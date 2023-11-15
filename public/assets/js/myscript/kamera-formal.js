$(document).ready(function(){
    let stream; // Variabel untuk menyimpan objek stream kamera
            let videoElement; // Variabel untuk menyimpan elemen video

            $("#kamera-formal").on("show.bs.modal", async function(event) {
                try {
                    stream = await navigator.mediaDevices.getUserMedia({
                        video: true,
                        audio: false
                    });
                    videoElement = document.getElementById('video-formal');
                    videoElement.style.display = 'block';
                    videoElement.srcObject = stream;
                } catch (error) {
                    // console.error('Gagal membuka kamera:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Memuat Kamera',
                        text: 'Pastikan perizin kamera telah diberikan.',
                    });
                }

                // Anda bisa memanggil fungsi ambilGambar di sini jika diperlukan
                $("#kamera-formal").on("hide.bs.modal", function(event) {
                    if (stream) {
                        const tracks = stream.getTracks();
                        tracks.forEach(track => track.stop());
                        stream = null;
                    }
                });

                // Reset atau kosongkan canvas saat modal ditampilkan kembali
                const canvas = document.getElementById('canvas-formal');
                const context = canvas.getContext('2d');
                canvas.width = 0;
                canvas.height = 0;


                // const canvas = document.getElementById('canvas-kk');
                const imgPreviewContainer = document.querySelector(
                    '.img-preview-container-formal');
                const imgPreview_kk = document.querySelector('.img-preview');


                $('#ambilGambarBtn-formal').on('click', function() {
                    $('#canvas-formal').show()
                    if (stream && videoElement && imgPreviewContainer && imgPreview_kk && canvas) {
                        // Menghentikan stream kamera
                        const tracks = stream.getTracks();
                        tracks.forEach(track => track.stop());

                        const context = canvas.getContext('2d');
                        canvas.width = videoElement.videoWidth;
                        canvas.height = videoElement.videoHeight;

                        context.drawImage(videoElement, 0, 0, canvas.width, canvas.height);

                        videoElement.style.display = 'none';
                        canvas.style.display = 'block';
                        imgPreviewContainer.style.display = 'block';
                        imgPreview_kk.src = canvas.toDataURL('image/png');
                        const imageDataURL = canvas.toDataURL('image/png');
                        $('#fhotoformal').val(imageDataURL)
                        stream = null;

                    } else {
                        console.error(
                            'Variabel stream, videoElement, atau gambarHasil tidak dideklarasikan atau diinisialisasi dengan benar.'
                        );

                    }
                })
            });
})