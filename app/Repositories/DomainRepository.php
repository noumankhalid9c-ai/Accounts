<?php

namespace App\Repositories;

use App\Interfaces\DomainRepositoryInterface;
use App\Models\Domain;

class DomainRepository implements DomainRepositoryInterface
{
    public function all() { return Domain::all(); }
    public function find($id) { return Domain::findOrFail($id); }
    public function create(array $data) { return Domain::create($data); }
    public function update($id, array $data) { $record = $this->find($id); $record->update($data); return $record; }
    public function delete($id) { return $this->find($id)->delete(); }
}
