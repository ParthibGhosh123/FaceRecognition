<?php

namespace App\Http\Controllers\SuperAdmin\v1;

use App\Http\Controllers\Controller;
use App\Repositories\Teacher\TeacherRepository;
use App\Repositories\User\UserRepository;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    protected $teacherRepo;
    protected $userRepo;

    public function __construct(TeacherRepository $teacherRepo, UserRepository $userRepo)
    {
        $this->teacherRepo = $teacherRepo;
        $this->userRepo = $userRepo;
        $this->middleware('auth');
    }

    //  Teacher List - Super Admin
    public function index(Request $request)
    {
        // Super Admin hole sob, Organisation Admin hole nijer ta
        if (auth()->user()->isSuperAdmin()) {
            $teachers = $this->teacherRepo->getAll();
        } else {
            $teachers = $this->teacherRepo->getByOrganisation(auth()->user()->organisation_id);
        }
        return view('super-admin.teachers.index', compact('teachers'));
    }

    public function create()
    {
        return view('super-admin.teachers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'organisation_id' => 'required|exists:organisations,id',
            'subject' => 'required|string',
            'phone' => 'nullable|string',
        ]);

        // 1. User create
        $user = $this->userRepo->createUser([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'organisation_id' => $validated['organisation_id'],
            'role_id' => 3, // 3 = Teacher - tomar role_id onujayi change koro
        ]);

        // 2.Teacher create
        $this->teacherRepo->create([
            'user_id' => $user->id,
            'organisation_id' => $validated['organisation_id'],
            'subject' => $validated['subject'],
            'phone' => $validated['phone'] ?? null,
        ]);

        return redirect()->route('super-admin.teachers.index')->with('success', 'Teacher created successfully');
    }

    // One Teacher View - If Another Institute then BLOCKED
    public function show($id)
    {
        if (auth()->user()->isSuperAdmin()) {
            $teacher = $this->teacherRepo->find($id);
        } else {
            $teacher = $this->teacherRepo->findByOrg($id, auth()->user()->organisation_id);
        }
        return view('super-admin.teachers.show', compact('teacher'));
    }

    public function destroy($id)
    {
        $teacher = $this->teacherRepo->find($id);
        // User  delete
        $this->userRepo->delete($teacher->user_id);
        $this->teacherRepo->delete($id);

        return back()->with('success', 'Teacher deleted');
    }
}