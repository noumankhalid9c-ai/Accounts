@props(['title' => ''])

<div class="card border-0 shadow-sm rounded-4 mb-4">
    @if($title)
        <div class="card-header bg-white border-bottom pb-3 pt-4 px-4 d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 fw-bold">{{ $title }}</h5>
            @isset($actions)
                <div>{{ $actions }}</div>
            @endisset
        </div>
    @endif
    <div class="card-body p-4">
        {{ $slot }}
    </div>
</div>
