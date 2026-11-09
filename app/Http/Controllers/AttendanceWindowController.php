<?php
namespace App\Http\Controllers;
use App\Models\AttendanceWindow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceWindowController extends Controller
{
    // Supervisor OPEN korbe
    public function open(Request $request)
    {
        // Ager khola window thakle bondho kore dao - delete na, sudhu close
        AttendanceWindow::where('supervisor_id', Auth::id())
            ->where('is_open', true)
            ->update(['is_open' => false, 'closed_at' => now()]);

        // Notun window kholo - abar asbe
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

    // Supervisor CLOSE korbe - delete hobe na
    public function close(AttendanceWindow $window)
    {
        $window->update(['is_open' => false, 'closed_at' => now()]);
        return back()->with('success', 'Window CLOSE holo, delete hoyni. Abar khola jabe.');
    }
}