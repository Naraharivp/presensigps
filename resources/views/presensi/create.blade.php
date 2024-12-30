@extends('layouts.presensi')
@section('header')
<div class="appHeader bg-primary text-light">
    <div class="left">
        <a href="/dashboard" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">Presensi</div>
    <div class="right"></div>
</div>

<style>
    .webcam-capture,
    .webcam-capture video {
        display: inline-block;
        width: 100% !important;
        margin: auto;
        height: auto !important;
        border-radius: 10px;
    }

    #map { 
        height: 200px; 
    }

    #faceDetectionStatus {
        text-align: center;
        margin-top: 10px;
        font-weight: bold;
    }
</style>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>
<script src="https://unpkg.com/leaflet.gridlayer.googlemutant/Leaflet.GoogleMutant.js"></script>
@endsection

@section('content')
<div class="row" style="margin-top: 70px">
    <div class="col">
        <input type="hidden" id="lokasi">
        <div class="webcam-capture" id="camera"></div>
        <div id="faceDetectionStatus"></div>
    </div>
</div>
<div class="row">
    <div class="col">
        @if ($cek > 0)
        <button id="takeabsen" class="btn btn-primary btn-block">Presensi Pulang</button>
        @else
        <button id="takeabsen" class="btn btn-primary btn-block">Presensi</button>
        @endif
    </div>
</div>
<div class="row mt-2">
    <div class="col">
        <div id="map"></div>
    </div>
</div>
@endsection

@push('myscript')
<script>
    let faceDetected = false;
    let faceApiLoaded = false;

    // Load face-api models
    Promise.all([
        faceapi.nets.tinyFaceDetector.loadFromUri('/models'),
        faceapi.nets.faceLandmark68Net.loadFromUri('/models'),
        faceapi.nets.faceRecognitionNet.loadFromUri('/models')
    ]).then(() => {
        faceApiLoaded = true;
        console.log('Face-api models loaded successfully');
        startWebcam();
    }).catch(err => console.error('Failed to load face-api models:', err));

    function startWebcam() {
        if (document.getElementById('camera')) {
            Webcam.set({
                width: 640,
                height: 480,
                image_format: 'jpeg',
                jpeg_quality: 90
            });
            Webcam.attach('#camera');  
            console.log('Webcam attached successfully');
            startFaceDetection();
        } else {
            console.error('Camera element not found in the DOM.');
        }
    }

    function startFaceDetection() {
        if (!faceApiLoaded) {
            console.log('Face-api not loaded yet, retrying in 1 second');
            setTimeout(startFaceDetection, 1000);
            return;
        }
        
        const video = document.querySelector('.webcam-capture video');
        if (!video) {
            console.error('Video element not found, retrying in 1 second');
            setTimeout(startFaceDetection, 1000);
            return;
        }

        setInterval(async () => {
            const detections = await faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions({ inputSize: 160, scoreThreshold: 0.5 }));
            faceDetected = detections.length > 0;
            updateFaceDetectionStatus(faceDetected);
            console.log('Face detection result:', detections);
        }, 1000);
    }

    function updateFaceDetectionStatus(detected) {
        const statusElement = document.getElementById('faceDetectionStatus');
        if (detected) {
            statusElement.textContent = 'Wajah Terdeteksi';
            statusElement.style.color = 'green';
        } else {
            statusElement.textContent = 'Wajah Tidak Terdeteksi';
            statusElement.style.color = 'red';
        }
    }

    var lokasi = document.getElementById('lokasi');
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(successCallback, errorCallback);
    } else {
        console.error('Geolocation is not supported by this browser.');
    }

    function successCallback(position) {
        lokasi.value = -7.748125757099419 + "," + 110.35527710020557;
        var map = L.map('map').setView([-7.748125757099419, 110.35527710020557], 16);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 17,
            // attribution: '&copy; <a href="http://www.openstreetmap.org/"></a>'
        }).addTo(map);
        var marker = L.marker([-7.748125757099419, 110.35527710020557]).addTo(map);
        var circle = L.circle([-7.748125757099419, 110.35527710020557], {
            color: 'red',
            fillColor: '#f03',   
            fillOpacity: 0.5,
            radius: 20
        }).addTo(map);
    }

    function errorCallback(error) {
        console.error('Geolocation error:', error);
    }

    $("#takeabsen").click(async function(e) {
        e.preventDefault();
        if (!faceDetected) {
            Swal.fire({
                title: 'Peringatan!',
                text: 'Wajah tidak terdeteksi. Pastikan wajah Anda terlihat jelas di kamera.',
                icon: 'warning',
            });
            return;
        }

        $("#takeabsen").prop('disabled', true).text('Processing...');

        try {
            Webcam.snap(function(uri) {
                image = uri;
            });
            var lokasi = $("#lokasi").val();

            console.log('Data yang akan dikirim:', { image: image.substring(0, 100) + '...', lokasi, faceDetected });

            $.ajax({
                type: 'POST',
                url: '/presensi/store',
                data: {
                    _token: "{{ csrf_token() }}",
                    image: image,
                    lokasi: lokasi,
                    faceDetected: faceDetected
                },
                cache: false,
                success: function(response) {
                    console.log('Server response:', response);
                    var status = response.split("|");
                    if (status[0] == 'success') {
                        Swal.fire({
                            title: 'Success!',
                            text: status[1],
                            icon: 'success',
                        });
                        setTimeout("location.href='/dashboard'", 2000);
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: status[1],
                            icon: 'error',
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', xhr.responseText);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to send data. Error: ' + error,
                        icon: 'error',
                    });
                }
            });
        } catch (error) {
            console.error('Error during attendance process:', error);
            Swal.fire({
                title: 'Error!',
                text: 'Terjadi kesalahan saat proses presensi. Silakan coba lagi.',
                icon: 'error',
            });
        }
        $("#takeabsen").prop('disabled', false).text('Presensi');
    });
</script>
@endpush