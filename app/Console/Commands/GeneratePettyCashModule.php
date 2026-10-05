<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GeneratePettyCashModule extends Command
{
    protected $signature = 'make:petty-cash-module';
    protected $description = 'Generate the Petty Cash and Daily Expense Module files';

    public function handle()
    {
        $base = base_path();

        // 1. Models
        $models = [
            'PettyCashDay' => "<?php\nnamespace App\Models;\nuse Illuminate\Database\Eloquent\Factories\HasFactory;\nuse Illuminate\Database\Eloquent\Model;\nclass PettyCashDay extends Model { use HasFactory; protected \$guarded = []; public function transactions() { return \$this->hasMany(PettyCashTransaction::class, 'petty_cash_day_id'); } }",
            
            'PettyCashTransaction' => "<?php\nnamespace App\Models;\nuse Illuminate\Database\Eloquent\Factories\HasFactory;\nuse Illuminate\Database\Eloquent\Model;\nclass PettyCashTransaction extends Model { use HasFactory; protected \$guarded = []; public function day() { return \$this->belongsTo(PettyCashDay::class, 'petty_cash_day_id'); } public function category() { return \$this->belongsTo(ExpenseCategory::class); } public function creator() { return \$this->belongsTo(User::class, 'created_by'); } }",
            
            'ExpenseCategory' => "<?php\nnamespace App\Models;\nuse Illuminate\Database\Eloquent\Factories\HasFactory;\nuse Illuminate\Database\Eloquent\Model;\nclass ExpenseCategory extends Model { use HasFactory; protected \$guarded = []; }"
        ];

        foreach ($models as $name => $content) {
            File::put($base . '/app/Models/' . $name . '.php', $content);
        }
        $this->info("Models created.");

        // 2. Migration
        $migrationContent = <<<'PHP'
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('status')->default('Active');
            $table->timestamps();
        });

        Schema::create('petty_cash_days', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->decimal('opening_cash', 15, 2)->default(0);
            $table->decimal('cash_received', 15, 2)->default(0);
            $table->decimal('cash_expenses', 15, 2)->default(0);
            $table->decimal('closing_cash', 15, 2)->default(0);
            $table->decimal('actual_cash', 15, 2)->nullable();
            $table->decimal('cash_difference', 15, 2)->nullable();
            $table->string('status')->default('Open');
            $table->unsignedBigInteger('closed_by')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('petty_cash_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('petty_cash_day_id')->constrained('petty_cash_days')->cascadeOnDelete();
            $table->string('transaction_type'); // Cash Received, Expense, Adjustment
            $table->date('date');
            $table->foreignId('category_id')->nullable()->constrained('expense_categories')->nullOnDelete();
            $table->text('description');
            $table->string('paid_to')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('payment_method')->default('Cash'); // Cash, Bank, Card, Online
            $table->string('reference_number')->nullable();
            $table->string('attachment')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
        
        // Insert default categories
        DB::table('expense_categories')->insert([
            ['name' => 'Office'], ['name' => 'Transport'], ['name' => 'Tea / Refreshment'],
            ['name' => 'Printing'], ['name' => 'Stationery'], ['name' => 'Marketing'],
            ['name' => 'Other']
        ]);
    }
    public function down(): void {
        Schema::dropIfExists('petty_cash_transactions');
        Schema::dropIfExists('petty_cash_days');
        Schema::dropIfExists('expense_categories');
    }
};
PHP;
        $migrationFile = $base . '/database/migrations/' . date('Y_m_d_His') . '_create_petty_cash_tables.php';
        File::put($migrationFile, $migrationContent);
        $this->info("Migration created.");

        // 3. Controller
        $controllerContent = <<<'PHP'
<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\PettyCashDay;
use App\Models\PettyCashTransaction;
use App\Models\ExpenseCategory;
use Carbon\Carbon;

class PettyCashController extends Controller
{
    public function dashboard()
    {
        $today = Carbon::today();
        $day = $this->getOrCreateDay($today);
        
        $monthExpenses = PettyCashTransaction::whereMonth('date', $today->month)
                                             ->where('transaction_type', 'Expense')
                                             ->sum('amount');
                                             
        return view('petty_cash.dashboard', compact('day', 'monthExpenses'));
    }

    public function dailyCash(Request $request)
    {
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));
        $day = $this->getOrCreateDay(Carbon::parse($date));
        $transactions = $day->transactions()->with('category', 'creator')->latest()->get();
        $categories = ExpenseCategory::where('status', 'Active')->get();
        
        return view('petty_cash.daily', compact('day', 'transactions', 'categories', 'date'));
    }

    private function getOrCreateDay($date)
    {
        $day = PettyCashDay::whereDate('date', $date)->first();
        if (!$day) {
            $previousDay = PettyCashDay::whereDate('date', '<', $date)
                                       ->orderBy('date', 'desc')
                                       ->first();
                                       
            $openingCash = $previousDay ? $previousDay->closing_cash : 0;
            
            $day = PettyCashDay::create([
                'date' => $date->format('Y-m-d'),
                'opening_cash' => $openingCash,
                'closing_cash' => $openingCash,
                'status' => 'Open'
            ]);
        }
        return $day;
    }

    public function storeTransaction(Request $request, PettyCashDay $day)
    {
        if ($day->status === 'Closed') {
            return back()->with('error', 'Cannot add transactions to a closed day.');
        }

        $data = $request->validate([
            'transaction_type' => 'required', // Cash Received, Expense
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required',
            'payment_method' => 'required', // Cash, Bank, etc.
            'category_id' => 'nullable|exists:expense_categories,id',
            'paid_to' => 'nullable',
        ]);
        
        $data['date'] = $day->date;
        $data['created_by'] = auth()->id();
        
        $day->transactions()->create($data);
        $this->recalculateDay($day);

        return back()->with('success', 'Transaction added successfully.');
    }

    private function recalculateDay(PettyCashDay $day)
    {
        $cashReceived = $day->transactions()
            ->where('transaction_type', 'Cash Received')
            ->sum('amount');
            
        $cashExpenses = $day->transactions()
            ->where('transaction_type', 'Expense')
            ->where('payment_method', 'Cash')
            ->sum('amount');
            
        $closingCash = $day->opening_cash + $cashReceived - $cashExpenses;
        
        $day->update([
            'cash_received' => $cashReceived,
            'cash_expenses' => $cashExpenses,
            'closing_cash' => $closingCash
        ]);
    }

    public function closeDay(Request $request, PettyCashDay $day)
    {
        if (auth()->user()->role_id != 1 && auth()->user()->role_id != 2) {
            abort(403, 'Unauthorized to close day.');
        }
        
        $actualCash = $request->input('actual_cash');
        
        $day->update([
            'status' => 'Closed',
            'closed_by' => auth()->id(),
            'closed_at' => now(),
            'actual_cash' => $actualCash,
            'cash_difference' => $actualCash !== null ? ($actualCash - $day->closing_cash) : 0,
        ]);

        return back()->with('success', 'Day closed successfully.');
    }
}
PHP;
        File::ensureDirectoryExists($base . '/app/Http/Controllers');
        File::put($base . '/app/Http/Controllers/PettyCashController.php', $controllerContent);
        $this->info("Controller created.");

        // 4. Routes
        $routes = <<<'PHP'

// Petty Cash Routes
Route::middleware(['auth'])->prefix('petty-cash')->name('petty_cash.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\PettyCashController::class, 'dashboard'])->name('dashboard');
    Route::get('/daily', [\App\Http\Controllers\PettyCashController::class, 'dailyCash'])->name('daily');
    Route::post('/days/{day}/transaction', [\App\Http\Controllers\PettyCashController::class, 'storeTransaction'])->name('transactions.store');
    Route::post('/days/{day}/close', [\App\Http\Controllers\PettyCashController::class, 'closeDay'])->name('days.close');
});
PHP;
        File::append($base . '/routes/web.php', $routes);
        $this->info("Routes added.");

        // 5. Views
        File::ensureDirectoryExists($base . '/resources/views/petty_cash');
        
        $dashboardView = <<<'HTML'
@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <h1>Petty Cash Dashboard</h1>
    <div class="row">
        <div class="col-md-3">
            <div class="card bg-info text-white mb-3"><div class="card-body">
                <h5>Opening Cash</h5><h3>PKR {{ number_format($day->opening_cash, 2) }}</h3>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white mb-3"><div class="card-body">
                <h5>Cash Received (Today)</h5><h3>PKR {{ number_format($day->cash_received, 2) }}</h3>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white mb-3"><div class="card-body">
                <h5>Cash Expenses (Today)</h5><h3>PKR {{ number_format($day->cash_expenses, 2) }}</h3>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white mb-3"><div class="card-body">
                <h5>Closing Cash</h5><h3>PKR {{ number_format($day->closing_cash, 2) }}</h3>
            </div></div>
        </div>
    </div>
</div>
@endsection
HTML;
        File::put($base . '/resources/views/petty_cash/dashboard.blade.php', $dashboardView);

        $dailyView = <<<'HTML'
@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Daily Cash: {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</h1>
        <form method="GET" class="d-flex">
            <input type="date" name="date" value="{{ $date }}" class="form-control me-2" onchange="this.form.submit()">
        </form>
    </div>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

    <div class="row mb-4">
        <div class="col"><strong>Opening:</strong> PKR {{ number_format($day->opening_cash, 2) }}</div>
        <div class="col"><strong>Received:</strong> <span class="text-success">+ PKR {{ number_format($day->cash_received, 2) }}</span></div>
        <div class="col"><strong>Expenses (Cash):</strong> <span class="text-danger">- PKR {{ number_format($day->cash_expenses, 2) }}</span></div>
        <div class="col"><strong>Closing:</strong> PKR {{ number_format($day->closing_cash, 2) }}</div>
        <div class="col">
            <span class="badge bg-{{ $day->status == 'Open' ? 'success' : 'secondary' }}">{{ $day->status }}</span>
        </div>
    </div>

    @if($day->status == 'Open')
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('petty_cash.transactions.store', $day->id) }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-2">
                        <select name="transaction_type" class="form-select" required>
                            <option value="Expense">Expense</option>
                            <option value="Cash Received">Cash Received</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="category_id" class="form-select">
                            <option value="">(No Category)</option>
                            @foreach($categories as $cat) <option value="{{ $cat->id }}">{{ $cat->name }}</option> @endforeach
                        </select>
                    </div>
                    <div class="col-md-2"><input type="text" name="paid_to" class="form-control" placeholder="Paid To / Received From"></div>
                    <div class="col-md-3"><input type="text" name="description" class="form-control" placeholder="Description" required></div>
                    <div class="col-md-1"><input type="number" step="0.01" name="amount" class="form-control" placeholder="Amount" required></div>
                    <div class="col-md-1">
                        <select name="payment_method" class="form-select" required>
                            <option value="Cash">Cash</option>
                            <option value="Bank">Bank</option>
                            <option value="Card">Card</option>
                        </select>
                    </div>
                    <div class="col-md-1"><button type="submit" class="btn btn-primary w-100">Add</button></div>
                </div>
            </form>
        </div>
    </div>
    @endif

    <div class="card mb-4">
        <div class="card-header bg-dark text-white">Transactions</div>
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead><tr><th>Type</th><th>Category</th><th>Description</th><th>Entity</th><th>Amount</th><th>Method</th><th>Added By</th></tr></thead>
                <tbody>
                    @foreach($transactions as $t)
                    <tr>
                        <td><span class="badge bg-{{ $t->transaction_type == 'Expense' ? 'danger' : 'success' }}">{{ $t->transaction_type }}</span></td>
                        <td>{{ $t->category->name ?? '-' }}</td>
                        <td>{{ $t->description }}</td>
                        <td>{{ $t->paid_to }}</td>
                        <td class="fw-bold">PKR {{ number_format($t->amount, 2) }}</td>
                        <td>{{ $t->payment_method }}</td>
                        <td>{{ $t->creator->name ?? 'Unknown' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    @if($day->status == 'Open')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('petty_cash.days.close', $day->id) }}" method="POST" class="d-flex align-items-center">
                @csrf
                <label class="me-3 fw-bold">Actual Physical Cash (For Reconciliation):</label>
                <input type="number" step="0.01" name="actual_cash" class="form-control w-25 me-3" placeholder="Enter physical cash counted" required>
                <button class="btn btn-danger" onclick="return confirm('Are you sure you want to close this day? No more edits will be allowed.')">Close Day</button>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection
HTML;
        File::put($base . '/resources/views/petty_cash/daily.blade.php', $dailyView);
        $this->info("Views created.");
    }
}
