<!DOCTYPE html>
<html>
<head>
    <title>Organisation Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body{font-family:Arial;background:#f4f4f4;padding:20px}
        .box{background:white;padding:20px;border-radius:10px;margin-bottom:20px}
        #video{width:100%;max-width:400px;border:2px solid #333;border-radius:10px;background:black}
        #attendanceBox{display:none;padding:15px;border-radius:10px;margin-top:15px}
        .present{background:#d4edda;border:1px solid green}
        .rejected{background:#f8d7da;border:1px solid red}
        button{padding:10px 20px;background:blue;color:white;border:none;border-radius:5px;cursor:pointer}
        input{padding:8px;margin:5px}
    </style>
</head>
<body>

<h2>Organisation Admin Dashboard - Institute ID: {{ auth()->user()->organisation_id }}</h2>
<p>Ajker Attendance: {{ $todayAttendance }} | Total Students: {{ $students->count() }}</p>

<div class="box">
    <h3>Add Student</h3>
    <form method="POST" action="{{ route('organisationadmin.student.store') }}">
        @csrf
        <input type="text" name="name" placeholder="Student Name" required>
        <input type="text" name="roll" placeholder="Roll - jemon 101-45" required>
        <button type="submit">Add Student</button>
    </form>
    @if(session('success')) <p style="color:green">{{ session('success') }}</p> @endif
</div>

<div class="box">
    <h3>Face Attendance</h3>
    <input type="text" id="rollInput" placeholder="Roll likho - 101-45">
    <br><br>
    <video id="video" autoplay playsinline></video>
    <br><br>
    <button onclick="startCamera()">📷 Camera On</button>
    <button onclick="capture()">📸 Capture & Face Scan</button>

    <div id="attendanceBox">
        <h3 id="boxStatus"></h3>
        <p><b>Name:</b> <span id="boxName">-</span></p>
        <p><b>Roll:</b> <span id="boxRoll">-</span></p>
        <p><b>Match:</b> <span id="boxMatch">-</span></p>
        <p><b>Time:</b> <span id="boxTime">-</span></p>
        <p><b>Reason:</b> <span id="boxReason">-</span></p>
    </div>
</div>

<div class="box">
    <h3>Total Students: {{ $students->count() }}</h3>
    <h3>Student List</h3>
    @foreach($students as $s)
        <p>{{ $s->name }} - {{ $s->roll }}</p>
    @endforeach
</div>

<script>
let video = document.getElementById('video');
async function startCamera(){
    let stream = await navigator.mediaDevices.getUserMedia({video:true});
    video.srcObject = stream;
}
async function capture(){
    let roll = document.getElementById('rollInput').value;
    if(!roll){ alert('Age Roll dao'); return; }
    let score = 0.94;
    let res = await fetch("{{ route('organisationadmin.attendance') }}", {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content},
        body: JSON.stringify({roll: roll, face_match_score: score})
    });
    let data = await res.json();
    if(data.show_box){
        let box = document.getElementById('attendanceBox');
        box.style.display = 'block';
        box.className = data.status === 'PRESENT' ? 'present' : 'rejected';
        document.getElementById('boxStatus').innerText = data.status === 'PRESENT' ? '✅ PRESENT' : '❌ REJECTED';
        document.getElementById('boxName').innerText = data.name || '-';
        document.getElementById('boxRoll').innerText = data.roll || roll;
        document.getElementById('boxMatch').innerText = data.match || '';
        document.getElementById('boxTime').innerText = data.time || '';
        document.getElementById('boxReason').innerText = data.reason || (data.status === 'PRESENT' ? 'Face Milche' : 'Face Mileni');
    }
}
</script>
</body>
</html>