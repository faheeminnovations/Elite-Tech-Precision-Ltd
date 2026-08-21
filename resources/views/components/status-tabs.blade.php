@props(['tabs', 'param' => 'status', 'preserve' => []])

<div class="d-flex gap-2 mb-3 flex-wrap">
    @foreach($tabs as $key => $tab)
        @php
            $query = array_merge(
                request()->only($preserve),
                $key === 'all' ? [] : [$param => $key]
            );
            $isActive = ($key === 'all' && !request($param)) || request($param) === $key;
        @endphp
        <a href="{{ request()->url() . '?' . http_build_query($query) }}"
           class="pill-tab text-decoration-none {{ $isActive ? 'active' : '' }}">
            {{ $tab['label'] }} ({{ $tab['count'] }})
        </a>
    @endforeach
</div>

<style>
    .pill-tab:not(.active):hover { background: var(--paper); border-color: var(--line); }
</style>
