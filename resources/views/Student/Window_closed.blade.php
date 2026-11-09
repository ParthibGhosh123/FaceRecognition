{{-- C:\Users\ADMIN\facial\facial\resources\views\student\window-closed.blade.php --}}

@extends('layouts.app')

@section('content')
<div style="text-align:center; margin-top:50px;">

    <h2 style="color:red;">Attendance Window Closed ❌</h2>
    <h3>BLOCKED - Supervisor Window khole ni</h3>

    {{-- Controller theke asa error dekhabe --}}
    @if(session('error'))
        <p style="color:red; font-size:18px;">{{ session('error') }}</p>
    @endif

    @if(isset($error))
        <p style="color:red; font-size:18px;">{{ $error }}</p>
    @endif

    <p>Supervisor ke bolo window khulte, tarpor abar try koro.</p>

    <br>
    <a href="{{ url('/student/attendance') }}">
        <button>Abar Check Koro</button>
    </a>

    <br><br>
    <a href="{{ url('/student/history') }}">Amar Attendance History</a>

</div>
@endsection