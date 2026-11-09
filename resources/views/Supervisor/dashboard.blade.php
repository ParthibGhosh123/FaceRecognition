<!DOCTYPE html>
<html>
<head>
    <title>Supervisor Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body{font-family:Arial;background:#f4f6f9;padding:20px}
        .box{background:#fff;padding:20px;border-radius:12px;max-width:700px;margin:auto}
        button{background:#000;color:#fff;padding:12px 20px;border:none;border-radius:8px;cursor:pointer;width:100%;font-size:16px}
        input{width:100%;padding:12px;margin:5px 0 10px;border:1px solid #ddd;border-radius:8px}
        .success{background:#d4edda;padding:10px;border-radius:8px;margin-bottom:10px}
        .open{background:#d4edda;padding:10px;border-radius:8px}
        table{width:100%;margin-top:15px;border-collapse:collapse}
        td,th{border-bottom:1px solid #eee;padding:8px;text-align:left}
    </style>
</head>
<body>
<div class="box">
    <h2>Supervisor Dashboard</h2>
    <p>Supervisor: {{ auth()->user()->name ?? '' }} | {{ auth()->user()->email ?? '' }}</p>

    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div style="background:#f8d7da;padding:10px;border-radius:8px;margin-bottom:10px">{{ session('error') }}</div>
    @endif

    @if($openWindow ?? null)
        <div class="open">
            <h3>🟢 Window OPEN ache</h3>
            <p>Subject: <b>{{ $openWindow->subject }}</b> | ID: {{ $openWindow->id }}</p>
            <p>Student ra ekhon self attendance dite parbe.</p>
        </div>
        <br>
        <form method="POST" action="{{ route('supervisor.window.close') }}">
            @csrf
            <button type="submit" style="background:#dc3545">CLOSE Window</button>
        </form>
    @else
        <h3>🔴 Window CLOSE ache</h3>
        <p>After open the Attendance Window students will give attendance.</p>
        <form method="POST" action="{{ route('supervisor.window.open') }}">
            @csrf
            <label>Subject</label>
            <input type="text" name="subject" placeholder="Subject likho - jemon: Math" required>
            <label>Duration (minute)</label>
            <input type="number" name="duration_minutes" value="10" required>
            <button type="submit">OPEN Attendance Window</button>
        </form>
    @endif

    <h3 style="margin-top:25px">Students ({{ count($students ?? []) }})</h3>
    <table>
        <tr><th>Name</th><th>Email</th></tr>
        @forelse($students ?? [] as $s)
            <tr><td>{{ $s->name }}</td><td>{{ $s->email }}</td></tr>
        @empty
            <tr><td colspan="2">No students found.</td></tr>
        @endforelse
    </table>

    <br>
    <a href="{{ route('student.attendance.create') }}">See Student Attendance Page</a> |
    <a href="/logout" onclick="event.preventDefault();document.getElementById('logout-form').submit();">Logout</a>
    <form id="logout-form" action="/logout" method="POST" style="display:none">@csrf</form>
</div>
</body>
</html>