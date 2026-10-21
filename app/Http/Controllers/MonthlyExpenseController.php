<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MonthlyExpense;
use App\Models\ExpenseCategory;
use App\Models\RecurringExpense;
use App\Models\MonthlyExpensePayment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class MonthlyExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:monthly_expenses.view')->only(['dashboard', 'index', 'utilityBills', 'recurring', 'reports', 'comparisonReport', 'yearlyReport', 'export', 'printReport', 'categories', 'show', 'viewAttachment']);
        $this->middleware('permission:monthly_expenses.create')->only(['create', 'store', 'storeRecurring', 'generateRecurring', 'storeCategory']);
        $this->middleware('permission:monthly_expenses.edit')->only(['edit', 'update', 'updateRecurring', 'updateCategory']);
        $this->middleware('permission:monthly_expenses.delete')->only(['destroy', 'destroyRecurring', 'destroyCategory', 'deletePayment']);
        $this->middleware('permission:monthly_expenses.payments')->only(['addPayment']);
    }

    /**
     * Check if current user has permission to manage monthly expenses
     */
    protected function authorizeFinance()
    {
        $user = auth()->user();
        if (!$user) {
            abort(401);
        }
        
        // RBAC Permission Check
        if ($user->isSuperAdmin() || $user->canDo('monthly_expenses.view') || in_array($user->role_id, [1, 2, 3, 5])) {
            return;
        }

        abort(403, 'Unauthorized. Access restricted to Finance, Administration, and authorized roles.');
    }

    protected function authorizeAdminOnly()
    {
        $user = auth()->user();
        if (!$user || (!$user->isSuperAdmin() && !$user->canDo('monthly_expenses.delete') && ($user->role_id ?? 0) != 1)) {
            abort(403, 'Unauthorized. This action requires Administrator privileges.');
        }
    }

    /**
     * 1. Monthly Expense Dashboard
     */
    public function dashboard(Request $request)
    {
        $this->authorizeFinance();

        $month = (int)$request->input('month', Carbon::now()->month);
        $year = (int)$request->input('year', Carbon::now()->year);

        // All non-cancelled expenses for the period
        $expenses = MonthlyExpense::with(['category', 'payments'])
            ->where('expense_month', $month)
            ->where('expense_year', $year)
            ->get();

        $activeExpenses = $expenses->where('payment_status', '!=', 'Cancelled');

        $totalExpenses = (float)$activeExpenses->sum('amount');
        $totalUtilityBills = (float)$activeExpenses->where('is_utility_bill', true)->sum('amount');
        $totalPaid = (float)$activeExpenses->sum('paid_amount');
        
        $totalPending = 0.0;
        $totalOverdue = 0.0;

        foreach ($activeExpenses as $exp) {
            $rem = $exp->remaining_amount;
            if ($rem > 0) {
                if ($exp->is_overdue || $exp->payment_status === 'Overdue') {
                    $totalOverdue += $rem;
                } else {
                    $totalPending += $rem;
                }
            }
        }

        // Active recurring expenses applicable this month
        $allRecurring = RecurringExpense::where('status', 'Active')->get();
        $applicableRecurringCount = $allRecurring->filter(fn($r) => $r->isApplicableFor($month, $year))->count();
        $totalRecords = $expenses->count();

        // Category breakdown
        $categoryBreakdown = $activeExpenses->groupBy('category.name')
            ->map(fn($group) => $group->sum('amount'))
            ->sortDesc();

        // Payment status counts
        $statusCounts = [
            'Paid' => $activeExpenses->where('payment_status', 'Paid')->count(),
            'Partially Paid' => $activeExpenses->where('payment_status', 'Partially Paid')->count(),
            'Pending' => $activeExpenses->where('payment_status', 'Pending')->count(),
            'Overdue' => $activeExpenses->where('payment_status', 'Overdue')->count(),
            'Cancelled' => $expenses->where('payment_status', 'Cancelled')->count(),
        ];

        // Recent expenses & Upcoming due dates
        $recentExpenses = MonthlyExpense::with('category')
            ->where('expense_month', $month)
            ->where('expense_year', $year)
            ->latest('expense_date')
            ->limit(8)
            ->get();

        $upcomingBills = $activeExpenses
            ->filter(fn($e) => $e->due_date && $e->remaining_amount > 0)
            ->sortBy('due_date')
            ->take(5);

        return view('monthly_expenses.dashboard', compact(
            'month', 'year', 'totalExpenses', 'totalUtilityBills',
            'totalPaid', 'totalPending', 'totalOverdue',
            'applicableRecurringCount', 'totalRecords',
            'categoryBreakdown', 'statusCounts', 'recentExpenses', 'upcomingBills'
        ));
    }

    /**
     * 2. All Expenses Table with filters
     */
    public function index(Request $request)
    {
        $this->authorizeFinance();

        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);
        $categoryId = $request->input('category_id');
        $status = $request->input('status');
        $utilityOnly = $request->boolean('utility_only');
        $search = $request->input('search');

        $query = MonthlyExpense::with(['category', 'creator'])
            ->orderBy('expense_date', 'desc')
            ->orderBy('id', 'desc');

        if ($month !== 'all' && $month !== null && $month !== '') {
            $query->where('expense_month', (int)$month);
        }

        if ($year !== 'all' && $year !== null && $year !== '') {
            $query->where('expense_year', (int)$year);
        }

        if (!empty($categoryId)) {
            $query->where('category_id', $categoryId);
        }

        if (!empty($status)) {
            $query->where('payment_status', $status);
        }

        if ($utilityOnly) {
            $query->where('is_utility_bill', true);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('expense_title', 'like', "%{$search}%")
                  ->orWhere('expense_code', 'like', "%{$search}%")
                  ->orWhere('provider_name', 'like', "%{$search}%")
                  ->orWhere('bill_reference', 'like', "%{$search}%")
                  ->orWhere('consumer_number', 'like', "%{$search}%");
            });
        }

        $expenses = $query->paginate(20)->withQueryString();

        $categories = ExpenseCategory::active()->orderBy('group')->orderBy('name')->get();

        return view('monthly_expenses.index', compact(
            'expenses', 'categories', 'month', 'year', 'categoryId', 'status', 'utilityOnly', 'search'
        ));
    }

    /**
     * 3. Add Monthly Expense Form
     */
    public function create(Request $request)
    {
        $this->authorizeFinance();

        $categories = ExpenseCategory::active()->orderBy('group')->orderBy('name')->get();
        $isUtility = $request->boolean('utility');
        $utilityType = $request->input('utility_type');

        return view('monthly_expenses.create', compact('categories', 'isUtility', 'utilityType'));
    }

    /**
     * 4. Store Monthly Expense
     */
    public function store(Request $request)
    {
        $this->authorizeFinance();

        $validated = $request->validate([
            'expense_title' => 'required|string|max:255',
            'category_id' => 'required|exists:expense_categories,id',
            'expense_month' => 'required|integer|between:1,12',
            'expense_year' => 'required|integer|min:2000|max:2100',
            'expense_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'initial_payment' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string|max:50',
            'provider_name' => 'nullable|string|max:255',
            'bill_reference' => 'nullable|string|max:100',
            'due_date' => 'nullable|date',
            'payment_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'is_utility_bill' => 'nullable|boolean',
            'utility_type' => 'nullable|string|max:50',
            'consumer_number' => 'nullable|string|max:100',
            'bill_issue_date' => 'nullable|date',
        ]);

        DB::beginTransaction();
        try {
            $attachmentPath = null;
            $attachmentName = null;

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $attachmentName = $file->getClientOriginalName();
                $attachmentPath = $file->store('monthly_expenses', 'local');
            }

            $isUtility = $request->boolean('is_utility_bill');

            // Category check to auto-set utility bill if in Utilities group
            $cat = ExpenseCategory::find($validated['category_id']);
            if ($cat && $cat->group === 'Utilities') {
                $isUtility = true;
                if (empty($validated['utility_type'])) {
                    $validated['utility_type'] = str_replace(' Bill', '', $cat->name);
                }
            }

            $expense = MonthlyExpense::create([
                'expense_title' => $validated['expense_title'],
                'category_id' => $validated['category_id'],
                'expense_month' => $validated['expense_month'],
                'expense_year' => $validated['expense_year'],
                'expense_date' => $validated['expense_date'],
                'amount' => $validated['amount'],
                'paid_amount' => 0.00,
                'provider_name' => $validated['provider_name'] ?? null,
                'bill_reference' => $validated['bill_reference'] ?? null,
                'due_date' => $validated['due_date'] ?? null,
                'payment_status' => 'Pending',
                'payment_method' => $validated['payment_method'] ?? 'Bank Transfer',
                'payment_date' => $validated['payment_date'] ?? null,
                'attachment_path' => $attachmentPath,
                'attachment_name' => $attachmentName,
                'is_utility_bill' => $isUtility,
                'utility_type' => $validated['utility_type'] ?? null,
                'consumer_number' => $validated['consumer_number'] ?? null,
                'bill_issue_date' => $validated['bill_issue_date'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            // If initial payment provided, record it
            $initialPay = (float)($request->input('initial_payment', 0));
            if ($initialPay > 0) {
                MonthlyExpensePayment::create([
                    'monthly_expense_id' => $expense->id,
                    'payment_date' => $validated['payment_date'] ?? $validated['expense_date'],
                    'amount' => min($initialPay, (float)$validated['amount']),
                    'payment_method' => $validated['payment_method'] ?? 'Bank Transfer',
                    'transaction_reference' => $validated['bill_reference'] ?? null,
                    'paid_by' => auth()->user()->name ?? 'System',
                    'notes' => 'Initial payment upon recording',
                    'created_by' => auth()->id(),
                ]);
            }

            $expense->recalculateStatus();

            DB::commit();

            return redirect()->route('monthly_expenses.show', $expense->id)
                ->with('success', 'Monthly expense record created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating expense: ' . $e->getMessage());
        }
    }

    /**
     * 5. View Single Expense Details & Payment History
     */
    public function show(MonthlyExpense $expense)
    {
        $this->authorizeFinance();

        $expense->load(['category', 'payments.creator', 'creator', 'updater', 'recurringExpense']);

        return view('monthly_expenses.show', compact('expense'));
    }

    /**
     * 6. Edit Expense
     */
    public function edit(MonthlyExpense $expense)
    {
        $this->authorizeFinance();

        $categories = ExpenseCategory::active()->orderBy('group')->orderBy('name')->get();

        return view('monthly_expenses.edit', compact('expense', 'categories'));
    }

    /**
     * 7. Update Expense
     */
    public function update(Request $request, MonthlyExpense $expense)
    {
        $this->authorizeFinance();

        $validated = $request->validate([
            'expense_title' => 'required|string|max:255',
            'category_id' => 'required|exists:expense_categories,id',
            'expense_month' => 'required|integer|between:1,12',
            'expense_year' => 'required|integer|min:2000|max:2100',
            'expense_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_status' => 'required|in:Pending,Partially Paid,Paid,Overdue,Cancelled',
            'payment_method' => 'nullable|string|max:50',
            'provider_name' => 'nullable|string|max:255',
            'bill_reference' => 'nullable|string|max:100',
            'due_date' => 'nullable|date',
            'payment_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'is_utility_bill' => 'nullable|boolean',
            'utility_type' => 'nullable|string|max:50',
            'consumer_number' => 'nullable|string|max:100',
            'bill_issue_date' => 'nullable|date',
        ]);

        DB::beginTransaction();
        try {
            if ($request->hasFile('attachment')) {
                // Delete old file if present
                if ($expense->attachment_path && Storage::disk('local')->exists($expense->attachment_path)) {
                    Storage::disk('local')->delete($expense->attachment_path);
                }
                $file = $request->file('attachment');
                $expense->attachment_name = $file->getClientOriginalName();
                $expense->attachment_path = $file->store('monthly_expenses', 'local');
            }

            $expense->update([
                'expense_title' => $validated['expense_title'],
                'category_id' => $validated['category_id'],
                'expense_month' => $validated['expense_month'],
                'expense_year' => $validated['expense_year'],
                'expense_date' => $validated['expense_date'],
                'amount' => $validated['amount'],
                'provider_name' => $validated['provider_name'] ?? null,
                'bill_reference' => $validated['bill_reference'] ?? null,
                'due_date' => $validated['due_date'] ?? null,
                'payment_method' => $validated['payment_method'] ?? $expense->payment_method,
                'payment_date' => $validated['payment_date'] ?? null,
                'is_utility_bill' => $request->boolean('is_utility_bill'),
                'utility_type' => $validated['utility_type'] ?? null,
                'consumer_number' => $validated['consumer_number'] ?? null,
                'bill_issue_date' => $validated['bill_issue_date'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'updated_by' => auth()->id(),
            ]);

            if ($validated['payment_status'] === 'Cancelled') {
                $expense->payment_status = 'Cancelled';
                $expense->save();
            } else {
                $expense->recalculateStatus();
            }

            DB::commit();

            return redirect()->route('monthly_expenses.show', $expense->id)
                ->with('success', 'Monthly expense updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error updating expense: ' . $e->getMessage());
        }
    }

    /**
     * 8. Delete Expense (Soft Delete)
     */
    public function destroy(MonthlyExpense $expense)
    {
        $this->authorizeAdminOnly();

        $expense->delete();

        return redirect()->route('monthly_expenses.index')
            ->with('success', 'Expense record moved to trash.');
    }

    /**
     * 9. Add Payment to Expense
     */
    public function addPayment(Request $request, MonthlyExpense $expense)
    {
        $this->authorizeFinance();

        $remaining = $expense->remaining_amount;

        $validated = $request->validate([
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01|max:' . ($remaining > 0 ? $remaining : 99999999),
            'payment_method' => 'required|string|max:50',
            'transaction_reference' => 'nullable|string|max:100',
            'paid_by' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'payment_attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        DB::beginTransaction();
        try {
            $attachmentPath = null;
            if ($request->hasFile('payment_attachment')) {
                $attachmentPath = $request->file('payment_attachment')->store('monthly_expense_payments', 'local');
            }

            MonthlyExpensePayment::create([
                'monthly_expense_id' => $expense->id,
                'payment_date' => $validated['payment_date'],
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'transaction_reference' => $validated['transaction_reference'] ?? null,
                'paid_by' => $validated['paid_by'] ?? auth()->user()->name,
                'notes' => $validated['notes'] ?? null,
                'attachment_path' => $attachmentPath,
                'created_by' => auth()->id(),
            ]);

            $expense->payment_date = $validated['payment_date'];
            $expense->payment_method = $validated['payment_method'];
            $expense->recalculateStatus();

            DB::commit();

            return back()->with('success', 'Payment of PKR ' . number_format($validated['amount'], 2) . ' recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error recording payment: ' . $e->getMessage());
        }
    }

    /**
     * 10. Delete a Payment
     */
    public function deletePayment(MonthlyExpensePayment $payment)
    {
        $this->authorizeAdminOnly();

        $expense = $payment->monthlyExpense;
        $payment->delete();
        $expense->recalculateStatus();

        return back()->with('success', 'Payment record deleted and balance recalculated.');
    }

    /**
     * 11. Dedicated Utility Bills Page
     */
    public function utilityBills(Request $request)
    {
        $this->authorizeFinance();

        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);
        $utilityType = $request->input('utility_type');
        $status = $request->input('status');

        $query = MonthlyExpense::utilityBills()
            ->with(['category', 'payments'])
            ->orderBy('expense_date', 'desc');

        if ($month !== 'all' && $month !== null && $month !== '') {
            $query->where('expense_month', (int)$month);
        }
        if ($year !== 'all' && $year !== null && $year !== '') {
            $query->where('expense_year', (int)$year);
        }
        if (!empty($utilityType)) {
            $query->where('utility_type', $utilityType);
        }
        if (!empty($status)) {
            $query->where('payment_status', $status);
        }

        $bills = $query->paginate(20)->withQueryString();

        // Specific utility type totals for the period
        $statsQuery = MonthlyExpense::utilityBills()
            ->activeNonCancelled();
        if ($month !== 'all' && $month !== null && $month !== '') {
            $statsQuery->where('expense_month', (int)$month);
        }
        if ($year !== 'all' && $year !== null && $year !== '') {
            $statsQuery->where('expense_year', (int)$year);
        }
        $allBills = $statsQuery->get();

        $totalUtilityAmount = (float)$allBills->sum('amount');
        $totalUtilityPaid = (float)$allBills->sum('paid_amount');
        $totalUtilityPending = max(0, $totalUtilityAmount - $totalUtilityPaid);

        $typeBreakdown = $allBills->groupBy('utility_type')
            ->map(fn($group) => $group->sum('amount'));

        $utilityTypes = ['Electricity', 'Gas', 'Telephone', 'Mobile', 'Internet', 'Water', 'Other'];

        return view('monthly_expenses.utility_bills', compact(
            'bills', 'month', 'year', 'utilityType', 'status',
            'totalUtilityAmount', 'totalUtilityPaid', 'totalUtilityPending',
            'typeBreakdown', 'utilityTypes'
        ));
    }

    /**
     * 12. Recurring Expenses Page & Generator
     */
    public function recurring(Request $request)
    {
        $this->authorizeFinance();

        $templates = RecurringExpense::with('category')
            ->orderBy('status', 'asc')
            ->orderBy('id', 'desc')
            ->get();

        $categories = ExpenseCategory::active()->orderBy('group')->orderBy('name')->get();

        return view('monthly_expenses.recurring', compact('templates', 'categories'));
    }

    /**
     * 13. Store Recurring Template
     */
    public function storeRecurring(Request $request)
    {
        $this->authorizeFinance();

        $validated = $request->validate([
            'expense_name' => 'required|string|max:255',
            'category_id' => 'required|exists:expense_categories,id',
            'default_amount' => 'required|numeric|min:0.01',
            'provider_name' => 'nullable|string|max:255',
            'frequency' => 'required|in:Monthly,Quarterly,Yearly',
            'start_month' => 'required|integer|between:1,12',
            'start_year' => 'required|integer|min:2000|max:2100',
            'end_month' => 'nullable|integer|between:1,12',
            'end_year' => 'nullable|integer|min:2000|max:2100',
            'due_day' => 'nullable|integer|between:1,31',
            'payment_method' => 'nullable|string|max:50',
            'is_utility_bill' => 'nullable|boolean',
            'utility_type' => 'nullable|string|max:50',
            'consumer_number' => 'nullable|string|max:100',
            'status' => 'required|in:Active,Inactive',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['is_utility_bill'] = $request->boolean('is_utility_bill');

        RecurringExpense::create($validated);

        return back()->with('success', 'Recurring expense template saved successfully.');
    }

    /**
     * 14. Update Recurring Template
     */
    public function updateRecurring(Request $request, RecurringExpense $recurring)
    {
        $this->authorizeFinance();

        $validated = $request->validate([
            'expense_name' => 'required|string|max:255',
            'category_id' => 'required|exists:expense_categories,id',
            'default_amount' => 'required|numeric|min:0.01',
            'provider_name' => 'nullable|string|max:255',
            'frequency' => 'required|in:Monthly,Quarterly,Yearly',
            'start_month' => 'required|integer|between:1,12',
            'start_year' => 'required|integer|min:2000|max:2100',
            'end_month' => 'nullable|integer|between:1,12',
            'end_year' => 'nullable|integer|min:2000|max:2100',
            'due_day' => 'nullable|integer|between:1,31',
            'payment_method' => 'nullable|string|max:50',
            'is_utility_bill' => 'nullable|boolean',
            'utility_type' => 'nullable|string|max:50',
            'consumer_number' => 'nullable|string|max:100',
            'status' => 'required|in:Active,Inactive',
            'notes' => 'nullable|string',
        ]);

        $validated['is_utility_bill'] = $request->boolean('is_utility_bill');

        $recurring->update($validated);

        return back()->with('success', 'Recurring template updated.');
    }

    /**
     * 15. Delete Recurring Template
     */
    public function destroyRecurring(RecurringExpense $recurring)
    {
        $this->authorizeAdminOnly();

        $recurring->delete();

        return back()->with('success', 'Recurring template deleted.');
    }

    /**
     * 16. Monthly Generation from Recurring Templates
     */
    public function generateRecurring(Request $request)
    {
        $this->authorizeFinance();

        $month = (int)($request->input('generate_month') ?? $request->input('month'));
        $year = (int)($request->input('generate_year') ?? $request->input('year'));

        if ($month < 1 || $month > 12 || $year < 2000 || $year > 2100) {
            return back()->with('error', 'Please provide a valid month (1-12) and year (2000-2100).');
        }

        $activeTemplates = RecurringExpense::where('status', 'Active')->get();

        $generatedCount = 0;
        $skippedCount = 0;

        $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;

        DB::beginTransaction();
        try {
            foreach ($activeTemplates as $template) {
                if (!$template->isApplicableFor($month, $year)) {
                    continue;
                }

                // Check duplicate generation
                $exists = MonthlyExpense::where('recurring_expense_id', $template->id)
                    ->where('expense_month', $month)
                    ->where('expense_year', $year)
                    ->exists();

                if ($exists) {
                    $skippedCount++;
                    continue;
                }

                $dueDay = $template->due_day ? min($template->due_day, $daysInMonth) : 10;
                $dueDate = Carbon::create($year, $month, $dueDay)->format('Y-m-d');
                $expenseDate = Carbon::create($year, $month, 1)->format('Y-m-d');

                MonthlyExpense::create([
                    'expense_title' => $template->expense_name,
                    'category_id' => $template->category_id,
                    'expense_month' => $month,
                    'expense_year' => $year,
                    'expense_date' => $expenseDate,
                    'amount' => $template->default_amount,
                    'paid_amount' => 0.00,
                    'provider_name' => $template->provider_name,
                    'due_date' => $dueDate,
                    'payment_status' => 'Pending',
                    'payment_method' => $template->payment_method ?? 'Bank Transfer',
                    'recurring_expense_id' => $template->id,
                    'is_utility_bill' => $template->is_utility_bill,
                    'utility_type' => $template->utility_type,
                    'consumer_number' => $template->consumer_number,
                    'notes' => 'Generated from recurring template: ' . $template->expense_name,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);

                $generatedCount++;
            }

            DB::commit();

            $monthName = Carbon::create($year, $month, 1)->format('F Y');
            return back()->with('success', "Generated {$generatedCount} expense(s) for {$monthName}. ({$skippedCount} skipped as already generated).");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error generating expenses: ' . $e->getMessage());
        }
    }

    /**
     * 17. Monthly Expense Report
     */
    public function reports(Request $request)
    {
        $this->authorizeFinance();

        $month = (int)$request->input('month', Carbon::now()->month);
        $year = (int)$request->input('year', Carbon::now()->year);
        $categoryId = $request->input('category_id');
        $status = $request->input('status');
        $provider = $request->input('provider');

        $query = MonthlyExpense::with('category')
            ->where('expense_month', $month)
            ->where('expense_year', $year)
            ->orderBy('expense_date', 'asc');

        if (!empty($categoryId)) {
            $query->where('category_id', $categoryId);
        }
        if (!empty($status)) {
            $query->where('payment_status', $status);
        }
        if (!empty($provider)) {
            $query->where('provider_name', 'like', "%{$provider}%");
        }

        $expenses = $query->get();

        $active = $expenses->where('payment_status', '!=', 'Cancelled');

        $totalExpenses = (float)$active->sum('amount');
        $totalPaid = (float)$active->sum('paid_amount');
        
        $totalPending = 0.0;
        $totalOverdue = 0.0;

        foreach ($active as $exp) {
            $rem = $exp->remaining_amount;
            if ($rem > 0) {
                if ($exp->is_overdue || $exp->payment_status === 'Overdue') {
                    $totalOverdue += $rem;
                } else {
                    $totalPending += $rem;
                }
            }
        }

        $categories = ExpenseCategory::active()->orderBy('name')->get();

        return view('monthly_expenses.reports', compact(
            'expenses', 'month', 'year', 'categoryId', 'status', 'provider',
            'totalExpenses', 'totalPaid', 'totalPending', 'totalOverdue', 'categories'
        ));
    }

    /**
     * 18. Month-to-Month Comparison Report
     */
    public function comparisonReport(Request $request)
    {
        $this->authorizeFinance();

        $m1 = (int)$request->input('m1', Carbon::now()->subMonth()->month);
        $y1 = (int)$request->input('y1', Carbon::now()->subMonth()->year);

        $m2 = (int)$request->input('m2', Carbon::now()->month);
        $y2 = (int)$request->input('y2', Carbon::now()->year);

        $expenses1 = MonthlyExpense::with('category')
            ->where('expense_month', $m1)
            ->where('expense_year', $y1)
            ->activeNonCancelled()
            ->get();

        $expenses2 = MonthlyExpense::with('category')
            ->where('expense_month', $m2)
            ->where('expense_year', $y2)
            ->activeNonCancelled()
            ->get();

        $categories = ExpenseCategory::active()->orderBy('name')->get();

        $comparison = [];
        $total1 = 0;
        $total2 = 0;

        foreach ($categories as $cat) {
            $amt1 = (float)$expenses1->where('category_id', $cat->id)->sum('amount');
            $amt2 = (float)$expenses2->where('category_id', $cat->id)->sum('amount');

            if ($amt1 > 0 || $amt2 > 0) {
                $diff = $amt2 - $amt1;
                $pct = $amt1 > 0 ? (($diff / $amt1) * 100) : ($amt2 > 0 ? 100 : 0);

                $comparison[] = [
                    'category' => $cat->name,
                    'group' => $cat->group,
                    'amount1' => $amt1,
                    'amount2' => $amt2,
                    'difference' => $diff,
                    'percentage' => $pct,
                ];

                $total1 += $amt1;
                $total2 += $amt2;
            }
        }

        $grandDiff = $total2 - $total1;
        $grandPct = $total1 > 0 ? (($grandDiff / $total1) * 100) : ($total2 > 0 ? 100 : 0);

        return view('monthly_expenses.comparison', compact(
            'm1', 'y1', 'm2', 'y2', 'comparison', 'total1', 'total2', 'grandDiff', 'grandPct'
        ));
    }

    /**
     * 19. Yearly Expense Report
     */
    public function yearlyReport(Request $request)
    {
        $this->authorizeFinance();

        $year = (int)$request->input('year', Carbon::now()->year);

        $expenses = MonthlyExpense::where('expense_year', $year)
            ->activeNonCancelled()
            ->get();

        $monthlyData = [];
        $yearlyTotal = 0;
        $yearlyUtility = 0;
        $yearlyPaid = 0;
        $yearlyPending = 0;

        for ($m = 1; $m <= 12; $m++) {
            $mExpenses = $expenses->where('expense_month', $m);

            $mTotal = (float)$mExpenses->sum('amount');
            $mUtility = (float)$mExpenses->where('is_utility_bill', true)->sum('amount');
            $mPaid = (float)$mExpenses->sum('paid_amount');
            $mPending = max(0, $mTotal - $mPaid);

            $monthlyData[$m] = [
                'month_name' => Carbon::create($year, $m, 1)->format('F'),
                'total' => $mTotal,
                'utility' => $mUtility,
                'paid' => $mPaid,
                'pending' => $mPending,
            ];

            $yearlyTotal += $mTotal;
            $yearlyUtility += $mUtility;
            $yearlyPaid += $mPaid;
            $yearlyPending += $mPending;
        }

        return view('monthly_expenses.yearly', compact(
            'year', 'monthlyData', 'yearlyTotal', 'yearlyUtility', 'yearlyPaid', 'yearlyPending'
        ));
    }

    /**
     * 20. Expense Categories Management
     */
    public function categories(Request $request)
    {
        $this->authorizeFinance();

        $categories = ExpenseCategory::withCount('monthlyExpenses')
            ->orderBy('group')
            ->orderBy('name')
            ->get();

        $groups = ['Utilities', 'Office Expenses', 'Transport and Vehicle', 'Technology', 'Business Expenses', 'Other'];

        return view('monthly_expenses.categories', compact('categories', 'groups'));
    }

    public function storeCategory(Request $request)
    {
        $this->authorizeFinance();

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories,name',
            'group' => 'required|string|max:100',
            'description' => 'nullable|string',
            'status' => 'required|in:Active,Inactive',
        ]);

        ExpenseCategory::create($validated);

        return back()->with('success', 'New expense category created successfully.');
    }

    public function updateCategory(Request $request, ExpenseCategory $category)
    {
        $this->authorizeFinance();

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories,name,' . $category->id,
            'group' => 'required|string|max:100',
            'description' => 'nullable|string',
            'status' => 'required|in:Active,Inactive',
        ]);

        $category->update($validated);

        return back()->with('success', 'Category updated successfully.');
    }

    public function destroyCategory(ExpenseCategory $category)
    {
        $this->authorizeAdminOnly();

        // Guard: Do not delete if category has historical records!
        if ($category->monthlyExpenses()->count() > 0) {
            return back()->with('error', 'Cannot delete this category because historical expenses are associated with it. You can deactivate it instead.');
        }

        $category->delete();

        return back()->with('success', 'Category deleted successfully.');
    }

    /**
     * 21. Export & Print functionality (Excel / CSV / PDF)
     */
    public function export(Request $request, $format = 'csv')
    {
        $this->authorizeFinance();

        $month = (int)$request->input('month', Carbon::now()->month);
        $year = (int)$request->input('year', Carbon::now()->year);
        $categoryId = $request->input('category_id');
        $status = $request->input('status');

        $query = MonthlyExpense::with('category')
            ->where('expense_month', $month)
            ->where('expense_year', $year)
            ->orderBy('expense_date', 'asc');

        if (!empty($categoryId)) {
            $query->where('category_id', $categoryId);
        }
        if (!empty($status)) {
            $query->where('payment_status', $status);
        }

        $expenses = $query->get();
        $monthName = Carbon::create($year, $month, 1)->format('F_Y');

        if ($format === 'pdf') {
            $active = $expenses->where('payment_status', '!=', 'Cancelled');
            $totalExpenses = (float)$active->sum('amount');
            $totalPaid = (float)$active->sum('paid_amount');
            $totalPending = max(0, $totalExpenses - $totalPaid);
            $totalOverdue = (float)$active->filter(fn($e) => $e->is_overdue)->sum('remaining_amount');

            $categoryTotals = $active->groupBy('category.name')
                ->map(fn($g) => $g->sum('amount'));

            $pdf = Pdf::loadView('monthly_expenses.pdf_report', compact(
                'expenses', 'month', 'year', 'totalExpenses', 'totalPaid', 'totalPending', 'totalOverdue', 'categoryTotals'
            ));
            return $pdf->download("Monthly_Expense_Report_{$monthName}.pdf");
        }

        // CSV or Excel
        $filename = "monthly_expenses_{$monthName}." . ($format === 'excel' ? 'csv' : 'csv');

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($expenses) {
            $file = fopen('php://output', 'w');
            // Write BOM for UTF-8 in Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, [
                'Expense Code', 'Date', 'Month/Year', 'Category', 'Expense Title',
                'Provider / Paid To', 'Bill/Ref #', 'Amount (PKR)', 'Paid (PKR)',
                'Remaining (PKR)', 'Due Date', 'Status', 'Payment Method', 'Notes'
            ]);

            foreach ($expenses as $e) {
                fputcsv($file, [
                    $e->expense_code,
                    $e->expense_date ? $e->expense_date->format('Y-m-d') : '',
                    Carbon::create($e->expense_year, $e->expense_month, 1)->format('F Y'),
                    $e->category ? $e->category->name : '-',
                    $e->expense_title,
                    $e->provider_name ?? '-',
                    $e->bill_reference ?? '-',
                    number_format($e->amount, 2, '.', ''),
                    number_format($e->paid_amount, 2, '.', ''),
                    number_format($e->remaining_amount, 2, '.', ''),
                    $e->due_date ? $e->due_date->format('Y-m-d') : '-',
                    $e->payment_status,
                    $e->payment_method ?? '-',
                    $e->notes ?? ''
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Printable Report View
     */
    public function printReport(Request $request)
    {
        $this->authorizeFinance();

        $month = (int)$request->input('month', Carbon::now()->month);
        $year = (int)$request->input('year', Carbon::now()->year);

        $expenses = MonthlyExpense::with('category')
            ->where('expense_month', $month)
            ->where('expense_year', $year)
            ->orderBy('expense_date', 'asc')
            ->get();

        $active = $expenses->where('payment_status', '!=', 'Cancelled');
        $totalExpenses = (float)$active->sum('amount');
        $totalPaid = (float)$active->sum('paid_amount');
        $totalPending = max(0, $totalExpenses - $totalPaid);
        $totalOverdue = (float)$active->filter(fn($e) => $e->is_overdue)->sum('remaining_amount');

        $categoryTotals = $active->groupBy('category.name')
            ->map(fn($g) => $g->sum('amount'));

        return view('monthly_expenses.print_report', compact(
            'expenses', 'month', 'year', 'totalExpenses', 'totalPaid', 'totalPending', 'totalOverdue', 'categoryTotals'
        ));
    }

    /**
     * 22. Secure Attachment Download/View
     */
    public function viewAttachment(MonthlyExpense $expense)
    {
        $this->authorizeFinance();

        if (!$expense->attachment_path || !Storage::disk('local')->exists($expense->attachment_path)) {
            abort(404, 'Attachment file not found.');
        }

        return response()->file(Storage::disk('local')->path($expense->attachment_path), [
            'Content-Disposition' => 'inline; filename="' . ($expense->attachment_name ?? 'expense_attachment') . '"'
        ]);
    }
}
