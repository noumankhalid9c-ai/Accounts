@extends('layouts.app')

@section('title', 'Travel Groups')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-header-title fs-3 fw-bold mb-1">Travel Groups</h1>
        <p class="page-header-subtitle text-muted mb-0">Manage umrah/hajj groups, flights, hotels, and vouchers.</p>
    </div>
    <div>
        <a href="{{ route('travel-groups.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2">
            <i class="bi bi-plus-lg"></i>
            <span>Create Group</span>
        </a>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <x-ui.card icon="bi-briefcase" title="All Travel Groups">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Group ID</th>
                            <th>Group Name</th>
                            <th>Service</th>
                            <th>Destination</th>
                            <th>Departure</th>
                            <th>Return</th>
                            <th>Pax</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($groups as $group)
                        <tr>
                            <td><span class="badge bg-secondary">{{ $group->group_id }}</span></td>
                            <td class="fw-semibold">{{ $group->group_name }}</td>
                            <td>{{ $group->service_type }}</td>
                            <td>{{ $group->destination }}</td>
                            <td>{{ $group->departure_date ? $group->departure_date->format('d M Y') : '-' }}</td>
                            <td>{{ $group->return_date ? $group->return_date->format('d M Y') : '-' }}</td>
                            <td>{{ $group->clients_count }}</td>
                            <td>
                                @if($group->status == 'Confirmed')
                                    <span class="badge bg-success">Confirmed</span>
                                @elseif($group->status == 'Draft')
                                    <span class="badge bg-secondary">Draft</span>
                                @else
                                    <span class="badge bg-primary">{{ $group->status }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('travel-groups.show', $group->id) }}" class="btn btn-sm btn-light border">
                                    <i class="bi bi-eye"></i> Manage
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">No travel groups found. Create one to get started.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    </div>
</div>
@endsection
