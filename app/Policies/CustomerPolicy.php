<?php
namespace App\Policies;
use App\Models\User;
class CustomerPolicy { public function delete(User $user) { return $user->isSuperAdmin() || $user->canDo('clients.delete') || $user->role_id == 1; } }