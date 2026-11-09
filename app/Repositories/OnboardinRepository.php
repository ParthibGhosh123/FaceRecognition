<?php
namespace App\Repositories;

use App\Models\Organisation;
use App\Models\User;
use App\Repositories\Interfaces\OnboardingRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class OnboardingRepository implements OnboardingRepositoryInterface
{
    public function createOrganisationWithAdmin(array $data)
    {
        return DB::transaction(function () use ($data) {
            // 1. Organisation Create
            $organisation = Organisation::create([
                'name' => $data['organisation_name'],
                'email' => $data['organisation_email'],
                'status' => 'active',
            ]);

            // 2. Admin Create - Auto organisation_id link
            $admin = User::create([
                'name' => $data['admin_name'],
                'email' => $data['admin_email'],
                'password' => Hash::make($data['admin_password']),
                'organisation_id' => $organisation->id,
                'role_id' => 1, // 1 = organisation_admin
            ]);

            return ['organisation' => $organisation, 'admin' => $admin];
        });
    }

    public function getAllOrganisations()
    {
        return Organisation::withCount('users')->latest()->get();
    }
}