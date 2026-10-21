<?php
namespace App\Policies;
use App\Models\User;
class CrmLeadPolicy { public function delete(User $user) { return $user->isSuperAdmin() || $user->canDo('crm.delete') || $user->role_id == 1; } }