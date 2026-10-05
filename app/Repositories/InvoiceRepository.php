<?php

namespace App\Repositories;

use App\Interfaces\InvoiceRepositoryInterface;
use App\Models\Invoice;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    public function all() { return Invoice::all(); }
    public function find($id) { return Invoice::findOrFail($id); }
    public function create(array $data) { return Invoice::create($data); }
    public function update($id, array $data) { $record = $this->find($id); $record->update($data); return $record; }
    public function delete($id) { return $this->find($id)->delete(); }
}
