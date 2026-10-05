<?php

namespace App\Repositories;

use App\Interfaces\HostingRepositoryInterface;
use App\Models\Hosting;

class HostingRepository implements HostingRepositoryInterface
{
    public function all() { return Hosting::all(); }
    public function find($id) { return Hosting::findOrFail($id); }
    public function create(array $data) { return Hosting::create($data); }
    public function update($id, array $data) { $record = $this->find($id); $record->update($data); return $record; }
    public function delete($id) { return $this->find($id)->delete(); }
}
