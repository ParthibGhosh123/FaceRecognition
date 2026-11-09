<?php
namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\OrganisationRepositoryInterface;

class OnboardingController extends Controller
{
    protected $orgRepo;

    public function __construct(OrganisationRepositoryInterface $orgRepo)
    {
        $this->orgRepo = $orgRepo;
    }

    // List
    public function index()
    {
        $organisations = $this->orgRepo->all();
        return view('super-admin.onboarding.index', compact('organisations'));
    }

    // Form
    public function create()
    {
        return view('super-admin.onboarding.create');
    }

    // Store - Main Onboarding Logic
    public function store(Request $request)
    {
        $request->validate([
            'org_name' => 'required|string|max:255',
            'org_email' => 'required|email',
            'org_address' => 'nullable|string',
            'admin_name' => 'required|string',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => 'required|min:6'
        ]);

        $orgData = [
            'name' => $request->org_name,
            'email' => $request->org_email,
            'address' => $request->org_address,
            'status' => 'active'
        ];

        $adminData = [
            'name' => $request->admin_name,
            'email' => $request->admin_email,
            'password' => $request->admin_password
        ];

        $this->orgRepo->createWithAdmin($orgData, $adminData);

        return redirect()->route('super.onboarding.index')->with('success', 'Organisation onboarded successfully!');
    }
}