<?php

namespace App\Repositories;

use App\Interfaces\ReceiptRepositoryInterface;
use App\Models\Receipt;

class ReceiptRepository implements ReceiptRepositoryInterface
{
    public function all() { return Receipt::all(); }
    public function find($id) { return Receipt::findOrFail($id); }
    public function create(array $data) { return Receipt::create($data); }
    public function update($id, array $data) { $record = $this->find($id); $record->update($data); return $record; }
    public function delete($id) { return $this->find($id)->delete(); }
}
