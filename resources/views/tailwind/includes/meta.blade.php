<div class="flex flex-col justify-between p-0 m-0 w-full h-full">

    <div class="bg-gray-200 w-full h-[50px] min-h-[50px] max-h-[50px]">
        <div class="flex items-center justify-between px-4 h-full w-full">
            <div></div>
            <div></div>
        </div>
    </div>

    <div class="flex-grow border-b border-t border-[#ccc] h-full p-2">
        <div class="flex items-start justify-center p-0 m-0 w-full h-full overflow-hidden">
            <div class="flex flex-col items-start justify-start w-full gap-y-1.5 p-2">

                @if ($this->mimeTypeImage($this->model->fileType))
                <figure class="mb-0">
                    <img src="{{ $this->model->fileUrl }}?id={{ $uniqueId }}" class="object-cover" />
                    <figcaption class="text-xs text-white overflow-hidden w-full mt-2 py-1 px-2 bg-[#444]">{{$this->model->title}}</figcaption>
                </figure>
                @endif

                @if ($this->model->fileOriginalName)
                <div class="text-xs text-white overflow-hidden w-full py-1 px-2 bg-[#444]">
                    {{ $this->model->fileOriginalName }}
                </div>
                @endif

                @if ($this->model->fileName)
                <div class="text-xs text-white overflow-hidden w-full py-1 px-2 bg-[#444]">
                    {{ $this->formatBytes($this->model->fileSize) }}
                </div>
                @endif

                @if ($this->model->fileName)
                <div class="text-xs text-white overflow-hidden w-full py-1 px-2 bg-[#444]">
                    {{ $this->formatMimeType($this->model->fileType) }}
                </div>
                @endif

                @if ($this->model->fileName)
                <div class="text-xs text-white overflow-hidden w-full py-1 px-2 bg-[#444]">
                    {{ $this->imageWidth }}&times;{{ $this->imageHeight }}
                </div>
                @endif

                @if ($this->model->createdAt)
                <div class="text-xs text-white overflow-hidden w-full py-1 px-2 bg-[#444]">
                    {{ $this->model->formatDateTime($this->model->createdAt) }}
                </div>
                @endif

                @if ($this->model->updatedAt)
                <div class="text-xs text-white overflow-hidden w-full py-1 px-2 bg-[#444]">
                    {{ $this->model->formatDateTime($this->model->updatedAt) }}
                </div>
                @endif

            </div>
        </div>
    </div>

    <div class="bg-gray-200 w-full h-[50px] min-h-[50px] max-h-[50px]">
        <div class="flex items-center justify-start px-4 h-full w-full gap-x-2"></div>
    </div>
</div>