<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\AttendanceWindow;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupervisorController extends Controller
{
    // Supervisor Dashboard
    public function index()
    {
        $openWindow = AttendanceWindow::where('supervisor_id', Auth::id())
            ->where('is_open', true)
            ->latest()
            ->first();

        // Student list - roles() use korini, tai error asbe na
        $students = User::where('organisation_id', Auth::user()->organisation_id ?? 1)
            ->latest()
            ->take(20)
            ->get();

        return view('supervisor.dashboard', compact('openWindow', 'students'));
    }

    // Student List
    public function studentList()
    {
        $students = User::where('organisation_id', Auth::user()->organisation_id ?? 1)
            ->latest()
            ->paginate(20);

        return view('supervisor.students', compact('students'));
    }

    // Ekta Student dekhbe
    public function show($id)
    {
        $student = User::findOrFail($id);
        return view('supervisor.student-show', compact('student'));
    }

    // Student Update
    public function update(Request $request, $id)
    {
        $student = User::findOrFail($id);
        $student->update($request->only(['name', 'email', 'phone']));
        return back()->with('success', 'Student update hoye geche.');
    }

    // Student Delete
    public function destroy($id)
    {
        $student = User::findOrFail($id);
        $student->delete();
        return back()->with('success', 'Student delete hoye geche.');
    }

    // WINDOW OPEN - Supervisor khulbe, delete hobe na
    public function openWindow(Request $request)
    {
        // Ager khola window thakle sudhu CLOSE koro
        AttendanceWindow::where('supervisor_id', Auth::id())
            ->where('is_open', true)
            ->update(['is_open' => false, 'closed_at' => now()]);

        // Notun window kholo
        $window = AttendanceWindow::create([
            'organisation_id' => Auth::user()->organisation_id ?? 1,
            'supervisor_id' => Auth::id(),
            'subject' => $request->subject ?? 'Attendance',
            'duration_minutes' => $request->duration_minutes ?? 10,
            'is_open' => true,
            'opened_at' => now(),
        ]);

        return back()->with('success', 'Supervisor Window OPEN holo! ID: '.$window->id.' - Student ra ekhon self attendance dite parbe.');
    }

    // WINDOW CLOSE - Delete hobe na, abar khola jabe
    public function closeWindow()
    {
        AttendanceWindow::where('supervisor_id', Auth::id())
            ->where('is_open', true)
            ->update(['is_open' => false, 'closed_at' => now()]);

        return back()->with('success', 'Window CLOSE holo, delete hoyni. Abar khola jabe.');
    }
}