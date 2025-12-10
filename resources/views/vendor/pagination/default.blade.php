@if ($paginator->hasPages())
    <div class="pagination-wrapper">
        <div class="pagination-info">
            <span class="text-muted">
                Showing <strong>{{ $paginator->firstItem() }}</strong> to <strong>{{ $paginator->lastItem() }}</strong> of <strong>{{ $paginator->total() }}</strong> results
            </span>
        </div>
        
        <nav class="pagination-nav" aria-label="Page navigation">
            <ul class="pagination">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link" aria-disabled="true" aria-label="Previous">
                            Previous
                        </span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous">
                            Previous
                        </a>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <li class="page-item disabled">
                            <span class="page-link">{{ $element }}</span>
                        </li>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="page-item active">
                                    <span class="page-link">{{ $page }}</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                </li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next">
                            Next
                        </a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link" aria-disabled="true" aria-label="Next">
                            Next
                        </span>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
@endif

<style>
.pagination-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.25rem 1.5rem;
    border-top: 1px solid #e7eaf3;
    flex-wrap: wrap;
    gap: 1rem;
    background-color: #fff;
}

.pagination-info {
    font-size: 0.875rem;
    color: #697a8d;
    font-weight: 400;
}

.pagination-info strong {
    color: #566a7f;
    font-weight: 600;
}

.pagination-nav {
    display: flex;
    align-items: center;
}

.pagination {
    display: flex;
    list-style: none;
    padding: 0;
    margin: 0;
    gap: 0.375rem;
    align-items: center;
}

.page-item {
    margin: 0;
}

.page-link {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: auto;
    height: 2.625rem;
    padding: 0.5rem 1rem;
    color: #697a8d;
    background-color: #fff;
    border: 1px solid #d9dee3;
    border-radius: 0.5rem;
    text-decoration: none;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    font-weight: 500;
    cursor: pointer;
    font-size: 0.9375rem;
    line-height: 1.5;
    white-space: nowrap;
}

.page-link:hover:not(.disabled):not(.active) {
    color: #696cff;
    background-color: #f5f5f9;
    border-color: #696cff;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(105, 108, 255, 0.15);
}

.page-item.active .page-link {
    color: #fff;
    background-color: #696cff;
    border-color: #696cff;
    box-shadow: 0 2px 6px rgba(105, 108, 255, 0.35);
    font-weight: 600;
}

.page-item.active .page-link:hover {
    background-color: #5f63e6;
    border-color: #5f63e6;
}

.page-item.disabled .page-link {
    color: #c7cdd4;
    background-color: #f5f5f9;
    border-color: #d9dee3;
    cursor: not-allowed;
    pointer-events: none;
    opacity: 0.6;
}


@media (max-width: 768px) {
    .pagination-wrapper {
        flex-direction: column;
        align-items: center;
        text-align: center;
        padding: 1rem;
    }
    
    .pagination-info {
        width: 100%;
        text-align: center;
        margin-bottom: 0.75rem;
    }
    
    .pagination-nav {
        width: 100%;
        justify-content: center;
    }
    
    .pagination {
        gap: 0.25rem;
    }
    
    .page-link {
        min-width: auto;
        height: 2.375rem;
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
    }
}
</style>

