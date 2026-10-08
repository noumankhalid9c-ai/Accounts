@extends('layouts.app')

@section('title', 'Airlines')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Airlines Master</h1>
        <p class="text-muted mb-0">Manage airlines for ticketing.</p>
    </div>
    <div>
        <a href="{{ route('airlines.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add Airline
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
                        <th class="ps-4">Code</th>
                        <th>Airline Name</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($airlines as $airline)
                    <tr>
                        <td class="ps-4 fw-bold text-primary">{{ $airline->code }}</td>
                        <td class="fw-bold">{{ $airline->name }}</td>
                        <td>{{ $airline->contact ?? 'N/A' }}</td>
                        <td>
                            @if($airline->status)
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">Active</span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2 py-1">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('airlines.edit', $airline->id) }}" class="btn btn-sm btn-light border text-primary" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @if($airline->status)
                            <form action="{{ route('airlines.destroy', $airline->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light border text-danger" title="Deactivate" onclick="return confirm('Are you sure you want to deactivate this airline?')">
                                    <i class="bi bi-slash-circle"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No airlines found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
