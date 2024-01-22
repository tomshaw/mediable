@if ($paginator->hasPages())
<div class="mr-4 p-0">

  <ul class="pagination m-0 p-0">

    @if ($paginator->onFirstPage())
    <li class="list-none inline-block">
      <button disabled><i class="previous">&larr;</i></button>
    </li>
    @else
    <li class="list-none inline-block">
      <button wire:click="previousPage" wire:loading.attr="disabled" rel="prev"><i class="previous">&larr;</i></button>
    </li>
    @endif

    @foreach ($elements as $element)
    @if (is_array($element))
    @foreach ($element as $page => $url)
    @if ($page == $paginator->currentPage())
    <li class="current list-none inline-block"><button wire:click="gotoPage({{$page}})">{{ $page }}</button></li>
    @else
    <li class="list-none inline-block"><button wire:click="gotoPage({{$page}})">{{ $page }}</button></li>
    @endif
    @endforeach
    @endif
    @endforeach

    @if ($paginator->hasMorePages())
    <li class="list-none inline-block">
      <button wire:click="nextPage" wire:loading.attr="disabled" rel="next"><i class="next">&rarr;</i></button>
    </li>
    @else
    <li class="list-none inline-block"><button disabled><i class="next">&rarr;</i></button></li>
    @endif

  </ul>

</div>
@endif