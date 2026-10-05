<?php
namespace App\Policies;
use App\Models\User;
use App\Models\Customer;
class CrmPolicy { public function delete(User $user) { return $user->is_admin; } }