<?php

namespace App\Http\Controllers\SuperAdmin\v1;

use App\Http\Controllers\Controller;
use App\Repositories\Student\StudentRepository;
use App\Repositories\User\UserRepository;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    protected $studentRepo;
    protected $userRepo;

    public function __construct(StudentRepository $studentRepo, UserRepository $userRepo)
    {
        $this->studentRepo = $studentRepo;
        $this->userRepo = $userRepo;
        $this->middleware('auth');
    }

    // Sob Student List
    public function index(Request $request)
    {
        if (auth()->user()->isSuperAdmin()) {
            $students = $this->studentRepo->getAll();
        } else {
            $students = $this->studentRepo->getByOrganisation(auth()->user()->organisation_id);
        }
        return view('super-admin.students.index', compact('students'));
    }

    public function create()
    {
        return view('super-admin.students.create');
    }

    // Student Create - User + Student 2 ta table e save
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'organisation_id' => 'required|exists:organisations,id',
            'class' => 'required|string',
            'roll_no' => 'required|string',
            'phone' => 'nullable|string',
        ]);

        // 1. Age User create
        $user = $this->userRepo->createUser([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'organisation_id' => $validated['organisation_id'],
            'role_id' => 4, // 4 = Student - tomar role_id onujayi change koro
        ]);

        // 2. Tarpor Student create
        $this->studentRepo->create([
            'user_id' => $user->id,
            'organisation_id' => $validated['organisation_id'],
            'class' => $validated['class'],
            'roll_no' => $validated['roll_no'],
            'phone' => $validated['phone'] ?? null,
        ]);

        return redirect()->route('super-admin.students.index')->with('success', 'Student created successfully');
    }

    // Ekta Student View - Onno Institute hole BLOCKED
    public function show($id)
    {
        if (auth()->user()->isSuperAdmin()) {
            $student = $this->studentRepo->find($id);
        } else {
            $student = $this->studentRepo->findByOrg($id, auth()->user()->organisation_id);
        }
        return view('super-admin.students.show', compact('student'));
    }

    public function destroy($id)
    {
        $student = $this->studentRepo->find($id);
        $this->userRepo->delete($student->user_id);
        $this->studentRepo->delete($id);

        return back()->with('success', 'Student deleted');
    }
}