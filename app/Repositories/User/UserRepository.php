<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UserRepository extends BaseRepository implements BaseRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    // Super Admin - Sob User List
    public function getAllUsers()
    {
        return User::with('organisation')->latest()->get();
    }

    // Role wise User - Teacher / Student / Supervisor
    public function getByRole($roleId)
    {
        return User::where('role_id', $roleId)->latest()->get();
    }

    // Sudhu Nijer Organisation er User
    public function getByOrganisation($orgId)
    {
        return User::where('organisation_id', $orgId)->latest()->get();
    }

    // User Create with Password Hash
    public function createUser(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        return $this->create($data);
    }
}