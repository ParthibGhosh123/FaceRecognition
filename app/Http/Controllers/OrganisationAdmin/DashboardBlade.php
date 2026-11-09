@extends('layouts.app')
@section('content')
<div class="container" style="max-width:800px; margin-top:20px;">

    <h3>Organisation Admin Dashboard</h3>
    <p>Today Attendance: <b>{{ $todayAttendance }}</b></p>

    {{-- Student Add --}}
    <div class="card p-3 mb-3">
        <h5>Student Add</h5>
        <form method="POST" action="/organisation-admin/students">
            @csrf
            <input name="name" class="form-control mb-2" placeholder="Name" required>
            <input name="roll" class="form-control mb-2" placeholder="Roll - 101-45" required>
            <button class="btn btn-primary">Add Student</button>
        </form>
    </div>

    {{-- Attendance - Camera + Scan + Box --}}
    <div class="card p-3 mb-3">
        <h5>Attendance - Face Scan</h5>
        <div style="display:flex; gap:10px;">
            <input type="text" id="roll" class="form-control" placeholder="Roll diye Set koro - 101-45">
            <button class="btn btn-primary" onclick="setRoll()">Set</button>
        </div>
        <small id="set-msg" style="color:green;"></small>

        <video id="camera" width="100%" height="280" autoplay style="background:#000; border-radius:10px; margin-top:10px;"></video>
        <button class="btn btn-success mt-2" onclick="captureAndScan()">📸 Capture & Face Scan</button>
        <p id="scan-msg"></p>

        {{-- Attendance Box - Output e asbe --}}
        <div id="attendance-box" style="display:none; border:2px solid green; padding:15px; border-radius:10px; margin-top:10px;">
            <h4 id="box-title"></h4><hr>
            <p><b>Roll:</b> <span id="out-roll"></span></p>
            <p><b>Name:</b> <span id="out-name"></span></p>
            <p><b>Match:</b> <span id="out-match"></span></p>
            <p><b>Time:</b> <span id="out-time"></span></p>
            <p><b>Status:</b> <span id="out-status"></span></p>
            <p id="out-reason" style="color:red; font-weight:bold;"></p>
        </div>
    </div>

    {{-- Student List --}}
    <div class="card p-3">
        <h5>My Institute Students</h5>
        @foreach($students as $s)
            <div style="display:flex; justify-content:space-between; border-bottom:1px solid #eee; padding:8px 0;">
                <span>{{ $s->name }} - {{ $s->roll }}</span>
                <form method="POST" action="/organisation-admin/students/{{ $s->id }}">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger">Delete</button>
                </form>
            </div>
        @endforeach
    </div>

</div>

<script>
let selectedRoll = "";
navigator.mediaDevices.getUserMedia({video:true}).then(s=>document.getElementById('camera').srcObject=s);
function setRoll(){
    selectedRoll = document.getElementById('roll').value;
    if(!selectedRoll){alert('Age Roll dao');return;}
    document.getElementById('set-msg').innerText = "Set: "+selectedRoll+" - Ebar Capture koro";
}
async function captureAndScan(){
    if(!selectedRoll){alert('Age Roll Set koro');return;}
    document.getElementById('scan-msg').innerText="Scanning...";
    let video=document.getElementById('camera');
    let canvas=document.createElement('canvas');
    canvas.width=video.videoWidth; canvas.height=video.videoHeight;
    canvas.getContext('2d').drawImage(video,0,0);
    let imageData=canvas.toDataURL('image/jpeg');

    let faceRes=await fetch('http://127.0.0.1:5000/verify',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({roll:selectedRoll,image:imageData})});
    let faceData=await faceRes.json();

    let res=await fetch('/organisation-admin/attendance',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},body:JSON.stringify({roll:selectedRoll,face_match_score:faceData.face_match_score})});
    let data=await res.json();

    let box=document.getElementById('attendance-box');
    box.style.display='block';
    if(data.status==='PRESENT'){
        box.style.borderColor='green'; box.style.background='#f0fff4';
        document.getElementById('box-title').innerText='✅ ATTENDANCE CONFIRMED';
        document.getElementById('out-roll').innerText=data.roll;
        document.getElementById('out-name').innerText=data.name;
        document.getElementById('out-match').innerText=data.match;
        document.getElementById('out-time').innerText=data.time;
        document.getElementById('out-status').innerText='PRESENT';
        document.getElementById('out-reason').innerText='';
    } else {
        box.style.borderColor='red'; box.style.background='#fff0f0';
        document.getElementById('box-title').innerText='❌ ATTENDANCE REJECTED';
        document.getElementById('out-roll').innerText=selectedRoll;
        document.getElementById('out-name').innerText='-';
        document.getElementById('out-match').innerText=data.match;
        document.getElementById('out-time').innerText='-';
        document.getElementById('out-status').innerText='NOT MARKED';
        document.getElementById('out-reason').innerText=data.reason;
    }
    document.getElementById('scan-msg').innerText='';
}
</script>
@endsection