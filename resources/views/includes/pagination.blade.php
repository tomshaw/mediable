<div>
  @if ($paginator->hasPages())
  <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">

    <div class="flex justify-between gap-2 flex-1 sm:hidden">
      <span>
        @if ($paginator->onFirstPage())
        <span class="relative inline-flex items-center h-8 px-3 text-xs font-medium text-zinc-300 dark:text-zinc-600 rounded-lg cursor-default select-none">
          {!! __('pagination.previous') !!}
        </span>
        @else
        <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" class="relative inline-flex items-center h-8 px-3 text-xs font-medium text-zinc-600 dark:text-zinc-300 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 hover:border-zinc-300 dark:hover:border-zinc-600 focus:outline-none transition-colors cursor-pointer">
          {!! __('pagination.previous') !!}
        </button>
        @endif
      </span>

      <span>
        @if ($paginator->hasMorePages())
        <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" class="relative inline-flex items-center h-8 px-3 text-xs font-medium text-zinc-600 dark:text-zinc-300 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 hover:border-zinc-300 dark:hover:border-zinc-600 focus:outline-none transition-colors cursor-pointer">
          {!! __('pagination.next') !!}
        </button>
        @else
        <span class="relative inline-flex items-center h-8 px-3 text-xs font-medium text-zinc-300 dark:text-zinc-600 rounded-lg cursor-default select-none">
          {!! __('pagination.next') !!}
        </span>
        @endif
      </span>
    </div>

    <div class="hidden gap-x-3 sm:flex-1 sm:flex sm:items-center sm:justify-between">

      <div>
        <span class="relative z-0 inline-flex items-center gap-1">
          <span>
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
            <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
              <span class="relative inline-flex items-center justify-center h-8 w-8 rounded-lg text-zinc-300 dark:text-zinc-600 cursor-default" aria-hidden="true">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
              </span>
            </span>
            @else
            <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" rel="prev" class="relative inline-flex items-center justify-center h-8 w-8 rounded-lg text-zinc-500 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-zinc-100 dark:hover:bg-zinc-800 focus:outline-none transition-colors cursor-pointer" aria-label="{{ __('pagination.previous') }}">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
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
            <span class="relative inline-flex items-center justify-center h-8 px-2 text-xs font-medium text-zinc-400 dark:text-zinc-500 cursor-default select-none">{{ $element }}</span>
          </span>
          @endif

          {{-- Array Of Links --}}
          @if (is_array($element))
          @foreach ($element as $page => $url)
          <span wire:key="paginator-{{ $paginator->getPageName() }}-page{{ $page }}">
            @if ($page == $paginator->currentPage())
            <span aria-current="page">
              <span class="relative inline-flex items-center justify-center h-8 min-w-8 px-2 rounded-lg text-xs font-semibold bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900 cursor-default select-none tabular-nums">{{ $page }}</span>
            </span>
            @else
            <button type="button" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" class="relative inline-flex items-center justify-center h-8 min-w-8 px-2 rounded-lg text-xs font-medium text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:text-zinc-100 dark:hover:bg-zinc-800 focus:outline-none transition-colors cursor-pointer tabular-nums" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
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
            <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" rel="next" class="relative inline-flex items-center justify-center h-8 w-8 rounded-lg text-zinc-500 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-zinc-100 dark:hover:bg-zinc-800 focus:outline-none transition-colors cursor-pointer" aria-label="{{ __('pagination.next') }}">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
              </svg>
            </button>
            @else
            <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
              <span class="relative inline-flex items-center justify-center h-8 w-8 rounded-lg text-zinc-300 dark:text-zinc-600 cursor-default" aria-hidden="true">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
              </span>
            </span>
            @endif
          </span>
        </span>
      </div>

      <div>
        <p class="hidden 2xl:block text-xs text-zinc-400 dark:text-zinc-500 leading-5 tabular-nums select-none">
          <span class="font-medium text-zinc-600 dark:text-zinc-300">{{ $paginator->firstItem() }}</span>
          <span>&ndash;</span>
          <span class="font-medium text-zinc-600 dark:text-zinc-300">{{ $paginator->lastItem() }}</span>
          <span>{!! __('of') !!}</span>
          <span class="font-medium text-zinc-600 dark:text-zinc-300">{{ $paginator->total() }}</span>
        </p>
      </div>

    </div>

  </nav>
  @endif
</div>
