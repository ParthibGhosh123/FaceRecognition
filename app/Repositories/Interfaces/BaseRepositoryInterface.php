<?php

namespace App\Repositories\Interfaces;

interface BaseRepositoryInterface
{
    /**
     * Get all records
     */
    public function all();

    /**
     * Find record by id
     */
    public function find($id);

    /**
     * Create new record
     */
    public function create(array $data);

    /**
     * Update record by id
     */
    public function update($id, array $data);

    /**
     * Delete record by id
     */
    public function delete($id);
}