<?php
namespace App\Repositories;

use App\Models\Organisation;
use App\Models\User;
use App\Repositories\Interfaces\OrganisationRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;

class OrganisationRepository implements OrganisationRepositoryInterface
{
    /**
     * Super Admin sob organisation dekhte parbe
     */
    public function viewAny(User $actor)
    {
        if (!$actor->hasRole('super_admin')) {
            throw new AuthorizationException('This action is unauthorized. Only super_admin can view all organizations.');
        }

        return Organisation::latest()->get();
    }

    /**
     * Super Admin OR nijer organisation hole dekhte parbe
     */
    public function view(User $actor, Organisation $organisation): Organisation
    {
        $canView = $actor->hasRole('super_admin') 
                || $actor->organisation_id === $organisation->id;

        if (!$canView) {
            throw new AuthorizationException('One organization cannot view another institution - BLOCKED');
        }

        return $organisation;
    }

    /**
     * Sudhu Super Admin can update 
     */
    public function update(User $actor, Organisation $organisation, array $data): Organisation
    {
        if (!$actor->hasRole('super_admin')) {
            throw new AuthorizationException('This action is unauthorized. One organization cannot update another institution - BLOCKED');
        }

        $organisation->update($data);
        return $organisation;
    }

    /**
     * Sudhu Super Admin can delete
     */
    public function delete(User $actor, Organisation $organisation): bool
    {
        if (!$actor->hasRole('super_admin')) {
            throw new AuthorizationException('This action is unauthorized. Only super_admin can delete organization.');
        }

        return $organisation->delete();
    }
}