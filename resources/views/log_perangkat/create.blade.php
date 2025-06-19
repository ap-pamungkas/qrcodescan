<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan QR Code Log Perangkat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h1>Scan QR Code Log Perangkat</h1>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="mb-3">
            <form action="{{ route('log_perangkat.create') }}" method="GET" id="scanForm" class="input-group mb-2">
                <input  type="text" value="{{ $parsedData['keseluruhan'] ?? ''}}" name="qr_data" id="qrInput" class="form-control" placeholder="Masukkan atau scan QR code (contoh: 1111220250620073021234)">
                <button type="submit" class="btn btn-primary">Proses Manual</button>
            </form>
            <button id="scanButton" class="btn btn-info">Scan QR Code</button>
        </div>

        <div id="scannerContainer" class="mb-3" style="display: none;">
            <video id="video" width="100%" height="auto" style="border: 1px solid #000;"></video>
            <canvas id="canvas" style="display: none;"></canvas>
            <button id="stopScanButton" class="btn btn-danger mt-2">Stop Scan</button>
        </div>

        @if (isset($parsedData) && !empty(array_filter($parsedData)))
         {{-- <input type="text" readonly name="qr_data" id="qrInput" class="form-control" placeholder="Masukkan atau scan QR code (contoh: 1111220250620073021234)"> --}}
            <div id="dataDisplayForm">
                <h2>Data Hasil Scan</h2>
                <form action="{{ route('log_perangkat.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        {{-- Menggunakan input hidden untuk data yang akan disimpan --}}
                        <input type="hidden" name="id_opd" value="{{ $parsedData['id_opd'] ?? '' }}">
                        <input type="hidden" name="id_perangkat" value="{{ $parsedData['id_perangkat'] ?? '' }}">
                        <input type="hidden" name="tahun" value="{{ $parsedData['tahun'] ?? '' }}">
                        <input type="hidden" name="bulan" value="{{ $parsedData['bulan'] ?? '' }}">
                        <input type="hidden" name="tanggal" value="{{ $parsedData['tanggal'] ?? '' }}">
                        <input type="hidden" name="jam" value="{{ $parsedData['jam'] ?? '' }}">
                        <input type="hidden" name="menit" value="{{ $parsedData['menit'] ?? '' }}">
                        <input type="hidden" name="detik" value="{{ $parsedData['detik'] ?? '' }}">
                        <input type="hidden" name="karakter_unik" value="{{ $parsedData['karakter_unik'] ?? '' }}">
                        <input type="hidden" name="keseluruhan" value="{{ $parsedData['keseluruhan'] ?? '' }}">
{{--
                        {{-- Tampilkan data dalam format yang user-friendly --}}
                        <div class="col-md-4 mb-3">
                            <label>ID OPD</label>
                            <p class="form-control-static">{{ $parsedData['id_opd'] ?? '-' }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>ID Perangkat</label>
                            <p class="form-control-static">{{ $parsedData['id_perangkat'] ?? '-' }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Tahun</label>
                            <p class="form-control-static">{{ $parsedData['tahun'] ?? '-' }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Bulan</label>
                            <p class="form-control-static">{{ $parsedData['bulan'] ?? '-' }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Tanggal</label>
                            <p class="form-control-static">{{ $parsedData['tanggal'] ?? '-' }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Jam</label>
                            <p class="form-control-static">{{ $parsedData['jam'] ?? '-' }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Menit</label>
                            <p class="form-control-static">{{ $parsedData['menit'] ?? '-' }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Detik</label>
                            <p class="form-control-static">{{ $parsedData['detik'] ?? '-' }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Karakter Unik</label>
                            <p class="form-control-static">{{ $parsedData['karakter_unik'] ?? '-' }}</p>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label>Keseluruhan QR</label>
                            <p class="form-control-static">{{ $parsedData['keseluruhan'] ?? '-' }}</p>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success">Simpan Data Log</button>
                    <a href="{{ route('log_perangkat.create') }}" class="btn btn-secondary">Scan Ulang / Reset</a>
                </form>
            </div>
        @endif
    </div>

    <script>
        const scanButton = document.getElementById('scanButton');
        const stopScanButton = document.getElementById('stopScanButton');
        const scannerContainer = document.getElementById('scannerContainer');
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const qrInput = document.getElementById('qrInput');
        const scanForm = document.getElementById('scanForm'); // Dapatkan elemen form scan
        let stream = null;

        scanButton.addEventListener('click', async () => {
            scannerContainer.style.display = 'block';
            scanButton.style.display = 'none';
            // Sembunyikan juga form input manual saat scanning
            scanForm.style.display = 'none';

            try {
                stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
                video.srcObject = stream;
                video.play();

                video.onloadedmetadata = () => {
                    if (video.videoWidth === 0 || video.videoHeight === 0) {
                        alert('Kamera tidak memberikan data video yang valid. Silakan coba lagi.');
                        stopScan();
                        return;
                    }
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    scanQRCode();
                };
            } catch (err) {
                alert('Gagal mengakses kamera: ' + err.message);
                stopScan();
            }
        });

        stopScanButton.addEventListener('click', stopScan);

        function stopScan() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
            scannerContainer.style.display = 'none';
            scanButton.style.display = 'block';
            // Tampilkan kembali form input manual saat stop scan
            scanForm.style.display = 'flex'; // Menggunakan flex karena awalnya input-group mb-2
        }

        function scanQRCode() {
            const ctx = canvas.getContext('2d');

            const tick = () => {
                if (!stream) return;

                if (video.readyState === video.HAVE_ENOUGH_DATA && video.videoWidth > 0 && video.videoHeight > 0) {
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                    const code = jsQR(imageData.data, imageData.width, imageData.height, {
                        inversionAttempts: 'dontInvert',
                    });


                    if (code) {
                        qrInput.value = code.data;
                        console.log('QR Code detected:', );

                        // Stop the camera
                        stopScan();
                        // Submit the scanForm (GET request) after a short delay
                        // This sends the QR data to the controller for parsing
                        setTimeout(() => {
                            scanForm.submit();
                        }, 50);
                        return; // Stop the animation loop
                    }


                }
                requestAnimationFrame(tick);
            };
            requestAnimationFrame(tick);
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
