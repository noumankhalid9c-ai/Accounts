@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Add B2B Agent</h1>
            <p class="text-muted mb-0">Register a new B2B agency or agent profile.</p>
        </div>
        <div>
            <a href="{{ route('b2b-agents.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Cancel
            </a>
        </div>
    </div>
    
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('b2b-agents.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Agent Name <span class="text-danger">*</span></label>
                        <input type="text" name="agent_name" class="form-control @error('agent_name') is-invalid @enderror" value="{{ old('agent_name') }}" required placeholder="E.g. Ahmed Khan">
                        @error('agent_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Company Name</label>
                        <input type="text" name="company_name" class="form-control @error('company_name') is-invalid @enderror" value="{{ old('company_name') }}" placeholder="E.g. ABC Travel & Tours">
                        @error('company_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Contact Person</label>
                        <input type="text" name="contact_person" class="form-control @error('contact_person') is-invalid @enderror" value="{{ old('contact_person') }}" placeholder="Main contact">
                        @error('contact_person')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="+92XXXXXXXXXX">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">WhatsApp</label>
                        <input type="text" name="whatsapp" class="form-control @error('whatsapp') is-invalid @enderror" value="{{ old('whatsapp') }}" placeholder="+92XXXXXXXXXX">
                        @error('whatsapp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="agent@example.com">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city') }}" placeholder="E.g. Lahore">
                        @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Country</label>
                        <input type="text" name="country" class="form-control @error('country') is-invalid @enderror" value="{{ old('country') }}" placeholder="E.g. Pakistan">
                        @error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2" placeholder="Full agency address">{{ old('address') }}</textarea>
                        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-8">
                        <label class="form-label">Notes / Remarks</label>
                        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="2" placeholder="Any special instructions">{{ old('notes') }}</textarea>
                        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <hr class="my-4">
                <div class="text-end">
                    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save"></i> Save Agent Profile</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
