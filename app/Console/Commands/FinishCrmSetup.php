<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class FinishCrmSetup extends Command
{
    protected $signature = 'make:crm-finish';

    public function handle()
    {
        $base = base_path();
        
        $policy = "<?php\nnamespace App\Policies;\nuse App\Models\User;\nclass CrmLeadPolicy { public function delete(User \$user) { return \$user->role_id == 1; } }";
        File::put($base . '/app/Policies/CrmLeadPolicy.php', $policy);
        
        $policy2 = "<?php\nnamespace App\Policies;\nuse App\Models\User;\nclass CustomerPolicy { public function delete(User \$user) { return \$user->role_id == 1; } }";
        File::put($base . '/app/Policies/CustomerPolicy.php', $policy2);

        $this->info("Setup complete.");
    }
}
