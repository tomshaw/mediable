<div class="flex items-center justify-around py-0 px-4 md:px-8 h-full">

  <div class="flex items-center justify-start w-full">
    <div class="w-28 cursor-pointer" wire:click="expandModal()">
      <x-icons.logo />
    </div>
  </div>

  @if($panel->isThumbMode())
  <div class="flex items-center justify-start w-full">
    <div class="p-0 m-0 w-full md:w-72">
      <input type="text" class="control-input" wire:model.live="searchTerm" spellcheck="false" placeholder="Search">
    </div>
  </div>
  @endif

  <div class="flex items-center justify-end w-full">
    <div class="flex items-center gap-2 whitespace-nowrap">
      <button class="focus:outline-none transform transition duration-500 hover:rotate-180" wire:click="closeModal()">
        <x-icons.exit />
      </button>
    </div>
  </div>

</div>