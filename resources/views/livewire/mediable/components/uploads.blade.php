<?php

use Illuminate\Validation\ValidationException;
use Livewire\Attributes\{Computed, On};
use Livewire\{Component, WithFileUploads};
use TomShaw\Mediable\Eloquent\Eloquent;
use TomShaw\Mediable\Traits\{ServerLimits, WithFileSize, WithMimeTypes};

new class extends Component
{
    use ServerLimits;
    use WithFileSize;
    use WithFileUploads;
    use WithMimeTypes;

    public array $files = [];

    public array $uploadErrors = [];

    public function getServerLimits(): array
    {
        return [
            'maxUploadSize' => $this->getMaxUploadSize(),
            'maxFileUploads' => $this->getMaxFileUploads(),
            'maxUploadFileSize' => $this->getMaxUploadFileSize(),
            'postMaxSize' => $this->getPostMaxSize(),
            'memoryLimit' => $this->getMemoryLimit(),
        ];
    }

    public function updatedFiles(): void
    {
        try {
            $this->validate(config('mediable.validation'));
        } catch (ValidationException $e) {
            $this->uploadErrors = collect($e->errors())->flatten()->all();
            $this->files = [];
            $this->dispatchFileCount();

            return;
        }

        $this->uploadErrors = [];
        $this->dispatchListData();
        $this->dispatchFileCount();
    }

    #[On('uploads-list:remove-file')]
    public function removeFile(int $index): void
    {
        array_splice($this->files, $index, 1);

        if (count($this->files)) {
            $this->dispatchListData();
        }

        $this->dispatchFileCount();
    }

    #[On('uploads-list:submit-files')]
    public function createAttachments(): void
    {
        Eloquent::create($this->files);

        $count = count($this->files);
        $message = ($count > 1) ? "Created $count attachment(s) successfully!" : 'Created attachment successfully!';

        $this->dispatch('uploads:completed', message: $message);

        $this->files = [];
        $this->dispatchFileCount();
    }

    #[On('uploads-list:clear-files')]
    #[On('uploads:reset')]
    public function clearFiles(): void
    {
        $this->files = [];
        $this->uploadErrors = [];
        $this->dispatchFileCount();
    }

    /** @return array<int, array{index: int, name: string, extension: string, size: int, formattedSize: string}> */
    #[Computed]
    public function fileMetadata(): array
    {
        return collect($this->files)->map(fn ($file, $index) => [
            'index' => $index,
            'name' => $file->getClientOriginalName(),
            'extension' => $file->getClientOriginalExtension(),
            'size' => $file->getSize(),
            'formattedSize' => $this->formatBytes($file->getSize()),
        ])->all();
    }

    /** @return array<string, array{count: int, size: int, formattedSize: string}> */
    #[Computed]
    public function mimeBreakdown(): array
    {
        $breakdown = [];

        foreach ($this->files as $file) {
            $category = $this->resolveMimeCategory($file->getMimeType());
            if (! isset($breakdown[$category])) {
                $breakdown[$category] = ['count' => 0, 'size' => 0, 'formattedSize' => ''];
            }
            $breakdown[$category]['count']++;
            $breakdown[$category]['size'] += $file->getSize();
        }

        foreach ($breakdown as &$data) {
            $data['formattedSize'] = $this->formatBytes($data['size']);
        }

        return $breakdown;
    }

    /** @return array{files: array, totalSize: int, formattedTotalSize: string, mimeBreakdown: array, uploadErrors: array} */
    #[Computed]
    public function listData(): array
    {
        return [
            'files' => $this->fileMetadata,
            'totalSize' => $this->getTotalUploadSize(),
            'formattedTotalSize' => $this->formatBytes($this->getTotalUploadSize()),
            'mimeBreakdown' => $this->mimeBreakdown,
            'uploadErrors' => $this->uploadErrors,
        ];
    }

    protected function resolveMimeCategory(string $mimeType): string
    {
        foreach ($this->strategies as $category => $types) {
            if (in_array($mimeType, $types)) {
                return ucfirst($category);
            }
        }

        return 'Other';
    }

    protected function dispatchListData(): void
    {
        $this->dispatch('uploads-list:data',
            files: $this->fileMetadata,
            totalSize: $this->getTotalUploadSize(),
            formattedTotalSize: $this->formatBytes($this->getTotalUploadSize()),
            mimeBreakdown: $this->mimeBreakdown,
            uploadErrors: $this->uploadErrors,
        );
    }

    protected function dispatchFileCount(): void
    {
        $this->dispatch('uploads:files-changed', count: count($this->files));
    }
}; ?>

<div @class(['flex items-center justify-center p-0 m-0 w-full', count($files) ? 'h-auto' : 'h-full']) x-data="initMediableUploads()">

    @if (count($files))
        <livewire:mediable::uploads-list :data="$this->listData" :key="'uploads-list'" />
    @else
    <form>
        <div class="h-auto max-w-125 py-6 px-7 text-center cursor-pointer border border-gray-400 border-dashed bg-gray-50 rounded-lg" @dragover.prevent @dragenter="dragEnter" @dragleave="dragLeave" @drop="drop" x-bind:class="{ 'border-gray-600': enter }" x-on:click.prevent="fileClick($event)">
            <div class="m-0 p-2 flex items-center text-left">
                <span class="text-gray-500 leading-none">
                    <img src="{{ asset("vendor/mediable/images/upload.png") }}" class="w-full h-full object-cover" alt="Upload files" />
                </span>
                <div class="ml-4">
                    <h3 class="text-gray-800 font-bold text-lg mb-1">Drop files here or click to upload.</h3>
                    <span class="text-gray-400 font-semibold text-sm">Upload up to <span x-text="maxFileUploads"></span> files not to exceed <span x-text="Mediable.formatBytes(maxUploadSize)"></span>. Maximum single file size is <span x-text="Mediable.formatBytes(maxUploadFileSize)"></span>.</span>
                </div>
            </div>
            {{-- Upload Progress --}}
            <template x-if="progress > 0 && progress < 100">
                <div class="mt-4 px-2">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Uploading</span>
                        <span class="text-xs text-gray-600" x-text="progress + '%'"></span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-1.5">
                        <div class="bg-gray-500 h-1.5 rounded-full transition-all duration-300" x-bind:style="'width: ' + progress + '%'"></div>
                    </div>
                </div>
            </template>
        </div>
        <div class="hidden">
            <input type="file" id="fileInput" wire:model="files" multiple>
        </div>
    </form>
    @endif

</div>

@script
<script>
    Alpine.data('initMediableUploads', () => ({
        error: null,
        enter: false,
        leave: false,
        progress: 0,
        dropingFile: false,

        maxFileUploads: 0,
        maxUploadFileSize: 0,
        maxUploadSize: 0,
        memoryLimit: 0,
        postMaxSize: 0,

        dragEnter(e) {
            this.enter = true;
        },

        dragLeave(e) {
            this.leave = false;
        },

        drop(e) {
            e.preventDefault();
            let files = e.dataTransfer.files;
            if (files.length > 0) {
                this.transferFiles(files)
            }
            this.dropingFile = false;
            this.enter = false;
        },

        fileClick(event) {
            document.getElementById('fileInput').click();
        },

        transferFiles(files) {
            this.error = null;

            this.maxUploadSizeCheck(files);
            this.maxUploadFileSizeCheck(files);

            if (this.error) {
                this.$dispatch('mediable.alert', {
                    event: { type: 'error', message: this.error }
                });
                return;
            }

            this.$wire.uploadMultiple('files', files,
                (uploadedFilename) => {
                    this.progress = 0;
                },
                (error) => {
                    this.error = error;
                    this.progress = 0;

                    this.$dispatch('mediable.alert', {
                        event: { type: 'error', message: error || 'An error occurred while uploading the file' }
                    });
                },
                (event) => {
                    if (event.detail.progress) {
                        this.progress = event.detail.progress;
                    }
                }
            );
        },

        maxUploadSizeCheck(files) {
            let totalSize = 0;
            for (let i = 0; i < files.length; i++) {
                totalSize += files[i].size;
            }

            if (totalSize > this.maxUploadSize) {
                this.error = 'Maximum upload size of ' + Mediable.formatBytes(this.maxUploadSize) + ' bytes has been exceeded.'
                return;
            }
        },

        maxUploadFileSizeCheck(files) {
            for (let i = 0; i < files.length; i++) {
                if (files[i].size > this.maxUploadFileSize) {
                    this.error = 'File upload ' + files[i].name + ' exceeds maximum upload size of ' + Mediable.formatBytes(this.maxUploadFileSize) + ' bytes.';
                    return;
                }
            }
        },

        async init() {
            const limits = await this.$wire.getServerLimits();
            this.maxUploadSize = limits.maxUploadSize;
            this.maxFileUploads = limits.maxFileUploads;
            this.maxUploadFileSize = limits.maxUploadFileSize;
            this.postMaxSize = limits.postMaxSize;
            this.memoryLimit = limits.memoryLimit;

            let fileInput = document.querySelector('#fileInput');

            if (fileInput) {
                fileInput.addEventListener('change', () => {
                    let files = fileInput.files;
                    if (files.length > 0) {
                        this.transferFiles(files);
                    }
                });
            }
        }
    }))
</script>
@endscript
