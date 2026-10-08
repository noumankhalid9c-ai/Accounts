@extends('layouts.app')

@section('title', 'Ticket Invoices')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Ticket Invoices</h1>
        <p class="text-muted mb-0">Manage all generated invoices for airline tickets.</p>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Invoice #</th>
                        <th>Invoice Date</th>
                        <th>PNR</th>
                        <th>Client / Agent</th>
                        <th>Amount</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                    <tr>
                        <td class="ps-4 fw-bold text-primary">{{ $invoice->invoice_number }}</td>
                        <td>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $invoice->ticket->pnr ?? 'N/A' }}</span></td>
                        <td>
                            @if($invoice->ticket && $invoice->ticket->customer_id)
                                <div class="fw-bold">{{ $invoice->ticket->customer->name ?? '' }}</div>
                                <div class="small text-muted">Direct Client</div>
                            @elseif($invoice->ticket && $invoice->ticket->b2b_agent_id)
                                <div class="fw-bold">{{ $invoice->ticket->b2bAgent->company_name ?? '' }}</div>
                                <div class="small text-muted">B2B Agent</div>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td class="fw-bold text-success">PKR {{ number_format($invoice->ticket->total_fare ?? 0, 2) }}</td>
                        <td class="text-end pe-4">
                            @if($invoice->ticket)
                            <a href="{{ route('airline-tickets.invoice', $invoice->ticket->id) }}" class="btn btn-sm btn-light border text-primary" target="_blank" title="View HTML Invoice">
                                <i class="bi bi-file-earmark-text"></i>
                            </a>
                            <a href="{{ route('airline-tickets.invoice.pdf', $invoice->ticket->id) }}" class="btn btn-sm btn-light border text-danger" title="Download PDF">
                                <i class="bi bi-file-earmark-pdf"></i>
                            </a>
                            <a href="{{ route('airline-tickets.show', $invoice->ticket->id) }}" class="btn btn-sm btn-light border" title="Go to Ticket">
                                <i class="bi bi-box-arrow-up-right"></i>
                            </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">No invoices generated yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
