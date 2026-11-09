<?php
namespace App\Repositories\Interfaces;

interface OnboardingRepositoryInterface
{
    public function createOrganisationWithAdmin(array $data);
    public function getAllOrganisations();
}