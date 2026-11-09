<!DOCTYPE html>
<html>
<head>
    <title>Student Attendance</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body{font-family:Arial;background:#f4f6f9;padding:20px}
        .box{background:#fff;padding:20px;border-radius:12px;max-width:500px;margin:auto;text-align:center}
        video{width:100%;border-radius:12px;background:#000}
        canvas{display:none}
        img{width:100%;border-radius:12px;margin-top:10px}
        button{background:#000;color:#fff;padding:14px;border:none;border-radius:8px;width:100%;font-size:16px;margin-top:10px;cursor:pointer}
        .success{background:#d4edda;padding:10px;border-radius:8px;margin-bottom:10px}
        .error{background:#f8d7da;padding:10px;border-radius:8px;margin-bottom:10px}
    </style>
</head>
<body>
<div class="box">
    <h2>Student Self Attendance</h2>

    @if(session('success')) <div class="success">{{ session('success') }}</div> @endif
    @if(session('error')) <div class="error">{{ session('error') }}</div> @endif

    @if($openWindow ?? null)
        <h3 style="color:green">🟢 Window OPEN - {{ $openWindow->subject }} | ID: {{ $openWindow->id }}</h3>

        @if($already ?? false)
            <p>Tumi agei attendance diye diyecho.</p>
        @else
            <!-- CAMERA -->
            <video id="video" autoplay playsinline></video>
            <canvas id="canvas"></canvas>
            <img id="preview" style="display:none">

            <button type="button" onclick="capture()">📸 Photo Tolo</button>

            <form method="POST" action="{{ route('student.attendance.store') }}" id="attForm">
                @csrf
                <input type="hidden" name="photo" id="photoInput">
                <button type="submit" id="submitBtn" disabled>✅ Attendance Dao</button>
            </form>
            <p id="msg">Age camera te mukh dekhao, tarpor Photo Tolo tip dao</p>

            <script>
                let video = document.getElementById('video');
                // Camera ON
                navigator.mediaDevices.getUserMedia({ video: true })
                .then(stream => { video.srcObject = stream; })
                .catch(err => { document.getElementById('msg').innerText = 'Camera on hocche na: ' + err; });

                function capture(){
                    let canvas = document.getElementById('canvas');
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    canvas.getContext('2d').drawImage(video, 0, 0);
                    let data = canvas.toDataURL('image/jpeg');
                    document.getElementById('photoInput').value = data;
                    let prev = document.getElementById('preview');
                    prev.src = data;
                    prev.style.display = 'block';
                    document.getElementById('submitBtn').disabled = false;
                    document.getElementById('msg').innerText = 'Photo hoye geche! Ebar Attendance Dao tip dao.';
                }
            </script>
        @endif
    @else
        <h3 style="color:red">🔴 Kono Window khola nei</h3>
        <p>Supervisor OPEN korle ekhane Camera asbe.</p>
    @endif
</div>
</body>
</html>