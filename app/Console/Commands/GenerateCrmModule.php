<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateCrmModule extends Command
{
    protected $signature = 'make:crm-module';
    protected $description = 'Generate the CRM Module files';

    public function handle()
    {
        $base = base_path();

        $models = [
            'Customer' => "<?php\nnamespace App\Models;\nuse Illuminate\Database\Eloquent\Factories\HasFactory;\nuse Illuminate\Database\Eloquent\Model;\nuse Illuminate\Database\Eloquent\SoftDeletes;\nclass Customer extends Model { use HasFactory, SoftDeletes; protected \$guarded = []; public function leads() { return \$this->hasMany(CrmLead::class); } public function followups() { return \$this->hasMany(CrmFollowup::class); } public function activities() { return \$this->hasMany(CrmActivity::class); } public function assignedTo() { return \$this->belongsTo(User::class, 'assigned_to'); } public function createdBy() { return \$this->belongsTo(User::class, 'created_by'); } }",
            'CrmLead' => "<?php\nnamespace App\Models;\nuse Illuminate\Database\Eloquent\Factories\HasFactory;\nuse Illuminate\Database\Eloquent\Model;\nuse Illuminate\Database\Eloquent\SoftDeletes;\nclass CrmLead extends Model { use HasFactory, SoftDeletes; protected \$guarded = []; public function customer() { return \$this->belongsTo(Customer::class); } public function followups() { return \$this->hasMany(CrmFollowup::class, 'lead_id'); } public function activities() { return \$this->hasMany(CrmActivity::class, 'lead_id'); } public function assignedTo() { return \$this->belongsTo(User::class, 'assigned_to'); } public function campaign() { return \$this->belongsTo(MarketingCampaign::class, 'campaign_id'); } }",
            'CrmFollowup' => "<?php\nnamespace App\Models;\nuse Illuminate\Database\Eloquent\Factories\HasFactory;\nuse Illuminate\Database\Eloquent\Model;\nuse Illuminate\Database\Eloquent\SoftDeletes;\nclass CrmFollowup extends Model { use HasFactory, SoftDeletes; protected \$guarded = []; public function lead() { return \$this->belongsTo(CrmLead::class, 'lead_id'); } public function customer() { return \$this->belongsTo(Customer::class, 'customer_id'); } public function assignedTo() { return \$this->belongsTo(User::class, 'assigned_to'); } }",
            'CrmActivity' => "<?php\nnamespace App\Models;\nuse Illuminate\Database\Eloquent\Factories\HasFactory;\nuse Illuminate\Database\Eloquent\Model;\nclass CrmActivity extends Model { use HasFactory; protected \$guarded = []; public function customer() { return \$this->belongsTo(Customer::class); } public function lead() { return \$this->belongsTo(CrmLead::class, 'lead_id'); } public function user() { return \$this->belongsTo(User::class, 'user_id'); } }",
            'MarketingCampaign' => "<?php\nnamespace App\Models;\nuse Illuminate\Database\Eloquent\Factories\HasFactory;\nuse Illuminate\Database\Eloquent\Model;\nclass MarketingCampaign extends Model { use HasFactory; protected \$guarded = []; public function leads() { return \$this->hasMany(CrmLead::class, 'campaign_id'); } }"
        ];

        foreach ($models as $name => $content) {
            File::put($base . '/app/Models/' . $name . '.php', $content);
        }
        $this->info("Models created.");
        
        $policy = "<?php\nnamespace App\Policies;\nuse App\Models\User;\nuse App\Models\Customer;\nclass CrmPolicy { public function delete(User \$user) { return \$user->is_admin; } }";
        File::ensureDirectoryExists($base . '/app/Policies');
        File::put($base . '/app/Policies/CrmPolicy.php', $policy);
        $this->info("Policy created.");
        
        // Let's create a single generic CRM Controller for now that covers dashboard, leads, and follow-ups.
        $controller = <<<'PHP'
<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\CrmLead;
use App\Models\CrmFollowup;
use App\Models\CrmActivity;

class CrmController extends Controller
{
    public function dashboard()
    {
        $totalCustomers = Customer::count();
        $newInquiries = CrmLead::where('stage', 'New Inquiry')->count();
        $followupsToday = CrmFollowup::whereDate('followup_date', today())->count();
        
        return view('crm.dashboard', compact('totalCustomers', 'newInquiries', 'followupsToday'));
    }

    public function leads()
    {
        $leads = CrmLead::with(['customer', 'assignedTo'])->latest()->get();
        return view('crm.leads.index', compact('leads'));
    }

    public function storeLead(Request $request)
    {
        $data = $request->validate([
            'customer_name' => 'required',
            'phone' => 'required',
            'inquiry_type' => 'required',
            'destination' => 'required',
            'travel_date' => 'required|date',
            'pax' => 'required|integer',
            'source' => 'required',
            'notes' => 'nullable',
        ]);
        
        $data['created_by'] = auth()->id();
        $data['assigned_to'] = auth()->id();
        $data['stage'] = 'New Inquiry';

        $lead = CrmLead::create($data);
        
        CrmActivity::create([
            'lead_id' => $lead->id,
            'user_id' => auth()->id(),
            'activity_type' => 'Created',
            'description' => 'New inquiry created',
        ]);

        return redirect()->route('crm.leads')->with('success', 'Lead created successfully');
    }
    
    public function destroyLead(CrmLead $lead)
    {
        $this->authorize('delete', $lead); // Calls policy
        $lead->delete();
        return back()->with('success', 'Lead deleted');
    }
}
PHP;
        File::put($base . '/app/Http/Controllers/CrmController.php', $controller);
        $this->info("Controller created.");
        
        // Web Routes
        $routes = <<<'PHP'

// CRM Routes
Route::middleware(['auth'])->prefix('crm')->name('crm.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\CrmController::class, 'dashboard'])->name('dashboard');
    Route::get('/leads', [\App\Http\Controllers\CrmController::class, 'leads'])->name('leads');
    Route::post('/leads', [\App\Http\Controllers\CrmController::class, 'storeLead'])->name('leads.store');
    Route::delete('/leads/{lead}', [\App\Http\Controllers\CrmController::class, 'destroyLead'])->name('leads.destroy');
});
PHP;
        File::append($base . '/routes/web.php', $routes);
        $this->info("Routes appended.");

        // Views
        File::ensureDirectoryExists($base . '/resources/views/crm/leads');
        
        $dashboardView = <<<'HTML'
@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <h1>CRM Dashboard</h1>
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h3>Total Customers</h3>
                    <p>{{ $totalCustomers }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h3>New Inquiries</h3>
                    <p>{{ $newInquiries }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h3>Follow-ups Today</h3>
                    <p>{{ $followupsToday }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
HTML;
        File::put($base . '/resources/views/crm/dashboard.blade.php', $dashboardView);
        
        $leadsView = <<<'HTML'
@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <h1>Inquiries / Leads</h1>
    <!-- Quick Add Form -->
    <div class="card mb-4">
        <div class="card-body">
            <h4>+ New Inquiry</h4>
            <form action="{{ route('crm.leads.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-3"><input type="text" name="customer_name" class="form-control" placeholder="Customer Name" required></div>
                    <div class="col-md-2"><input type="text" name="phone" class="form-control" placeholder="Phone / WhatsApp" required></div>
                    <div class="col-md-2">
                        <select name="inquiry_type" class="form-control" required>
                            <option value="">Type</option>
                            <option value="Umrah">Umrah</option>
                            <option value="Hajj">Hajj</option>
                            <option value="Tour">Tour</option>
                        </select>
                    </div>
                    <div class="col-md-2"><input type="text" name="destination" class="form-control" placeholder="Destination" required></div>
                    <div class="col-md-2"><input type="date" name="travel_date" class="form-control" required></div>
                    <div class="col-md-1"><input type="number" name="pax" class="form-control" placeholder="Pax" required></div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3">
                        <select name="source" class="form-control" required>
                            <option value="">Source</option>
                            <option value="WhatsApp">WhatsApp</option>
                            <option value="Facebook">Facebook</option>
                        </select>
                    </div>
                    <div class="col-md-7"><input type="text" name="notes" class="form-control" placeholder="Notes"></div>
                    <div class="col-md-2"><button type="submit" class="btn btn-primary w-100">Save</button></div>
                </div>
            </form>
        </div>
    </div>

    <!-- Leads Table -->
    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Type</th>
                        <th>Destination</th>
                        <th>Stage</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leads as $lead)
                    <tr>
                        <td>{{ $lead->customer_name }}</td>
                        <td>{{ $lead->phone }}</td>
                        <td>{{ $lead->inquiry_type }}</td>
                        <td>{{ $lead->destination }}</td>
                        <td>{{ $lead->stage }}</td>
                        <td>
                            <!-- No delete button for sales -->
                            @can('delete', $lead)
                            <form action="{{ route('crm.leads.destroy', $lead->id) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
HTML;
        File::put($base . '/resources/views/crm/leads/index.blade.php', $leadsView);
        $this->info("Views created.");
    }
}
