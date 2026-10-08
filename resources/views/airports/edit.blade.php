@extends('layouts.app')

@section('title', 'Edit Route/Airport')

@section('content')
<div class="page-header mb-4">
    <h1 class="page-header-title fs-3 fw-bold mb-1">Edit Route/Airport</h1>
    <p class="page-header-subtitle text-muted mb-0">Update information for {{ $airport->iata_code }} - {{ $airport->name }}.</p>
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
                
                <form action="{{ route('airports.update', $airport->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">IATA Code *</label>
                            <input type="text" name="iata_code" class="form-control text-uppercase" value="{{ old('iata_code', $airport->iata_code) }}" maxlength="5" required>
                        </div>
                        
                        <div class="col-md-8">
                            <label class="form-label">Airport Name *</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $airport->name) }}" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">City</label>
                            <input type="text" name="city" class="form-control" value="{{ old('city', $airport->city) }}">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Country</label>
                            <input type="text" name="country" class="form-control" value="{{ old('country', $airport->country) }}">
                        </div>
                        
                        <div class="col-md-12 mt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="status" name="status" value="1" {{ old('status', $airport->status) ? 'checked' : '' }}>
                                <label class="form-check-label" for="status">Active (Available for routing)</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('airports.index') }}" class="btn btn-light border">Cancel</a>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i>Update Route</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
