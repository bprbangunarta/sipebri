@if ($paginator->hasPages())

<ul class="pagination pagination-sm no-margin pull-right">

    {{-- Previous Page Link --}}
    @if ($paginator->onFirstPage())
        <li class="disable" aria-disabled="true" aria-label="@lang('pagination.previous')">
            <span aria-hidden="true">Previous</span>
        </li>
    @else
        <li class="disable" aria-disabled="true" aria-label="@lang('pagination.previous')">
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">Previous</a>
        </li>
    @endif

    {{-- Pagination Elements --}}
    @foreach ($elements as $element)
        {{-- "Three Dots" Separator --}}
        @if (is_string($element))
            <li class="disable" aria-disabled="true">
                <span href="#">{{ $element }}</span>
            </li>
        @endif

        {{-- Array Of Links --}}
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <li class="active" aria-current="page">
                        <span class="page-link">{{ $page }}</span>
                    </li>
                @else
                    <li>
                        <a href="{{ $url }}">{{ $page }}</a>
                    </li>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Next Page Link --}}
    @if ($paginator->hasMorePages())
        <li aria-disabled="true" aria-label="@lang('pagination.previous')">
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">Next</a>
        </li>
    @else
        <li class="disable" aria-disabled="true" aria-label="@lang('pagination.next')">
            <span aria-hidden="true">Next</span>
        </li>
    @endif
</ul>

@endif