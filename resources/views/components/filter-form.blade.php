@props([
    'action',
    'keys' => [],
    'total' => null,
    'label' => 'result',
    'exportRoute' => null,
    'id' => 'listing-filters',
])

<form method="GET" action="{{ $action }}" {{ $attributes->merge(['class' => 'filter-bar']) }} id="{{ $id }}">
    {{ $slot }}

    <button type="submit" class="btn-ef-primary">
        <i class="bi bi-funnel"></i> Apply
    </button>

    @if(request()->hasAny($keys))
        <a href="{{ $action }}" class="btn-ef-outline">
            <i class="bi bi-x-lg"></i> Clear
        </a>
    @endif

    @if($exportRoute)
        <a href="{{ route($exportRoute, request()->only($keys)) }}" class="btn-ef-outline ms-auto text-decoration-none">
            <i class="bi bi-download"></i> Export to Excel
        </a>
    @endif
</form>

@if(! is_null($total))
    <div class="filter-results-meta cell-sub mb-2">
        Showing {{ $total }} {{ Str::plural($label, $total) }}
        @if(request()->hasAny($keys))
            <span>· filters active</span>
        @endif
    </div>
@endif

<style>
    .filter-results-meta { font-size: 12.5px; }
</style>
