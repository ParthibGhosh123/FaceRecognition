<?php

namespace App\Repositories\Student;

use App\Models\Student;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\BaseRepositoryInterface;

class StudentRepository extends BaseRepository implements BaseRepositoryInterface
{
    public function __construct(Student $model)
    {
        parent::__construct($model);
    }

    // Sudhu Nijer Institute er Student - BLOCKED Rule
    public function getByOrganisation($orgId)
    {
        return Student::where('organisation_id', $orgId)
            ->latest()
            ->get();
    }

    // Student + Organisation + User details
    public function getWithUser($orgId)
    {
        return Student::where('organisation_id', $orgId)
            ->with('user', 'organisation')
            ->latest()
            ->get();
    }

    // Ekta Student View - Onno Institute hole BLOCKED
    public function findByOrg($id, $orgId)
    {
        $student = $this->find($id);

        if ($student->organisation_id !== $orgId) {
            abort(403, 'One organization cannot view another institution - BLOCKED');
        }
        return $student;
    }
}