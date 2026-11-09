<?php
namespace App\Repositories\Interfaces;

use App\Models\Organisation;
use App\Models\User;

interface OrganisationRepositoryInterface
{
    public function viewAny(User $actor);
    public function view(User $actor, Organisation $organisation): Organisation;
    public function update(User $actor, Organisation $organisation, array $data): Organisation;
    public function delete(User $actor, Organisation $organisation): bool;
    
    // On-boarding
    public function onboard(array $orgData, array $adminData): Organisation;
}