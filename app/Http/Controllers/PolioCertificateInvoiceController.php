<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PolioCertificateInvoice;
use App\Models\PolioInvoicePayment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class PolioCertificateInvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:polio_invoices.view')->only(['index', 'show', 'printInvoice', 'downloadPdf']);
        $this->middleware('permission:polio_invoices.create')->only(['create', 'store']);
        $this->middleware('permission:polio_invoices.edit')->only(['edit', 'update']);
        $this->middleware('permission:polio_invoices.delete')->only(['destroy', 'deletePayment']);
        $this->middleware('permission:polio_invoices.payments')->only(['addPayment']);
    }

    /**
     * 1. Invoices Listing Page
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $query = PolioCertificateInvoice::with(['creator', 'payments'])
            ->orderBy('invoice_date', 'desc')
            ->orderBy('id', 'desc');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('client_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('passport_number', 'like', "%{$search}%");
            });
        }

        if (!empty($status) && $status !== 'All') {
            $query->where('payment_status', $status);
        }

        if (!empty($fromDate)) {
            $query->whereDate('invoice_date', '>=', $fromDate);
        }

        if (!empty($toDate)) {
            $query->whereDate('invoice_date', '<=', $toDate);
        }

        // Summary Statistics
        $totalInvoicesCount = (clone $query)->count();
        $totalAmountSum = (clone $query)->sum('total_amount');
        $totalPaidSum = (clone $query)->sum('amount_paid');
        $totalBalanceSum = (clone $query)->sum('balance_due');

        $invoices = $query->paginate(15)->withQueryString();

        return view('polio_invoices.index', compact(
            'invoices',
            'search',
            'status',
            'fromDate',
            'toDate',
            'totalInvoicesCount',
            'totalAmountSum',
            'totalPaidSum',
            'totalBalanceSum'
        ));
    }

    /**
     * 2. Create Invoice Form
     */
    public function create()
    {
        $nextInvoiceNumber = PolioCertificateInvoice::generateNextInvoiceNumber();
        $defaultDate = Carbon::today()->format('Y-m-d');

        return view('polio_invoices.create', compact('nextInvoiceNumber', 'defaultDate'));
    }

    /**
     * 3. Store Invoice
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_date' => 'required|date',
            'client_name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'passport_number' => 'nullable|string|max:50',
            'persons' => 'required|integer|min:1',
            'cost_per_person' => 'required|numeric|min:0',
            'amount_paid' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $persons = (int)$validated['persons'];
        $costPerPerson = (float)$validated['cost_per_person'];
        $totalAmount = round($persons * $costPerPerson, 2);

        $initialPaid = round((float)($validated['amount_paid'] ?? 0), 2);
        if ($initialPaid > $totalAmount) {
            $initialPaid = $totalAmount;
        }

        $balanceDue = max(0, round($totalAmount - $initialPaid, 2));

        if ($totalAmount > 0 && $initialPaid >= $totalAmount) {
            $status = 'Paid';
        } elseif ($initialPaid > 0) {
            $status = 'Partially Paid';
        } else {
            $status = 'Pending';
        }

        DB::beginTransaction();
        try {
            $invoice = PolioCertificateInvoice::create([
                'invoice_date' => $validated['invoice_date'],
                'client_name' => $validated['client_name'],
                'phone' => $validated['phone'],
                'passport_number' => $validated['passport_number'] ?? null,
                'persons' => $persons,
                'cost_per_person' => $costPerPerson,
                'total_amount' => $totalAmount,
                'amount_paid' => $initialPaid,
                'balance_due' => $balanceDue,
                'payment_status' => $status,
                'payment_method' => $validated['payment_method'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            // If an initial payment was entered, record it in payment history
            if ($initialPaid > 0) {
                PolioInvoicePayment::create([
                    'polio_invoice_id' => $invoice->id,
                    'payment_date' => $validated['invoice_date'],
                    'amount' => $initialPaid,
                    'payment_method' => $validated['payment_method'] ?? 'Cash',
                    'transaction_reference' => 'Initial Payment',
                    'notes' => 'Payment received upon invoice creation',
                    'created_by' => auth()->id(),
                ]);
            }

            DB::commit();

            return redirect()->route('polio-invoices.show', $invoice->id)
                ->with('success', "Polio Certificate Invoice {$invoice->invoice_number} created successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating invoice: ' . $e->getMessage());
        }
    }

    /**
     * 4. View Invoice Details & Payment History
     */
    public function show(PolioCertificateInvoice $polioInvoice)
    {
        $polioInvoice->load(['creator', 'updater', 'payments.creator']);

        return view('polio_invoices.show', compact('polioInvoice'));
    }

    /**
     * 5. Edit Invoice
     */
    public function edit(PolioCertificateInvoice $polioInvoice)
    {
        return view('polio_invoices.edit', compact('polioInvoice'));
    }

    /**
     * 6. Update Invoice
     */
    public function update(Request $request, PolioCertificateInvoice $polioInvoice)
    {
        $validated = $request->validate([
            'invoice_date' => 'required|date',
            'client_name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'passport_number' => 'nullable|string|max:50',
            'persons' => 'required|integer|min:1',
            'cost_per_person' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $persons = (int)$validated['persons'];
        $costPerPerson = (float)$validated['cost_per_person'];
        $totalAmount = round($persons * $costPerPerson, 2);

        DB::beginTransaction();
        try {
            $polioInvoice->update([
                'invoice_date' => $validated['invoice_date'],
                'client_name' => $validated['client_name'],
                'phone' => $validated['phone'],
                'passport_number' => $validated['passport_number'] ?? null,
                'persons' => $persons,
                'cost_per_person' => $costPerPerson,
                'total_amount' => $totalAmount,
                'notes' => $validated['notes'] ?? null,
                'updated_by' => auth()->id(),
            ]);

            // Recalculate status and balance due based on recorded payments
            $polioInvoice->recalculatePayments();

            DB::commit();

            return redirect()->route('polio-invoices.show', $polioInvoice->id)
                ->with('success', 'Polio Certificate Invoice updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error updating invoice: ' . $e->getMessage());
        }
    }

    /**
     * 7. Delete Invoice (Admin only, Soft Delete)
     */
    public function destroy(PolioCertificateInvoice $polioInvoice)
    {
        $this->authorizeAdminOnly();

        $invoiceNum = $polioInvoice->invoice_number;
        $polioInvoice->delete();

        return redirect()->route('polio-invoices.index')
            ->with('success', "Invoice {$invoiceNum} deleted successfully.");
    }

    /**
     * 8. Record Additional Payment (Installments)
     */
    public function addPayment(Request $request, PolioCertificateInvoice $polioInvoice)
    {
        $remaining = $polioInvoice->balance_due;

        $validated = $request->validate([
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01|max:' . ($remaining > 0 ? $remaining : 99999999),
            'payment_method' => 'required|string|max:50',
            'transaction_reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            PolioInvoicePayment::create([
                'polio_invoice_id' => $polioInvoice->id,
                'payment_date' => $validated['payment_date'],
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'transaction_reference' => $validated['transaction_reference'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            // Model booted event automatically invokes recalculatePayments()
            DB::commit();

            return back()->with('success', 'Payment of PKR ' . number_format($validated['amount'], 2) . ' recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error recording payment: ' . $e->getMessage());
        }
    }

    /**
     * 9. Delete Payment (Admin only)
     */
    public function deletePayment(PolioInvoicePayment $payment)
    {
        $this->authorizeAdminOnly();

        $invoice = $payment->invoice;
        $payment->delete();
        $invoice->recalculatePayments();

        return back()->with('success', 'Payment record deleted and balance recalculated.');
    }

    /**
     * 10. Printable Invoice View (Browser A4 Print)
     */
    public function printInvoice(PolioCertificateInvoice $polioInvoice)
    {
        $polioInvoice->load(['creator', 'payments']);

        return view('polio_invoices.print', compact('polioInvoice'));
    }

    /**
     * 11. Download Invoice PDF (DomPDF A4)
     */
    public function downloadPdf(PolioCertificateInvoice $polioInvoice)
    {
        $polioInvoice->load(['creator', 'payments']);

        $pdf = Pdf::loadView('polio_invoices.pdf', compact('polioInvoice'))
            ->setPaper('a4', 'portrait');

        $fileName = 'Polio_Invoice_' . $polioInvoice->invoice_number . '.pdf';

        return $pdf->download($fileName);
    }
}
