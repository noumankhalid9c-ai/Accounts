@extends('layouts.app')

@section('title', 'Manage Clients')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-header-title fs-3 fw-bold mb-1">Manage Clients</h1>
        <p class="page-header-subtitle text-muted mb-0">View, add, and manage your client portfolio.</p>
    </div>
    <div>
        <a href="{{ route('clients.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2">
            <i class="bi bi-person-plus-fill"></i>
            <span>Add New Client</span>
        </a>
    </div>
</div>

<x-ui.card icon="bi-people" title="Client Directory">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">
                <tr>
                    <th>ID</th>
                    <th>Company / Client</th>
                    <th>Contact Info</th>
                    <th>Country</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($clients as $client)
                <tr>
                    <td class="text-muted fw-medium">#{{ $client->id }}</td>
                    <td>
                        <a href="{{ route('clients.show', $client->id) }}" class="fw-bold text-dark text-decoration-none">{{ $client->company_name ?: $client->name }}</a>
                        <div class="small text-muted">{{ $client->contact_person }}</div>
                    </td>
                    <td>
                        <div><a href="mailto:{{ $client->email }}" class="text-decoration-none text-muted small"><i class="bi bi-envelope me-1"></i>{{ $client->email }}</a></div>
                        @if($client->phone)
                            <div><span class="text-muted small"><i class="bi bi-telephone me-1"></i>{{ $client->phone }}</span></div>
                        @endif
                        @if($client->mobile)
                            <div><span class="text-muted small"><i class="bi bi-phone me-1"></i>{{ $client->mobile }}</span></div>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border">{{ $client->country ?: 'N/A' }}</span>
                        @if($client->reference_type === 'Vendor' && $client->vendor_name)
                            <div class="small text-muted mt-1"><i class="bi bi-diagram-2"></i> {{ $client->vendor_name }}</div>
                        @endif
                    </td>
                    <td>
                        @php
                            $statusClass = match($client->status) {
                                'Active' => 'success',
                                'Prospect' => 'info',
                                'Suspended' => 'danger',
                                default => 'secondary',
                            };
                        @endphp
                        <span class="badge bg-{{ $statusClass }}-subtle text-{{ $statusClass }} rounded-pill px-3">{{ $client->status }}</span>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-sm btn-light border text-primary" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('clients.destroy', $client->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this client?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-light border text-danger" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-3 text-secondary opacity-50"></i>
                        No clients found. Click "Add New Client" to create one.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4 d-flex justify-content-center">
        {{ $clients->links() }}
    </div>
</x-ui.card>
@endsection
