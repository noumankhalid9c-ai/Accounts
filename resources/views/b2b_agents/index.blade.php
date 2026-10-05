@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">B2B Agents List</h1>
            <p class="text-muted mb-0">Manage all your B2B agents and their profiles.</p>
        </div>
        <div>
            <a href="{{ route('b2b.dashboard') }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left"></i> Back to Dashboard
            </a>
            <a href="{{ route('b2b-agents.create') }}" class="btn btn-primary">
                <i class="bi bi-person-plus"></i> Add New Agent
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <h5 class="card-title mb-4">Agent Directory</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Agent ID</th>
                            <th>Company Name</th>
                            <th>Agent Name</th>
                            <th>Contact Person</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($agents as $agent)
                        <tr>
                            <td>#{{ $agent->id }}</td>
                            <td class="fw-bold">{{ $agent->company_name ?? 'N/A' }}</td>
                            <td>{{ $agent->agent_name ?? $agent->name }}</td>
                            <td>{{ $agent->contact_person ?? 'N/A' }}</td>
                            <td>{{ $agent->phone ?? $agent->contact }}</td>
                            <td>
                                @if($agent->status == 'Active')
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 rounded-pill">Active</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2 py-1 rounded-pill">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('b2b-agents.statement', $agent->id) }}" class="btn btn-sm btn-light border text-primary" data-bs-toggle="tooltip" title="View Statement">
                                    <i class="bi bi-journal-text"></i> Statement
                                </a>
                                <a href="{{ route('b2b-agents.edit', $agent->id) }}" class="btn btn-sm btn-light border text-secondary" data-bs-toggle="tooltip" title="Edit Agent">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('b2b-agents.destroy', $agent->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border text-danger" onclick="return confirm('Are you sure you want to delete this agent?');" data-bs-toggle="tooltip" title="Delete Agent">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No agents found. Click "Add New Agent" to create one.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            <div class="mt-4 d-flex justify-content-center">
                {{ $agents->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
