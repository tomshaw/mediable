<div>
  @if ($paginator->hasPages())
  <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">

    <div class="flex justify-between flex-1 sm:hidden">
      <span>
        @if ($paginator->onFirstPage())
        <span class="relative inline-flex items-center px-4 py-1.5 text-sm font-medium text-gray-300 bg-gray-100 border border-gray-400 cursor-default leading-5 rounded-md select-none">
          {!! __('pagination.previous') !!}
        </span>
        @else
        <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" class="relative inline-flex items-center px-4 py-1.5 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-400 leading-5 rounded-md hover:bg-gray-200 focus:outline-none transition ease-in-out duration-150 cursor-pointer">
          {!! __('pagination.previous') !!}
        </button>
        @endif
      </span>

      <span>
        @if ($paginator->hasMorePages())
        <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" class="relative inline-flex items-center px-4 py-1.5 ml-3 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-400 leading-5 rounded-md hover:bg-gray-200 focus:outline-none transition ease-in-out duration-150 cursor-pointer">
          {!! __('pagination.next') !!}
        </button>
        @else
        <span class="relative inline-flex items-center px-4 py-1.5 ml-3 text-sm font-medium text-gray-300 bg-gray-100 border border-gray-400 cursor-default leading-5 rounded-md select-none">
          {!! __('pagination.next') !!}
        </span>
        @endif
      </span>
    </div>

    <div class="hidden gap-x-2 sm:flex-1 sm:flex sm:items-center sm:justify-between">

      <div>
        <span class="relative z-0 inline-flex rounded-md">
          <span>
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
            <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
              <span class="relative inline-flex items-center px-2 py-1.5 text-sm font-medium text-gray-300 bg-gray-100 border border-gray-400 cursor-default rounded-l-md leading-5" aria-hidden="true">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
              </span>
            </span>
            @else
            <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" rel="prev" class="relative inline-flex items-center px-2 py-1.5 text-sm font-medium text-gray-500 bg-gray-100 border border-gray-400 rounded-l-md leading-5 hover:bg-gray-200 hover:text-gray-700 focus:outline-none transition ease-in-out duration-150 cursor-pointer" aria-label="{{ __('pagination.previous') }}">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
              </svg>
            </button>
            @endif
          </span>

          {{-- Pagination Elements --}}
          @foreach ($elements as $element)
          {{-- "Three Dots" Separator --}}
          @if (is_string($element))
          <span aria-disabled="true">
            <span class="relative inline-flex items-center px-4 py-1.5 -ml-px text-sm font-medium text-gray-400 bg-gray-100 border border-gray-400 cursor-default leading-5 select-none">{{ $element }}</span>
          </span>
          @endif

          {{-- Array Of Links --}}
          @if (is_array($element))
          @foreach ($element as $page => $url)
          <span wire:key="paginator-{{ $paginator->getPageName() }}-page{{ $page }}">
            @if ($page == $paginator->currentPage())
            <span aria-current="page">
              <span class="relative inline-flex items-center px-4 py-1.5 -ml-px text-sm font-medium text-gray-50 bg-gray-400 border border-gray-400 cursor-default leading-5 select-none">{{ $page }}</span>
            </span>
            @else
            <button type="button" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" class="relative inline-flex items-center px-4 py-1.5 -ml-px text-sm font-medium text-gray-700 bg-gray-100 border border-gray-400 leading-5 hover:bg-gray-200 focus:outline-none transition ease-in-out duration-150 cursor-pointer" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
              {{ $page }}
            </button>
            @endif
          </span>
          @endforeach
          @endif
          @endforeach

          <span>
            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
            <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" rel="next" class="relative inline-flex items-center px-2 py-1.5 -ml-px text-sm font-medium text-gray-500 bg-gray-100 border border-gray-400 rounded-r-md leading-5 hover:bg-gray-200 hover:text-gray-700 focus:outline-none transition ease-in-out duration-150 cursor-pointer" aria-label="{{ __('pagination.next') }}">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
              </svg>
            </button>
            @else
            <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
              <span class="relative inline-flex items-center px-2 py-1.5 -ml-px text-sm font-medium text-gray-300 bg-gray-100 border border-gray-400 cursor-default rounded-r-md leading-5" aria-hidden="true">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
              </span>
            </span>
            @endif
          </span>
        </span>
      </div>

      <div>
        <p class="hidden 2xl:block text-sm text-gray-500 leading-5">
          <span class="font-medium text-gray-700">{{ $paginator->firstItem() }}</span>
          <span>&ndash;</span>
          <span class="font-medium text-gray-700">{{ $paginator->lastItem() }}</span>
          <span>{!! __('of') !!}</span>
          <span class="font-medium text-gray-700">{{ $paginator->total() }}</span>
        </p>
      </div>

    </div>

  </nav>
  @endif
</div>
