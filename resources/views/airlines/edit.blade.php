@extends('layouts.app')

@section('title', 'Edit Airline')

@section('content')
<div class="page-header mb-4">
    <h1 class="page-header-title fs-3 fw-bold mb-1">Edit Airline</h1>
    <p class="page-header-subtitle text-muted mb-0">Update information for {{ $airline->name }}.</p>
</div>

<div class="row">
    <div class="col-xl-8 col-lg-10">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <form action="{{ route('airlines.update', $airline->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Airline Name *</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $airline->name) }}" required>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">IATA Code *</label>
                            <input type="text" name="code" class="form-control text-uppercase" value="{{ old('code', $airline->code) }}" maxlength="5" required>
                        </div>
                        
                        <div class="col-md-12">
                            <label class="form-label">Contact Information</label>
                            <input type="text" name="contact" class="form-control" value="{{ old('contact', $airline->contact) }}" placeholder="Phone, Email, etc.">
                        </div>
                        
                        <div class="col-md-12 mt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="status" name="status" value="1" {{ old('status', $airline->status) ? 'checked' : '' }}>
                                <label class="form-check-label" for="status">Active (Available for ticketing)</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('airlines.index') }}" class="btn btn-light border">Cancel</a>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i>Update Airline</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
