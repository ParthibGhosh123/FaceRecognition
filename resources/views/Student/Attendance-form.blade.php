{{-- C:\Users\ADMIN\facial\facial\resources\views\student\attendance-form.blade.php --}}

@extends('layouts.app')

@section('content')
<div style="max-width:500px; margin:20px auto; text-align:center; padding:15px; border:1px solid #ddd; border-radius:10px;">

    {{-- Window Info - Controller theke $window asche --}}
    <h2>Self Attendance ✅</h2>
    <p style="font-size:18px;"><b>Subject:</b> {{ $window->subject }}</p>
    <p style="color:green;"><b>Window: OPEN ✅ - {{ $window->duration_minutes }} min</b></p>

    {{-- Success / Error Message --}}
    @if(session('success'))
        <p style="color:green; background:#e8f5e9; padding:10px; border-radius:5px;">{{ session('success') }}</p>
    @endif

    @if(session('error'))
        <p style="color:red; background:#ffebee; padding:10px; border-radius:5px;">{{ session('error') }}</p>
    @endif

    @if($errors->any())
        <p style="color:red;">{{ $errors->first() }}</p>
    @endif

    {{-- Camera --}}
    <video id="camera" width="320" height="240" autoplay playsinline style="border:2px solid #333; border-radius:10px; background:#000;"></video>
    <br><br>

    <canvas id="canvas" width="320" height="240" style="display:none;"></canvas>

    <button type="button" onclick="takePhoto()" style="padding:10px 20px; background:#2196F3; color:white; border:none; border-radius:5px; cursor:pointer;">
        📸 Chobi Tolo
    </button>

    <p id="photoStatus" style="color:green; font-weight:bold;"></p>

    {{-- Attendance Form - AttendanceController@store e jabe --}}
    <form action="{{ url('/student/attendance') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Chobi ta ekhane jabe - Controller e $request->file('photo') --}}
        <input type="file" name="photo" id="photoInput" accept="image/*" capture="user" required style="margin:10px 0;">

        <br><br>
        <button type="submit" style="padding:12px 25px; background:green; color:white; border:none; border-radius:5px; font-size:16px; cursor:pointer;">
            Attendance Dao ✅
        </button>
    </form>

    <br>
    <a href="{{ url('/student/history') }}">Amar Attendance History</a>

</div>

<script>
// Camera On - Mobile / Laptop 2 toi cholbe
navigator.mediaDevices.getUserMedia({ video: true })
.then(stream => {
    document.getElementById('camera').srcObject = stream;
})
.catch(err => {
    alert('Camera khulche na - Niche theke chobi choose koro');
});

function takePhoto() {
    let video = document.getElementById('camera');
    let canvas = document.getElementById('canvas');
    canvas.getContext('2d').drawImage(video, 0, 0, 320, 240);

    canvas.toBlob(blob => {
        let file = new File([blob], "selfie.jpg", {type: "image/jpeg"});
        let dt = new DataTransfer();
        dt.items.add(file);
        document.getElementById('photoInput').files = dt.files;
        document.getElementById('photoStatus').innerText = 'Chobi tola hoye geche ✅ Ebar Attendance Dao te click koro';
    }, 'image/jpeg', 0.9);
}
</script>
@endsection