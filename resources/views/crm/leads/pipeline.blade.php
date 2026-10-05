@extends('layouts.app')
@section('content')
<style>
    .kanban-board {
        display: flex;
        flex-wrap: wrap; /* Changed from nowrap to wrap */
        align-items: flex-start;
        gap: 20px; /* slightly more gap for grid layout */
    }
    .kanban-column {
        flex: 1 1 calc(25% - 20px); /* 4 columns per row */
        min-width: 250px; /* ensures it doesn't get too squished on mobile */
        background: #f4f6f8;
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        margin-bottom: 10px;
    }
    .kanban-header {
        padding: 12px 15px;
        font-weight: 700;
        font-size: 0.9rem;
        text-transform: uppercase;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
        color: #fff;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .kanban-cards {
        padding: 10px;
        max-height: 400px; /* Optional: adds a vertical scroll inside the column if it gets too long, keeping the grid clean */
        overflow-y: auto;
        flex: 1;
    }
    .kanban-card {
        background: #fff;
        border-radius: 6px;
        padding: 12px;
        margin-bottom: 10px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border-left: 4px solid transparent;
        transition: transform 0.1s ease, box-shadow 0.1s ease;
    }
    .kanban-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.12);
    }
    .kanban-card-title {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 4px;
        color: #2c3e50;
    }
    .kanban-card-subtitle {
        font-size: 0.8rem;
        color: #6c757d;
        margin-bottom: 8px;
    }
    .kanban-card-price {
        font-size: 0.85rem;
        font-weight: 700;
        color: #198754;
        margin-bottom: 8px;
    }
    .kanban-card-assigned {
        font-size: 0.75rem;
        color: #fff;
        background-color: #6c757d;
        padding: 2px 6px;
        border-radius: 4px;
        display: inline-block;
    }
    .stage-select {
        border: 1px solid #ced4da;
        font-size: 0.75rem;
        padding: 4px;
        border-radius: 4px;
        width: 100%;
        background-color: #f8f9fa;
        color: #495057;
        margin-top: 8px;
    }
    
    /* Stage Colors */
    .bg-new-inquiry { background-color: #0d6efd; }
    .bg-contacted { background-color: #0dcaf0; color: #000; }
    .bg-follow-up { background-color: #ffc107; color: #000; }
    .bg-quotation-sent { background-color: #6f42c1; }
    .bg-negotiation { background-color: #fd7e14; }
    .bg-confirmed { background-color: #20c997; }
    .bg-converted { background-color: #198754; }
    .bg-lost { background-color: #dc3545; }
</style>

<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="m-0 font-weight-bold text-dark">Sales Pipeline</h2>
        <a href="{{ route('crm.leads') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-list"></i> Switch to List View
        </a>
    </div>
    
    <div class="kanban-board">
        @php
            $stageColors = [
                'New Inquiry' => 'bg-new-inquiry',
                'Contacted' => 'bg-contacted',
                'Follow-up' => 'bg-follow-up',
                'Quotation Sent' => 'bg-quotation-sent',
                'Negotiation' => 'bg-negotiation',
                'Confirmed' => 'bg-confirmed',
                'Converted' => 'bg-converted',
                'Lost' => 'bg-lost'
            ];
        @endphp

        @foreach($stages as $stage)
            @php 
                $stageLeads = $leads->where('stage', $stage);
                $colorClass = $stageColors[$stage] ?? 'bg-secondary';
            @endphp
            <div class="kanban-column shadow-sm">
                <div class="kanban-header {{ $colorClass }}">
                    <span>{{ $stage }}</span>
                    <span class="badge bg-light text-dark rounded-pill">{{ $stageLeads->count() }}</span>
                </div>
                <div class="kanban-cards">
                    @foreach($stageLeads as $lead)
                        <div class="kanban-card">
                            <div class="kanban-card-title">
                                <a href="{{ route('crm.leads.show', $lead->id) }}" class="text-decoration-none text-dark">
                                    {{ $lead->customer_name }}
                                </a>
                            </div>
                            <div class="kanban-card-subtitle">
                                <i class="bi bi-tag-fill me-1"></i>{{ $lead->inquiry_type }} &middot; {{ $lead->destination }}
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="kanban-card-price">
                                    PKR {{ number_format((float)$lead->budget, 0) }}
                                </div>
                                <div class="kanban-card-assigned">
                                    <i class="bi bi-person-fill"></i> {{ explode(' ', $lead->assignedTo->name ?? 'None')[0] }}
                                </div>
                            </div>
                            
                            <form action="{{ route('crm.leads.stage', $lead->id) }}" method="POST">
                                @csrf
                                <select name="stage" class="stage-select" onchange="this.form.submit()">
                                    <option value="" disabled>Move to...</option>
                                    @foreach($stages as $opt)
                                        <option value="{{ $opt }}" {{ $lead->stage == $opt ? 'selected' : '' }}>
                                            &rarr; {{ $opt }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    @endforeach
                    
                    @if($stageLeads->isEmpty())
                        <div class="text-center p-3 text-muted small" style="border: 1px dashed #ccc; border-radius: 6px;">
                            No leads in this stage
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
