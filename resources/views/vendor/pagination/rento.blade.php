@if ($paginator->hasPages())
    <nav class="rento-pagination" role="navigation" aria-label="Pagination Navigation">
        <ul class="rento-pages">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="rento-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <span class="rento-link" aria-hidden="true">‹</span>
                </li>
            @else
                <li class="rento-item">
                    <a class="rento-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">‹</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="rento-item rento-ellipsis" aria-disabled="true"><span class="rento-link">…</span></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="rento-item active" aria-current="page"><span class="rento-link">{{ $page }}</span></li>
                        @else
                            <li class="rento-item"><a class="rento-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="rento-item">
                    <a class="rento-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">›</a>
                </li>
            @else
                <li class="rento-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <span class="rento-link" aria-hidden="true">›</span>
                </li>
            @endif
        </ul>
    </nav>
@endif