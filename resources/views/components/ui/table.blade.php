<div class="table-responsive">
    <table {{ $attributes->merge(['class' => 'table table-hover align-middle mb-0']) }}>
        <thead class="table-light">
            <tr>
                {{ $head }}
            </tr>
        </thead>
        <tbody>
            {{ $slot }}
        </tbody>
    </table>
</div>
