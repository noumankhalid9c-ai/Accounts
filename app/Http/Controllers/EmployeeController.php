<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::orderBy('name')->get();
        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        return view('employees.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|unique:employees,employee_id',
            'name' => 'required|string|max:255',
            'designation' => 'nullable|string|max:255',
            'joining_date' => 'nullable|date',
            'basic_salary' => 'required|numeric|min:0',
            'contact' => 'nullable|string|max:255',
            'status' => 'required|in:Active,Inactive'
        ]);

        Employee::create($validated);
        return redirect()->route('employees.index')->with('success', 'Employee added successfully.');
    }

    public function show(Employee $employee)
    {
        $salaries = $employee->salaries()->orderBy('salary_year', 'desc')->orderBy('salary_month', 'desc')->get();
        return view('employees.show', compact('employee', 'salaries'));
    }

    public function edit(Employee $employee)
    {
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'employee_id' => 'required|unique:employees,employee_id,'.$employee->id,
            'name' => 'required|string|max:255',
            'designation' => 'nullable|string|max:255',
            'joining_date' => 'nullable|date',
            'basic_salary' => 'required|numeric|min:0',
            'contact' => 'nullable|string|max:255',
            'status' => 'required|in:Active,Inactive'
        ]);

        $employee->update($validated);
        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }
    
    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
    }
}
