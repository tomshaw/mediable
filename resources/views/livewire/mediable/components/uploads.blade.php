<?php

use Illuminate\Validation\ValidationException;
use Livewire\Attributes\{Computed, On};
use Livewire\{Component, WithFileUploads};
use TomShaw\Mediable\Eloquent\Eloquent;
use TomShaw\Mediable\Traits\{WithFileSize, WithMimeTypes};

new class extends Component
{
    use WithFileSize;
    use WithFileUploads;
    use WithMimeTypes;

    public array $files = [];

    public array $uploadErrors = [];

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
        $this->dispatchFileCount();
    }

    public function clearFile(int $index): void
    {
        array_splice($this->files, $index, 1);
        $this->dispatchFileCount();
    }

    #[On('uploads:reset')]
    public function clearFiles(): void
    {
        $this->files = [];
        $this->uploadErrors = [];
        $this->dispatchFileCount();
    }

    #[On('uploads:submit')]
    public function createAttachments(): void
    {
        Eloquent::create($this->files);

        $count = count($this->files);
        $message = ($count > 1) ? "Created $count attachment(s) successfully!" : 'Created attachment successfully!';

        $this->dispatch('uploads:completed', message: $message);

        $this->files = [];
        $this->dispatchFileCount();
    }

    /** @return array<string, array{count: int, size: int}> */
    #[Computed]
    public function mimeBreakdown(): array
    {
        $breakdown = [];

        foreach ($this->files as $file) {
            $category = $this->resolveMimeCategory($file->getMimeType());
            if (! isset($breakdown[$category])) {
                $breakdown[$category] = ['count' => 0, 'size' => 0];
            }
            $breakdown[$category]['count']++;
            $breakdown[$category]['size'] += $file->getSize();
        }

        return $breakdown;
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

    protected function dispatchFileCount(): void
    {
        $this->dispatch('uploads:files-changed', count: count($this->files));
    }
}; ?>

<div @class(['flex items-center justify-center p-0 m-0 w-full', sizeof($files) ? 'h-auto' : 'h-full' ]) x-data="initMediableUploads()">

    @if (sizeof($files))
    <div class="flex flex-col items-center justify-center h-auto w-full max-w-screen-2xl p-4 md:p-6 lg:p-8 m-0">
        <div class="flex flex-col lg:flex-row w-full h-full gap-6">

            {{-- LEFT COLUMN: File Table --}}
            <div class="w-full lg:w-2/3">
                <div class="overflow-auto max-h-128 border border-gray-200 rounded-lg shadow-sm">
                    <table class="border-collapse table-auto w-full text-sm">
                        <thead class="bg-gray-100 sticky top-0 z-10">
                            <tr>
                                <th class="px-4 py-2.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-12">#</th>
                                <th class="px-4 py-2.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-4 py-2.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider hidden md:table-cell">Type</th>
                                <th class="px-4 py-2.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Size</th>
                                <th class="px-4 py-2.5 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-24"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($files as $index => $file)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-4 py-2 text-gray-400 text-xs">{{ $index + 1 }}</td>
                                <td class="px-4 py-2 text-gray-700 truncate max-w-xs" title="{{ $file->getClientOriginalName() }}">{{ Str::limit($file->getClientOriginalName(), 40, '...') }}</td>
                                <td class="px-4 py-2 hidden md:table-cell">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 uppercase">{{ $file->getClientOriginalExtension() }}</span>
                                </td>
                                <td class="px-4 py-2 text-gray-500 text-xs whitespace-nowrap">{{ $this->formatBytes($file->getSize()) }}</td>
                                <td class="px-4 py-2 text-center">
                                    <button wire:click="clearFile({{ $index }})" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-neutral-900 py-1 px-3 text-xs font-normal text-white cursor-pointer">
                                        <span class="absolute h-0 w-0 rounded-full bg-red-500 transition-all duration-300 group-hover:h-56 group-hover:w-32"></span>
                                        <span class="relative">Remove</span>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- RIGHT COLUMN: Summary Panel --}}
            <div class="w-full lg:w-1/3 lg:sticky lg:top-0 lg:self-start flex flex-col gap-4">

                {{-- Error Messages --}}
                <template x-if="error">
                    <div class="flex items-start gap-2 rounded-lg border border-red-200 bg-red-50 p-3">
                        <svg class="h-4 w-4 text-red-500 mt-0.5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                        </svg>
                        <p class="text-xs text-red-700" x-text="error"></p>
                    </div>
                </template>

                @if(count($uploadErrors))
                <div class="flex flex-col gap-1.5 rounded-lg border border-red-200 bg-red-50 p-3">
                    @foreach($uploadErrors as $uploadError)
                    <div class="flex items-start gap-2">
                        <svg class="h-4 w-4 text-red-500 mt-0.5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                        </svg>
                        <p class="text-xs text-red-700">{{ $uploadError }}</p>
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- Upload Summary --}}
                <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Upload Summary</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total Files</span>
                            <span class="text-sm font-semibold text-gray-800">{{ count($files) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total Size</span>
                            <span class="text-sm font-semibold text-gray-800">{{ $this->formatBytes($this->getTotalUploadSize()) }}</span>
                        </div>
                        @if(count($this->mimeBreakdown))
                        <div class="border-t border-gray-100 pt-2 mt-2">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">By Type</span>
                            <div class="mt-1.5 space-y-1">
                                @foreach($this->mimeBreakdown as $category => $data)
                                <div class="flex justify-between items-center">
                                    <span class="text-xs text-gray-500">{{ $category }}</span>
                                    <span class="text-xs text-gray-600">{{ $data['count'] }} {{ $data['count'] === 1 ? 'file' : 'files' }} &middot; {{ $this->formatBytes($data['size']) }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Server Limits --}}
                <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Server Limits</h3>
                    <div class="space-y-3">
                        {{-- File Count Limit --}}
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-xs text-gray-500">Files</span>
                                <span class="text-xs text-gray-600">{{ count($files) }} / <span x-text="maxFileUploads"></span></span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-1.5">
                                <div class="h-1.5 rounded-full transition-all duration-300"
                                    x-bind:class="({{ count($files) }} / maxFileUploads) > 0.8 ? 'bg-red-400' : 'bg-gray-500'"
                                    x-bind:style="'width: ' + Math.min(({{ count($files) }} / maxFileUploads) * 100, 100) + '%'"></div>
                            </div>
                        </div>
                        {{-- Upload Size Limit --}}
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-xs text-gray-500">Upload Size</span>
                                <span class="text-xs text-gray-600">{{ $this->formatBytes($this->getTotalUploadSize()) }} / <span x-text="Mediable.formatBytes(maxUploadSize)"></span></span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-1.5">
                                <div class="h-1.5 rounded-full transition-all duration-300"
                                    x-bind:class="({{ $this->getTotalUploadSize() }} / maxUploadSize) > 0.8 ? 'bg-red-400' : 'bg-gray-500'"
                                    x-bind:style="'width: ' + Math.min(({{ $this->getTotalUploadSize() }} / maxUploadSize) * 100, 100) + '%'"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Upload Progress --}}
                <template x-if="progress > 0 && progress < 100">
                    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Uploading</span>
                            <span class="text-xs text-gray-600" x-text="progress + '%'"></span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-1.5">
                            <div class="bg-gray-500 h-1.5 rounded-full transition-all duration-300" x-bind:style="'width: ' + progress + '%'"></div>
                        </div>
                    </div>
                </template>

                {{-- Action Buttons --}}
                <div class="flex flex-col gap-2">
                    <button type="button" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-neutral-900 w-full py-2.5 px-4 font-medium text-xs tracking-wider text-gray-50 cursor-pointer" wire:click="createAttachments" wire:loading.attr="disabled" wire:target="createAttachments">
                        <span class="absolute h-0 w-0 rounded-full bg-blue-400 transition-all duration-300 group-hover:h-full group-hover:w-full"></span>
                        <span class="spinner relative" wire:loading wire:target="createAttachments"></span>
                        <span class="relative" wire:loading.remove wire:target="createAttachments">Submit</span>
                    </button>
                    <button type="button" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-neutral-900 w-full py-2.5 px-4 font-medium text-xs tracking-wider text-gray-50 cursor-pointer" wire:click="clearFiles">
                        <span class="absolute h-0 w-0 rounded-full bg-red-500 transition-all duration-300 group-hover:h-56 group-hover:w-full"></span>
                        <span class="relative">Reset</span>
                    </button>
                </div>

            </div>

        </div>
    </div>
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
            const limits = await this.$wire.$parent.getServerLimits();
            this.maxUploadSize = limits.maxUploadSize;
            this.maxFileUploads = limits.maxFileUploads;
            this.maxUploadFileSize = limits.maxUploadFileSize;
            this.postMaxSize = limits.postMaxSize;
            this.memoryLimit = limits.memoryLimit;

            let fileInput = document.querySelector('#fileInput');

            fileInput.addEventListener('change', () => {
                let files = fileInput.files;
                if (files.length > 0) {
                    this.transferFiles(files);
                }
            });
        }
    }))
</script>
@endscript
