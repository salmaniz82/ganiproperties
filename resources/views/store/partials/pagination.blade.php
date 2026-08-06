@if ($paginator->hasPages())
    @php
        $currentCategory = $category ?? null;
        $query = request()->except('page');
        $pageUrl = function (int $page) use ($currentCategory, $query): string {
            $url = $currentCategory
                ? ($page > 1 ? route('category.page', [$currentCategory, $page]) : route('category.show', $currentCategory))
                : ($page > 1 ? route('shop.page', $page) : route('shop'));

            return $query ? $url.'?'.http_build_query($query) : $url;
        };
    @endphp
    <nav role="navigation" aria-label="Pagination Navigation" class="pagination">
        @if ($paginator->onFirstPage())
            <span aria-disabled="true">Previous</span>
        @else
            <a href="{{ $pageUrl($paginator->currentPage() - 1) }}" rel="prev">Previous</a>
        @endif

        @for ($page = 1; $page <= $paginator->lastPage(); $page++)
            @if ($page === $paginator->currentPage())
                <span aria-current="page">{{ $page }}</span>
            @else
                <a href="{{ $pageUrl($page) }}">{{ $page }}</a>
            @endif
        @endfor

        @if ($paginator->hasMorePages())
            <a href="{{ $pageUrl($paginator->currentPage() + 1) }}" rel="next">Next</a>
        @else
            <span aria-disabled="true">Next</span>
        @endif
    </nav>
@endif
