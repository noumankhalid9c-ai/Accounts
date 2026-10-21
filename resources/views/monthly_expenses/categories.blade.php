@extends('layouts.app')

@section('title', 'Expense Categories')

@section('content')
<div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="page-header-title fs-3 fw-bold mb-1">
            <i class="bi bi-tags-fill text-primary me-2"></i>Expense Categories
        </h1>
        <p class="page-header-subtitle text-muted mb-0">Manage office expense and utility classification groups.</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            <i class="bi bi-plus-circle me-1"></i> Add Category
        </button>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-1 text-dark">Category Directory</h5>
            <span class="text-muted small">{{ $categories->count() }} categories configured.</span>
        </div>
    </div>
    <div class="card-body p-0 pt-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase small fw-bold text-muted">Category Name</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Group / Type</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Description</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Associated Expenses</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Status</th>
                        <th class="pe-4 py-3 text-uppercase small fw-bold text-muted text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $cat)
                    <tr>
                        <td class="ps-4">
                            <strong class="text-dark">{{ $cat->name }}</strong>
                        </td>
                        <td>
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1">
                                {{ $cat->group ?? 'General' }}
                            </span>
                        </td>
                        <td class="text-muted small">
                            {{ $cat->description ?? '-' }}
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border px-2 py-1">
                                {{ $cat->monthly_expenses_count }} expense(s)
                            </span>
                        </td>
                        <td>
                            @if($cat->status === 'Active')
                                <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill px-3 py-1">Active</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-1">Inactive</span>
                            @endif
                        </td>
                        <td class="pe-4 text-end">
                            <button type="button" class="btn btn-sm btn-light border text-secondary" data-bs-toggle="modal" data-bs-target="#editCategoryModal{{ $cat->id }}" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </button>
                            @if(auth()->user() && (auth()->user()->isSuperAdmin() || auth()->user()->canDo('monthly_expenses.delete')))
                                @if($cat->monthly_expenses_count == 0)
                                    <form action="{{ route('monthly_expenses.categories.destroy', $cat->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this category?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border text-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-sm btn-light border text-muted opacity-50" disabled title="Cannot delete: associated with historical expenses">
                                        <i class="bi bi-lock-fill"></i>
                                    </button>
                                @endif
                            @endif
                        </td>
                    </tr>

                    <!-- Edit Category Modal -->
                    <div class="modal fade" id="editCategoryModal{{ $cat->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content border-0 shadow rounded-4">
                                <div class="modal-header border-0 pb-0">
                                    <h5 class="modal-title fw-bold">Edit Category</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('monthly_expenses.categories.update', $cat->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body p-4">
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold text-muted">Category Name *</label>
                                            <input type="text" name="name" class="form-control" value="{{ $cat->name }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold text-muted">Group *</label>
                                            <select name="group" class="form-select" required>
                                                @foreach($groups as $grp)
                                                    <option value="{{ $grp }}" {{ $cat->group == $grp ? 'selected' : '' }}>{{ $grp }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold text-muted">Status *</label>
                                            <select name="status" class="form-select" required>
                                                <option value="Active" {{ $cat->status == 'Active' ? 'selected' : '' }}>Active</option>
                                                <option value="Inactive" {{ $cat->status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold text-muted">Description</label>
                                            <textarea name="description" class="form-control" rows="2">{{ $cat->description }}</textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0 pt-0">
                                        <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary rounded-pill px-4">Update Category</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">No categories configured.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-plus-circle text-primary me-2"></i>New Expense Category
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('monthly_expenses.categories.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Category Name *</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Courier & Dispatch" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Classification Group *</label>
                        <select name="group" class="form-select" required>
                            @foreach($groups as $grp)
                                <option value="{{ $grp }}">{{ $grp }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Status</label>
                        <select name="status" class="form-select">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Description</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Optional notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
