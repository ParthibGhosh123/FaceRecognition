<?php

namespace App\Repositories\Teacher;

use App\Models\Teacher;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\BaseRepositoryInterface;

class TeacherRepository extends BaseRepository implements BaseRepositoryInterface
{
    public function __construct(Teacher $model)
    {
        parent::__construct($model);
    }

    // Sudhu Nijer Institute er Teacher
    public function getByOrganisation($orgId)
    {
        return Teacher::where('organisation_id', $orgId)
            ->latest()
            ->get();
    }

    // Teacher + User + Organisation
    public function getWithUser($orgId)
    {
        return Teacher::where('organisation_id', $orgId)
            ->with('user', 'organisation')
            ->latest()
            ->get();
    }

    // Ekta Teacher View - Onno Institute hole BLOCKED
    public function findByOrg($id, $orgId)
    {
        $teacher = $this->find($id);

        if ($teacher->organisation_id !== $orgId) {
            abort(403, 'One organization cannot view another institution - BLOCKED');
        }
        return $teacher;
    }
}