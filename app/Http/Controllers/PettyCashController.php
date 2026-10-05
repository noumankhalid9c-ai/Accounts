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
        
        $monthQuery = PettyCashTransaction::whereMonth('date', $today->month)
                                          ->whereYear('date', $today->year)
                                          ->where('transaction_type', 'Expense');
                                             
        $monthExpenses = (clone $monthQuery)->sum('amount');
        $monthCashExpenses = (clone $monthQuery)->where('payment_method', 'Cash')->sum('amount');
        $monthBankExpenses = (clone $monthQuery)->where('payment_method', 'Bank')->sum('amount');
        $monthCardExpenses = (clone $monthQuery)->where('payment_method', 'Card')->sum('amount');
        
        $totalExpenses = PettyCashTransaction::where('transaction_type', 'Expense')->sum('amount');
        
        return view('petty_cash.dashboard', compact(
            'day', 'monthExpenses', 'monthCashExpenses', 'monthBankExpenses', 'monthCardExpenses', 'totalExpenses'
        ));
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

    public function printTransaction(PettyCashTransaction $transaction)
    {
        $transaction->load(['category', 'creator', 'day']);
        return view('petty_cash.print', compact('transaction'));
    }

    public function reports(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        
        $transactions = PettyCashTransaction::with(['category', 'creator'])
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->get();
            
        $totalReceived = $transactions->where('transaction_type', 'Cash Received')->sum('amount');
        $totalExpenses = $transactions->where('transaction_type', 'Expense')->sum('amount');
        
        return view('petty_cash.reports', compact('transactions', 'startDate', 'endDate', 'totalReceived', 'totalExpenses'));
    }

    public function export(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        
        $transactions = PettyCashTransaction::with(['category', 'creator'])
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'asc')
            ->get();

        $filename = "petty_cash_report_{$startDate}_to_{$endDate}.csv";
        
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];
        
        $callback = function() use($transactions) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Type', 'Category', 'Description', 'Paid To/Received From', 'Amount (PKR)', 'Payment Method', 'Added By']);
            
            foreach ($transactions as $t) {
                fputcsv($file, [
                    Carbon::parse($t->date)->format('Y-m-d'),
                    $t->transaction_type,
                    $t->category ? $t->category->name : '-',
                    $t->description,
                    $t->paid_to ?? '-',
                    $t->amount,
                    $t->payment_method,
                    $t->creator ? $t->creator->name : 'System'
                ]);
            }
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}