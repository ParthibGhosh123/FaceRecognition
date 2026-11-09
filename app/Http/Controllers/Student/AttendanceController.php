<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AttendanceWindow;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    // Student form khulbe - Window khola thakle tobei parbe + Camera on hobe
    public function create()
    {
        $openWindow = AttendanceWindow::where('is_open', true)
            ->latest()
            ->first();

        // Window khola nei
        if (!$openWindow) {
            return view('student.attendance', [
                'openWindow' => null,
                'message' => 'Ekhon kono Window khola nei. Supervisor OPEN korle parbe.',
            ]);
        }

        // Eii window te agei attendance diye diyeche kina check
        $already = DB::table('attendances')
            ->where('attendance_window_id', $openWindow->id)
            ->where('student_id', Auth::id())
            ->exists();

        return view('student.attendance', compact('openWindow', 'already'));
    }

    // Student nije attendance debe - Camera Photo soho
    public function store(Request $request)
    {
        $openWindow = AttendanceWindow::where('is_open', true)
            ->latest()
            ->first();

        // 1. Window khola nei - debe na
        if (!$openWindow) {
            return back()->with('error', 'Window khola nei. Supervisor OPEN korle parbe.');
        }

        // 2. Abar check - Window bondho hoye geche kina
        if (!$openWindow->is_open) {
            return back()->with('error', 'Window CLOSE hoye geche.');
        }

        // 3. Ekbar e dite parbe, 2 bar na - Delete hobe na, duplicate o hobe na
        $exists = DB::table('attendances')
            ->where('attendance_window_id', $openWindow->id)
            ->where('student_id', Auth::id())
            ->exists();

        if ($exists) {
            return back()->with('error', 'Tumi eii Window te agei attendance diye diyecho.');
        }

        // 4. Camera Photo save koro - jodi ase
        $photoPath = null;
        if ($request->filled('photo')) {
            try {
                $photoData = $request->photo;
                // data:image/jpeg;base64, part ta bad dao
                if (str_contains($photoData, ',')) {
                    $photoData = explode(',', $photoData, 2)[1];
                }
                $photoData = str_replace(' ', '+', $photoData);
                $fileName = 'attendance_'.Auth::id().'_'.$openWindow->id.'_'.time().'.jpg';
                Storage::disk('public')->put('attendance/'.$fileName, base64_decode($photoData));
                $photoPath = 'attendance/'.$fileName;
            } catch (\Exception $e) {
                // Photo save na holeo attendance debe, atke thakbe na
                $photoPath = null;
            }
        }

        // 5. Attendance save koro
        $data = [
            'attendance_window_id' => $openWindow->id,
            'organisation_id' => $openWindow->organisation_id,
            'student_id' => Auth::id(),
            'subject' => $openWindow->subject,
            'status' => 'present',
            'date' => now()->toDateString(),
            'marked_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // Jodi photo column thake tobei photo save korbo - nahole error asbe na
        if ($photoPath && Schema::hasColumn('attendances', 'photo')) {
            $data['photo'] = $photoPath;
        }

        DB::table('attendances')->insert($data);

        return back()->with('success', 'Self Attendance hoye geche! Subject: '.$openWindow->subject.' | Window ID: '.$openWindow->id);
    }

    // Student nijer history dekhbe - Delete hobe na, sob thakbe
    public function history()
    {
        $history = DB::table('attendances')
            ->where('student_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('student.history', compact('history'));
    }
}