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

            </div>
        </div>
    </div>

    <div class="bg-gray-200 h-10 min-h-10 max-h-10 xl:h-11 xl:min-h-11 xl:max-h-11 2xl:h-12 2xl:min-h-12 2xl:max-h-12 w-full">
        <div class="flex items-center justify-start px-4 h-full w-full gap-x-2"></div>
    </div>
</div>