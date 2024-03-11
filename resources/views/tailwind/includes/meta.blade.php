<div class="flex flex-col justify-between p-0 m-0 w-full h-full">

    <div class="bg-gray-200 h-10 min-h-10 max-h-10 xl:h-11 xl:min-h-11 xl:max-h-11 2xl:h-12 2xl:min-h-12 2xl:max-h-12 w-full">
        <div class="flex items-center justify-between px-4 h-full w-full">
            <div></div>
            <div></div>
        </div>
    </div>

    <div class="flex-grow border-b border-t border-[#ccc] scrollY h-auto p-2">
        <div class="flex items-start justify-center p-0 m-0 w-full h-full">
            <div class="flex flex-col items-start justify-start w-full p-2 gap-y-1.5">

                @if ($this->mimeTypeImage($this->attachment->file_type))
                <figure class="w-full mb-0">
                    <img src="{{ $this->attachment->file_url }}?id={{ $uniqueId }}" class="w-full object-cover" />
                    <figcaption class="text-xs text-white overflow-hidden w-full select-none mt-3 py-1.5 px-2 bg-[#555] hover:bg-[#444]">{{$this->attachment->title}}</figcaption>
                </figure>
                @endif

                @if ($this->mimeTypeVideo($this->attachment->file_type))
                <figure class="w-full mb-0">
                    <video src="{{ asset($this->attachment->file_url) }}" controls></video>
                    <figcaption class="text-xs text-white overflow-hidden w-full select-none mt-3 py-1.5 px-2 bg-[#555] hover:bg-[#444]">{{$this->attachment->title}}</figcaption>
                </figure>
                @endif

                @if ($this->mimeTypeAudio($this->attachment->file_type))
                <figure class="w-full mb-0">
                    <audio controls class="w-[223px] mb-2">
                        <source src="{{ asset($this->attachment->file_url) }}" type="{{ $this->attachment->file_type }}">
                    </audio>
                    <figcaption class="text-xs text-white overflow-hidden w-full select-none mt-3 py-1.5 px-2 bg-[#555] hover:bg-[#444]">{{$this->attachment->title}}</figcaption>
                </figure>
                @endif

                @if ($this->attachment->file_original_name)
                <div class="text-xs text-white overflow-hidden w-full select-none py-1.5 px-2 bg-[#555] hover:bg-[#444]" data-textcopy>
                    {{ $this->attachment->file_original_name }}
                </div>
                @endif

                @if ($this->attachment->file_size)
                <div class="text-xs text-white overflow-hidden w-full select-none py-1.5 px-2 bg-[#555] hover:bg-[#444]" data-textcopy>
                    {{ $this->formatBytes($this->attachment->file_size) }}
                </div>
                @endif

                @if ($this->attachment->file_type)
                <div class="text-xs text-white overflow-hidden w-full select-none py-1.5 px-2 bg-[#555] hover:bg-[#444]" data-textcopy>
                    {{ $this->formatMimeType($this->attachment->file_type) }}
                </div>
                @endif

                @if ($this->imageWidth && $this->imageHeight)
                <div class="text-xs text-white overflow-hidden w-full select-none py-1.5 px-2 bg-[#555] hover:bg-[#444]" data-textcopy>
                    {{ $this->imageWidth }}&times;{{ $this->imageHeight }}
                </div>
                @endif

                @if ($this->attachment->created_at)
                <div class="text-xs text-white overflow-hidden w-full select-none py-1.5 px-2 bg-[#555] hover:bg-[#444]" data-textcopy>
                    {{ $this->attachment->getCreatedAt() }}
                </div>
                @endif

                @if ($this->attachment->updated_at)
                <div class="text-xs text-white overflow-hidden w-full select-none py-1.5 px-2 bg-[#555] hover:bg-[#444]" data-textcopy>
                    {{ $this->attachment->getUpdatedAt() }}
                </div>
                @endif

                @if ($show->isShowEditor() && count($selected))
                <button wire:click="enableEditorMode()" class="group relative inline-flex h-7 items-center justify-center overflow-hidden w-full bg-[#555] px-4 text-xs font-normal text-white"><span>Editor</span>
                    <div class="w-0 translate-x-[100%] pl-0 opacity-0 transition-all duration-200 group-hover:w-5 group-hover:translate-x-0 group-hover:pl-1 group-hover:opacity-100"><svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5">
                            <path d="M8.14645 3.14645C8.34171 2.95118 8.65829 2.95118 8.85355 3.14645L12.8536 7.14645C13.0488 7.34171 13.0488 7.65829 12.8536 7.85355L8.85355 11.8536C8.65829 12.0488 8.34171 12.0488 8.14645 11.8536C7.95118 11.6583 7.95118 11.3417 8.14645 11.1464L11.2929 8H2.5C2.22386 8 2 7.77614 2 7.5C2 7.22386 2.22386 7 2.5 7H11.2929L8.14645 3.85355C7.95118 3.65829 7.95118 3.34171 8.14645 3.14645Z" fill="currentColor" fill-rule="evenodd" clip-rule="evenodd"></path>
                        </svg></div>
                </button>
                @endif

                @if ($show->isShowPreview() && count($selected))
                <button wire:click="enablePreviewMode()" class="group relative inline-flex h-7 items-center justify-center overflow-hidden w-full bg-[#555] px-4 text-xs font-normal text-white"><span>Preview</span>
                    <div class="w-0 translate-x-[100%] pl-0 opacity-0 transition-all duration-200 group-hover:w-5 group-hover:translate-x-0 group-hover:pl-1 group-hover:opacity-100"><svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5">
                            <path d="M8.14645 3.14645C8.34171 2.95118 8.65829 2.95118 8.85355 3.14645L12.8536 7.14645C13.0488 7.34171 13.0488 7.65829 12.8536 7.85355L8.85355 11.8536C8.65829 12.0488 8.34171 12.0488 8.14645 11.8536C7.95118 11.6583 7.95118 11.3417 8.14645 11.1464L11.2929 8H2.5C2.22386 8 2 7.77614 2 7.5C2 7.22386 2.22386 7 2.5 7H11.2929L8.14645 3.85355C7.95118 3.65829 7.95118 3.34171 8.14645 3.14645Z" fill="currentColor" fill-rule="evenodd" clip-rule="evenodd"></path>
                        </svg></div>
                </button>
                @endif

            </div>
        </div>
    </div>

    <div class="bg-gray-200 h-10 min-h-10 max-h-10 xl:h-11 xl:min-h-11 xl:max-h-11 2xl:h-12 2xl:min-h-12 2xl:max-h-12 w-full">
        <div class="flex items-center justify-start px-4 h-full w-full gap-x-2"></div>
    </div>
</div>