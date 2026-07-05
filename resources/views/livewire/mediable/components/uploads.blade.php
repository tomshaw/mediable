<?php

use Illuminate\Validation\ValidationException;
use Livewire\Attributes\{Computed, On};
use Livewire\{Component, WithFileUploads};
use TomShaw\Mediable\Eloquent\Eloquent;
use TomShaw\Mediable\Enums\BrowserEvents;
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

            return;
        }

        $this->uploadErrors = [];
    }

    public function removeFile(int $index): void
    {
        array_splice($this->files, $index, 1);
    }

    public function createAttachments(): void
    {
        Eloquent::create($this->files);

        $count = count($this->files);
        $message = ($count > 1) ? "Created $count attachment(s) successfully!" : 'Created attachment successfully!';

        $this->dispatch(BrowserEvents::UPLOADS_COMPLETED->value, message: $message);

        $this->files = [];
    }

    #[On(BrowserEvents::UPLOADS_RESET->value)]
    public function clearFiles(): void
    {
        $this->files = [];
        $this->uploadErrors = [];
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

    protected function resolveMimeCategory(string $mimeType): string
    {
        foreach ($this->strategies as $category => $types) {
            if (in_array($mimeType, $types)) {
                return ucfirst($category);
            }
        }

        return 'Other';
    }
}; ?>

<div @class(['flex items-center justify-center p-0 m-0 w-full', count($files) ? 'h-auto' : 'h-full']) x-data="initMediableUploads()">

    @if (count($files))
    <div class="flex flex-col lg:flex-row items-start justify-start gap-4 w-full p-4" x-data="initMediableUploadsList()">

        {{-- LEFT COLUMN: File Table --}}
        <div class="w-full lg:w-2/3 overflow-auto max-h-[80vh]">
            <div class="rounded-xl ring-1 ring-zinc-200 dark:ring-zinc-800 bg-white dark:bg-zinc-900 shadow-sm overflow-hidden">
                <table class="border-collapse table-auto w-full text-sm">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/50 sticky top-0 z-10">
                        <tr>
                            <th class="px-4 py-2.5 text-left text-[10px] font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider w-12">#</th>
                            <th class="px-4 py-2.5 text-left text-[10px] font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Name</th>
                            <th class="px-4 py-2.5 text-left text-[10px] font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider hidden md:table-cell">Type</th>
                            <th class="px-4 py-2.5 text-left text-[10px] font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Size</th>
                            <th class="px-4 py-2.5 text-center text-[10px] font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider w-24"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @foreach($this->fileMetadata as $file)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/40 transition-colors duration-150" wire:key="upload-file-{{ $file['index'] }}">
                            <td class="px-4 py-2 text-zinc-400 dark:text-zinc-500 text-xs tabular-nums">{{ $file['index'] + 1 }}</td>
                            <td class="px-4 py-2 text-zinc-700 dark:text-zinc-200 text-xs truncate max-w-xs" title="{{ $file['name'] }}">{{ Str::limit($file['name'], 40, '...') }}</td>
                            <td class="px-4 py-2 hidden md:table-cell">
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-md font-mono text-[10px] font-medium bg-zinc-100 text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400 uppercase tracking-wider">{{ $file['extension'] }}</span>
                            </td>
                            <td class="px-4 py-2 text-zinc-500 dark:text-zinc-400 text-xs whitespace-nowrap tabular-nums">{{ $file['formattedSize'] }}</td>
                            <td class="px-4 py-2 text-center">
                                <button wire:click="removeFile({{ $file['index'] }})" class="inline-flex items-center h-7 rounded-md px-2.5 text-xs font-medium text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/40 cursor-pointer transition-colors">
                                    Remove
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
            @if(count($uploadErrors))
            <div class="flex flex-col gap-1.5 rounded-xl ring-1 ring-red-200 dark:ring-red-900 bg-red-50 dark:bg-red-950 p-3">
                @foreach($uploadErrors as $uploadError)
                <div class="flex items-start gap-2">
                    <svg class="h-4 w-4 text-red-500 mt-0.5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                    </svg>
                    <p class="text-xs text-red-700 dark:text-red-300">{{ $uploadError }}</p>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Upload Summary --}}
            <div class="rounded-xl ring-1 ring-zinc-200 dark:ring-zinc-800 bg-white dark:bg-zinc-900 p-4 shadow-sm">
                <h3 class="text-[10px] font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-3">Upload summary</h3>
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Total files</span>
                        <span class="text-xs font-semibold text-zinc-800 dark:text-zinc-100 tabular-nums">{{ count($this->fileMetadata) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Total size</span>
                        <span class="text-xs font-semibold text-zinc-800 dark:text-zinc-100 tabular-nums">{{ $this->formatBytes($this->getTotalUploadSize()) }}</span>
                    </div>
                    @if(count($this->mimeBreakdown))
                    <div class="border-t border-zinc-100 dark:border-zinc-800 pt-2 mt-2">
                        <span class="text-[10px] font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">By type</span>
                        <div class="mt-1.5 space-y-1">
                            @foreach($this->mimeBreakdown as $category => $data)
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $category }}</span>
                                <span class="text-xs text-zinc-700 dark:text-zinc-200 tabular-nums">{{ $data['count'] }} {{ $data['count'] === 1 ? 'file' : 'files' }} &middot; {{ $data['formattedSize'] }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Server Limits --}}
            <div class="rounded-xl ring-1 ring-zinc-200 dark:ring-zinc-800 bg-white dark:bg-zinc-900 p-4 shadow-sm">
                <h3 class="text-[10px] font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-3">Server limits</h3>
                <div class="space-y-3">
                    {{-- File Count Limit --}}
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-xs text-zinc-500 dark:text-zinc-400">Files</span>
                            <span class="text-xs text-zinc-700 dark:text-zinc-200 tabular-nums">{{ count($this->fileMetadata) }} / <span x-text="maxFileUploads"></span></span>
                        </div>
                        <div class="w-full bg-zinc-100 dark:bg-zinc-800 rounded-full h-1.5">
                            <div class="h-1.5 rounded-full transition-all duration-300"
                                x-bind:class="({{ count($this->fileMetadata) }} / maxFileUploads) > 0.8 ? 'bg-red-500' : 'bg-indigo-500'"
                                x-bind:style="'width: ' + Math.min(({{ count($this->fileMetadata) }} / maxFileUploads) * 100, 100) + '%'"></div>
                        </div>
                    </div>
                    {{-- Upload Size Limit --}}
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-xs text-zinc-500 dark:text-zinc-400">Upload size</span>
                            <span class="text-xs text-zinc-700 dark:text-zinc-200 tabular-nums">{{ $this->formatBytes($this->getTotalUploadSize()) }} / <span x-text="Mediable.formatBytes(maxUploadSize)"></span></span>
                        </div>
                        <div class="w-full bg-zinc-100 dark:bg-zinc-800 rounded-full h-1.5">
                            <div class="h-1.5 rounded-full transition-all duration-300"
                                x-bind:class="({{ $this->getTotalUploadSize() }} / maxUploadSize) > 0.8 ? 'bg-red-500' : 'bg-indigo-500'"
                                x-bind:style="'width: ' + Math.min(({{ $this->getTotalUploadSize() }} / maxUploadSize) * 100, 100) + '%'"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-col gap-2">
                <button type="button" class="inline-flex items-center justify-center h-9 rounded-lg bg-indigo-600 px-4 text-xs font-medium text-white hover:bg-indigo-500 cursor-pointer transition-colors shadow-sm disabled:opacity-60 disabled:cursor-wait" x-on:click="submitting = true; $wire.createAttachments()" x-bind:disabled="submitting">
                    <span class="spinner" x-show="submitting" x-cloak></span>
                    <span x-show="!submitting">Upload {{ count($this->fileMetadata) }} {{ count($this->fileMetadata) === 1 ? 'file' : 'files' }}</span>
                </button>
                <button type="button" class="inline-flex items-center justify-center h-9 rounded-lg px-4 text-xs font-medium text-zinc-600 hover:bg-zinc-200/70 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100 cursor-pointer transition-colors" wire:click="clearFiles">
                    Reset
                </button>
            </div>

        </div>

    </div>
    @else
    <form>
        <div class="group h-auto w-md max-w-full py-8 px-8 text-center cursor-pointer border-2 border-dashed border-zinc-300 dark:border-zinc-700 bg-white/60 dark:bg-zinc-900/60 rounded-2xl transition-colors hover:border-indigo-400 dark:hover:border-indigo-500" @dragover.prevent @dragenter="dragEnter" @dragleave="dragLeave" @drop="drop" x-bind:class="{ '!border-indigo-500 bg-indigo-50/60 dark:bg-indigo-950/30': enter }" x-on:click.prevent="fileClick($event)">
            <div class="m-0 p-2 flex flex-col items-center text-center gap-3">
                <span class="flex items-center justify-center w-14 h-14 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-400 dark:text-zinc-500 transition-colors group-hover:text-indigo-500 dark:group-hover:text-indigo-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0 3 3m-3-3-3 3M6.75 19.5a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.233-2.33 3 3 0 0 1 3.758 3.848A3.752 3.752 0 0 1 18 19.5H6.75Z" />
                    </svg>
                </span>
                <div>
                    <h3 class="text-zinc-800 dark:text-zinc-100 font-semibold text-base mb-1">Drop files here or click to browse</h3>
                    <span class="block text-zinc-400 dark:text-zinc-500 text-xs leading-relaxed">Up to <span x-text="maxFileUploads"></span> files, <span x-text="Mediable.formatBytes(maxUploadSize)"></span> in total. Maximum single file size is <span x-text="Mediable.formatBytes(maxUploadFileSize)"></span>.</span>
                </div>
            </div>
            {{-- Upload Progress --}}
            <template x-if="progress > 0 && progress < 100">
                <div class="mt-4 px-2">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-[10px] font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Uploading</span>
                        <span class="text-xs text-zinc-600 dark:text-zinc-300 tabular-nums" x-text="progress + '%'"></span>
                    </div>
                    <div class="w-full bg-zinc-100 dark:bg-zinc-800 rounded-full h-1.5">
                        <div class="bg-indigo-500 h-1.5 rounded-full transition-all duration-300" x-bind:style="'width: ' + progress + '%'"></div>
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
    Alpine.data('initMediableUploadsList', () => ({
        submitting: false,
        maxFileUploads: 0,
        maxUploadFileSize: 0,
        maxUploadSize: 0,

        async init() {
            const limits = await this.$wire.getServerLimits();
            this.maxUploadSize = limits.maxUploadSize;
            this.maxFileUploads = limits.maxFileUploads;
            this.maxUploadFileSize = limits.maxUploadFileSize;
        }
    }));

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
                this.$dispatch('{{ BrowserEvents::ALERT->value }}', {
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

                    this.$dispatch('{{ BrowserEvents::ALERT->value }}', {
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
