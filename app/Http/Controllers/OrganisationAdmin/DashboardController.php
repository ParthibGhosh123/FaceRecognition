<?php
namespace App\Http\Controllers\OrganisationAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Attendance;

class DashboardController extends Controller
{
    // Organisation Admin can see the data of own Institute 
    public function index()
    {
        $orgId = auth()->user()->organisation_id;

        $students = Student::where('organisation_id', $orgId)->latest()->get();
        $todayAttendance = Attendance::where('organisation_id', $orgId)
                            ->whereDate('date', today())->count();

        // THIK KORA - age chilo organisation-admin, ekhon organisationadmin
        return view('organisationadmin.dashboard', compact('students', 'todayAttendance'));
    }

    public function storeStudent(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'roll' => 'required',
        ]);

        Student::create([
            'name' => $request->name,
            'roll' => $request->roll,
            'organisation_id' => auth()->user()->organisation_id,
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Student added to my Institute');
    }

    // web.php te ei 2 to route ache, tai ei 2 to lagbei
    public function updateStudent(Request $request, $id)
    {
        $student = Student::where('id', $id)->where('organisation_id', auth()->user()->organisation_id)->firstOrFail();
        $student->update($request->only(['name','roll']));
        return back()->with('success', 'Student Updated');
    }

    public function deleteStudent($id)
    {
        Student::where('id', $id)->where('organisation_id', auth()->user()->organisation_id)->delete();
        return back()->with('success', 'Student Deleted');
    }

    public function markAttendance(Request $request)
    {
        $request->validate([
            'roll' => 'required',
            'face_match_score' => 'required|numeric',
        ]);

        $orgId = auth()->user()->organisation_id;
        $roll = $request->roll;
        $score = $request->face_match_score;

        // 1.Check the student of Own Institute ?
        $student = Student::where('roll', $roll)
                    ->where('organisation_id', $orgId)
                    ->first();

        if (!$student) {
            return response()->json([
                'show_box' => true,
                'status' => 'REJECTED',
                'reason' => 'Onno Institute er student',
                'match' => round($score * 100) . '%',
            ]);
        }

        // 2. Face Match check
        if ($score < 0.60) {
            return response()->json([
                'show_box' => true,
                'status' => 'REJECTED',
                'reason' => 'Face Mileni',
                'match' => round($score * 100) . '%',
            ]);
        }

        // 3. Attendance Save
        Attendance::updateOrCreate(
            ['student_id' => $student->id, 'date' => today()],
            [
                'organisation_id' => $orgId,
                'time' => now()->toTimeString(),
                'status' => 'PRESENT',
            ]
        );

        // 4. Output for Attendance Box 
        return response()->json([
            'show_box' => true,
            'status' => 'PRESENT',
            'roll' => $student->roll,
            'name' => $student->name,
            'match' => round($score * 100) . '%',
            'time' => now()->format('h:i A'),
        ]);
    }
}