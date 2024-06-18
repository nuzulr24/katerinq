<ul class="pagination pagination-outline">
    {{-- Previous Page Link --}}
    @if ($paginator->onFirstPage())
        <li class="page-item previous disabled"><span class="page-link"><i class="previous"></i></span></li>
    @else
        <li class="page-item"><a href="#" class="page-link" wire:click="previousPage"><i class="previous"></i></a></li>
    @endif

    {{-- Pagination Elements --}}
    @foreach ($elements as $element)
        {{-- "Three Dots" Separator --}}
        @if (is_string($element))
            <li class="page-item"><span class="page-link">{{ $element }}</span></li>
        @endif

        {{-- Array Of Links --}}
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                @else
                    <li class="page-item"><a href="#" class="page-link" wire:click="goToPage({{ $page }})">{{ $page }}</a></li>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Next Page Link --}}
    @if ($paginator->hasMorePages())
        <li class="page-item"><a href="#" class="page-link" wire:click="nextPage"><i class="next"></i></a></li>
    @else
        <li class="page-item next disabled"><span class="page-link"><i class="next"></i></span></li>
    @endif
</ul>
