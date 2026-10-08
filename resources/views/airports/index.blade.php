@extends('layouts.app')

@section('title', 'Routes (Airports)')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Routes (Airports)</h1>
        <p class="text-muted mb-0">Manage airports and locations for routes.</p>
    </div>
    <div>
        <a href="{{ route('airports.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add Airport
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">IATA Code</th>
                        <th>Airport Name</th>
                        <th>City / Country</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($airports as $airport)
                    <tr>
                        <td class="ps-4 fw-bold text-primary">{{ $airport->iata_code }}</td>
                        <td class="fw-bold">{{ $airport->name }}</td>
                        <td>{{ $airport->city ?? 'N/A' }}{{ $airport->country ? ', ' . $airport->country : '' }}</td>
                        <td>
                            @if($airport->status)
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">Active</span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2 py-1">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('airports.edit', $airport->id) }}" class="btn btn-sm btn-light border text-primary" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @if($airport->status)
                            <form action="{{ route('airports.destroy', $airport->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light border text-danger" title="Deactivate" onclick="return confirm('Are you sure you want to deactivate this airport?')">
                                    <i class="bi bi-slash-circle"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No airports found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
