@if ($paginator->hasPages())
    <div class="pager-wrap">
        <p class="pager-meta">
            Showing {{ $paginator->firstItem() }}&ndash;{{ $paginator->lastItem() }} of {{ $paginator->total() }} records
        </p>

        <nav class="pager" aria-label="Pagination">
            @if ($paginator->onFirstPage())
                <span class="is-disabled" aria-disabled="true">PREV</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev">PREV</a>
            @endif

            @foreach ($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
                @if ($page === $paginator->currentPage())
                    <span class="is-current" aria-current="page">{{ str_pad($page, 2, '0', STR_PAD_LEFT) }}</span>
                @else
                    <a href="{{ $url }}">{{ str_pad($page, 2, '0', STR_PAD_LEFT) }}</a>
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next">NEXT</a>
            @else
                <span class="is-disabled" aria-disabled="true">NEXT</span>
            @endif
        </nav>
    </div>
@endif
