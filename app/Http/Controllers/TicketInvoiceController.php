<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TicketInvoice;

class TicketInvoiceController extends Controller
{
    public function index()
    {
        $invoices = TicketInvoice::with(['ticket.customer', 'ticket.b2bAgent'])->latest()->get();
        return view('airline_tickets.invoices.index', compact('invoices'));
    }
}
