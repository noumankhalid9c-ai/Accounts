<?php

namespace App\Repositories;

use App\Interfaces\PaymentRepositoryInterface;
use App\Models\Payment;

class PaymentRepository implements PaymentRepositoryInterface
{
    public function all() { return Payment::all(); }
    public function find($id) { return Payment::findOrFail($id); }
    public function create(array $data) { return Payment::create($data); }
    public function update($id, array $data) { $record = $this->find($id); $record->update($data); return $record; }
    public function delete($id) { return $this->find($id)->delete(); }
}
