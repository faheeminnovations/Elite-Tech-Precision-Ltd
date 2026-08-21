@if ($paginator->hasPages())
    <nav class="d-flex justify-content-between align-items-center mt-3">
        <div class="text-muted" style="font-size: 12.5px; color: var(--ink-soft);">
            Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} entries
        </div>
        
        <ul class="pagination mb-0" style="gap: 4px;">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link" style="border: 1px solid var(--line); border-radius: 8px; color: var(--ink-soft); background: var(--paper); padding: 6px 12px; font-size: 12.5px;">
                        <i class="bi bi-chevron-left"></i> Previous
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" style="border: 1px solid var(--line); border-radius: 8px; color: var(--navy); background: #fff; padding: 6px 12px; font-size: 12.5px; text-decoration: none;">
                        <i class="bi bi-chevron-left"></i> Previous
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled">
                        <span class="page-link" style="border: 1px solid var(--line); border-radius: 8px; color: var(--ink-soft); background: var(--paper); padding: 6px 12px; font-size: 12.5px;">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active">
                                <span class="page-link" style="border: 1px solid var(--orange); border-radius: 8px; color: #1a1300; background: var(--orange); padding: 6px 12px; font-size: 12.5px; font-weight: 600;">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $url }}" style="border: 1px solid var(--line); border-radius: 8px; color: var(--navy); background: #fff; padding: 6px 12px; font-size: 12.5px; text-decoration: none;">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" style="border: 1px solid var(--line); border-radius: 8px; color: var(--navy); background: #fff; padding: 6px 12px; font-size: 12.5px; text-decoration: none;">
                        Next <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link" style="border: 1px solid var(--line); border-radius: 8px; color: var(--ink-soft); background: var(--paper); padding: 6px 12px; font-size: 12.5px;">
                        Next <i class="bi bi-chevron-right"></i>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif