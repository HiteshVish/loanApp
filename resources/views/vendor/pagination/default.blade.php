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
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    padding: 1.25rem 1.5rem !important;
    border-top: 1px solid #e7eaf3 !important;
    flex-wrap: wrap !important;
    gap: 1rem !important;
    background-color: #fff !important;
}

.pagination-info {
    font-size: 0.875rem !important;
    color: #697a8d !important;
    font-weight: 400 !important;
}

.pagination-info strong {
    color: #566a7f !important;
    font-weight: 600 !important;
}

.pagination-nav {
    display: flex !important;
    align-items: center !important;
}

.pagination-wrapper .pagination {
    display: flex !important;
    list-style: none !important;
    padding: 0 !important;
    margin: 0 !important;
    gap: 0.375rem !important;
    align-items: center !important;
}

.pagination-wrapper .page-item {
    margin: 0 !important;
}

.pagination-wrapper .page-link {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    min-width: auto !important;
    height: 2.625rem !important;
    padding: 0.5rem 1rem !important;
    color: #697a8d !important;
    background-color: #fff !important;
    border: 1px solid #d9dee3 !important;
    border-radius: 0.5rem !important;
    text-decoration: none !important;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
    font-weight: 500 !important;
    cursor: pointer !important;
    font-size: 0.9375rem !important;
    line-height: 1.5 !important;
    white-space: nowrap !important;
}

.pagination-wrapper .page-link:hover:not(.disabled):not(.active) {
    color: #696cff !important;
    background-color: #f5f5f9 !important;
    border-color: #696cff !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 2px 4px rgba(105, 108, 255, 0.15) !important;
}

.pagination-wrapper .page-item.active .page-link {
    color: #fff !important;
    background-color: #696cff !important;
    border-color: #696cff !important;
    box-shadow: 0 2px 6px rgba(105, 108, 255, 0.35) !important;
    font-weight: 600 !important;
}

.pagination-wrapper .page-item.active .page-link:hover {
    background-color: #5f63e6 !important;
    border-color: #5f63e6 !important;
}

.pagination-wrapper .page-item.disabled .page-link {
    color: #c7cdd4 !important;
    background-color: #f5f5f9 !important;
    border-color: #d9dee3 !important;
    cursor: not-allowed !important;
    pointer-events: none !important;
    opacity: 0.6 !important;
}

/* Remove any icon styles */
.pagination-wrapper .page-link i,
.pagination-wrapper .page-link .bx,
.pagination-wrapper .page-link::before,
.pagination-wrapper .page-link::after {
    display: none !important;
    content: none !important;
}

/* Ensure text is visible */
.pagination-wrapper .page-link {
    text-indent: 0 !important;
}

/* Hide any chevron or arrow icons that might be added by theme */
.pagination-wrapper .page-item.prev .page-link::before,
.pagination-wrapper .page-item.previous .page-link::before,
.pagination-wrapper .page-item.next .page-link::after {
    display: none !important;
    content: none !important;
}


@media (max-width: 768px) {
    .pagination-wrapper {
        flex-direction: column !important;
        align-items: center !important;
        text-align: center !important;
        padding: 1rem !important;
    }
    
    .pagination-info {
        width: 100% !important;
        text-align: center !important;
        margin-bottom: 0.75rem !important;
    }
    
    .pagination-nav {
        width: 100% !important;
        justify-content: center !important;
    }
    
    .pagination-wrapper .pagination {
        gap: 0.25rem !important;
    }
    
    .pagination-wrapper .page-link {
        min-width: auto !important;
        height: 2.375rem !important;
        padding: 0.375rem 0.75rem !important;
        font-size: 0.875rem !important;
    }
}
</style>

