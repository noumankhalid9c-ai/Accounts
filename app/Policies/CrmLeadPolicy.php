<?php
namespace App\Policies;
use App\Models\User;
class CrmLeadPolicy { public function delete(User $user) { return $user->role_id == 1; } }