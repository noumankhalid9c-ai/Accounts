<?php
namespace App\Policies;
use App\Models\User;
class CustomerPolicy { public function delete(User $user) { return $user->role_id == 1; } }