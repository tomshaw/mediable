<div class="flex items-center justify-between bg-[#E6E6E6] p-0 m-0 px-4 w-full h-[50px] min-h-[50px] border-t border-[#ccc]">
  
  @if($showPagination && method_exists($data, 'links') && !$uploadMode)
  {!! $data->links("mediable::tailwind.includes.pagination") !!}
  @endif

  @if($showPerPage && !$uploadMode)
  <select class="control-select" wire:model.live="perPage">
    @foreach($perPageValues as $value)
    <option value="{{$value}}"> @if($value == 0) All @else {{ $value }} @endif</option>
    @endforeach
  </select>
  @endif
  
</div>