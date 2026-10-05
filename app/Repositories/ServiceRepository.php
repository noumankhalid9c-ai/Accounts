<?php

namespace App\Repositories;

use App\Interfaces\ServiceRepositoryInterface;
use App\Models\Service;

class ServiceRepository implements ServiceRepositoryInterface
{
    public function all() { return Service::all(); }
    public function find($id) { return Service::findOrFail($id); }
    public function create(array $data) { return Service::create($data); }
    public function update($id, array $data) { $record = $this->find($id); $record->update($data); return $record; }
    public function delete($id) { return $this->find($id)->delete(); }
}
