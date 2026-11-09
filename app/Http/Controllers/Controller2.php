<?php

namespace App\Http\Controllers;

use App\Models\Organisation;
use App\Models\Roles;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class Controller2 extends Controller
{
    public function index()
    {
        $orgA = Organisation::firstOrCreate(['name' => 'College A']);
        $orgB = Organisation::firstOrCreate(['name' => 'College B']);

        $role = Roles::firstOrCreate(['slug' => 'organisation_admin'], ['name' => 'Org Admin']);

        $userA = User::firstOrCreate(
            ['email' => 'adminA@test.com'],
            [
                'organisation_id' => $orgA->id,
                'role_id' => $role->id,
                'first_name' => 'Admin A',
                'password' => Hash::make('password'),
                'status' => 'active'
            ]
        );

        Auth::login($userA);

        echo "Testing Policy...<br>";
        echo "Logged in as: College A Admin (ID: {$orgA->id})<br>";
        echo "Trying to edit: College B (ID: {$orgB->id})<br><br>";

        try {
            Gate::authorize('update', $orgB);
            return response("<h2 style='color:red'>Policy FAIL</h2>", 200);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response("
                <div style='font-family: monospace; padding: 20px; background: #ffe6e6; border: 2px solid red;'>
                
                    <h3>403 | This action is unauthorized.</h3>
                    <p><b>One organization cannot update another institution - BLOCKED</b></p>
                    <p>Logged User Org: {$orgA->id} (College A)</p>
                    <p>Target Org: {$orgB->id} (College B)</p>
                </div>
            ", 403);
        }
    }
}