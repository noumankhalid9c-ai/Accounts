<?php

namespace App\Http\Controllers;

use App\Models\Salary;
use App\Models\Employee;
use Illuminate\Http\Request;

class SalaryController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', date('n'));
        $year = $request->input('year', date('Y'));
        $employee_id = $request->input('employee_id');
        $status = $request->input('status');

        $query = Salary::with('employee')
            ->where('salary_year', $year)
            ->where('salary_month', $month);

        if ($employee_id) {
            $query->where('employee_id', $employee_id);
        }
        if ($status) {
            $query->where('status', $status);
        }

        $salaries = $query->get();

        // Summary calculations
        $summary = [
            'total_employees' => $salaries->count(),
            'total_basic_salary' => $salaries->sum('basic_salary'),
            'total_commission' => $salaries->sum('sales_commission'),
            'total_incentive' => $salaries->sum('incentive'),
            'total_advance' => $salaries->sum('advance_salary'),
            'total_deduction' => $salaries->sum('deduction'),
            'total_net_salary' => $salaries->sum('net_salary'),
            'total_paid' => $salaries->where('status', 'Paid')->sum('net_salary'),
            'total_pending' => $salaries->where('status', 'Pending')->sum('net_salary'),
        ];

        $employees = Employee::orderBy('name')->get();

        return view('salaries.index', compact('salaries', 'month', 'year', 'employee_id', 'status', 'summary', 'employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'salary_month' => 'required|integer|min:1|max:12',
            'salary_year' => 'required|integer',
            'basic_salary' => 'required|numeric|min:0',
            'sales_commission' => 'nullable|numeric|min:0',
            'incentive' => 'nullable|numeric|min:0',
            'advance_salary' => 'nullable|numeric|min:0',
            'deduction' => 'nullable|numeric|min:0',
            'status' => 'required|in:Pending,Paid',
            'notes' => 'nullable|string'
        ]);

        // Check for duplicate
        $exists = Salary::where('employee_id', $validated['employee_id'])
                        ->where('salary_month', $validated['salary_month'])
                        ->where('salary_year', $validated['salary_year'])
                        ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Salary record already exists for this employee for the selected month and year.');
        }

        if ($validated['status'] == 'Paid') {
            $validated['payment_date'] = now()->toDateString();
        }

        Salary::create($validated);

        return redirect()->back()->with('success', 'Salary record added successfully.');
    }

    public function update(Request $request, Salary $salary)
    {
        $validated = $request->validate([
            'basic_salary' => 'required|numeric|min:0',
            'sales_commission' => 'nullable|numeric|min:0',
            'incentive' => 'nullable|numeric|min:0',
            'advance_salary' => 'nullable|numeric|min:0',
            'deduction' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        $salary->update($validated);

        return redirect()->back()->with('success', 'Salary updated successfully.');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer'
        ]);

        $month = $request->month;
        $year = $request->year;

        $activeEmployees = Employee::where('status', 'Active')->get();
        $generatedCount = 0;

        foreach ($activeEmployees as $employee) {
            $exists = Salary::where('employee_id', $employee->id)
                            ->where('salary_month', $month)
                            ->where('salary_year', $year)
                            ->exists();

            if (!$exists) {
                Salary::create([
                    'employee_id' => $employee->id,
                    'salary_month' => $month,
                    'salary_year' => $year,
                    'basic_salary' => $employee->basic_salary,
                    'sales_commission' => 0,
                    'incentive' => 0,
                    'advance_salary' => 0,
                    'deduction' => 0,
                    'status' => 'Pending'
                ]);
                $generatedCount++;
            }
        }

        return redirect()->back()->with('success', "Successfully generated $generatedCount salary records for " . date('F Y', mktime(0, 0, 0, $month, 1, $year)) . ".");
    }

    public function markPaid(Salary $salary)
    {
        $salary->update([
            'status' => 'Paid',
            'payment_date' => now()->toDateString()
        ]);

        return redirect()->back()->with('success', 'Salary marked as Paid.');
    }

    public function markPending(Salary $salary)
    {
        $salary->update([
            'status' => 'Pending',
            'payment_date' => null
        ]);

        return redirect()->back()->with('success', 'Salary marked as Pending.');
    }

    public function destroy(Salary $salary)
    {
        $salary->delete();
        return redirect()->back()->with('success', 'Salary record deleted.');
    }

    public function slip(Salary $salary)
    {
        $salary->load('employee');
        return view('salaries.slip', compact('salary'));
    }
}
