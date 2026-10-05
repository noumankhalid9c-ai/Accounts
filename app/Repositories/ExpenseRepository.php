<?php

namespace App\Repositories;

use App\Interfaces\ExpenseRepositoryInterface;
use App\Models\Expense;

class ExpenseRepository implements ExpenseRepositoryInterface
{
    public function all() { return Expense::all(); }
    public function find($id) { return Expense::findOrFail($id); }
    public function create(array $data) { return Expense::create($data); }
    public function update($id, array $data) { $record = $this->find($id); $record->update($data); return $record; }
    public function delete($id) { return $this->find($id)->delete(); }
}
