<?php

namespace App\Repositories;

use App\Interfaces\RenewalRepositoryInterface;
use App\Models\Renewal;

class RenewalRepository implements RenewalRepositoryInterface
{
    public function all() { return Renewal::all(); }
    public function find($id) { return Renewal::findOrFail($id); }
    public function create(array $data) { return Renewal::create($data); }
    public function update($id, array $data) { $record = $this->find($id); $record->update($data); return $record; }
    public function delete($id) { return $this->find($id)->delete(); }
}
