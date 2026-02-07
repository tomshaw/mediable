<?php

use Livewire\Attributes\On;
use Livewire\Component;
use TomShaw\Mediable\Traits\WithFileSize;

new class extends Component
{
    use WithFileSize;

    public array $fileData = [];

    public array $uploadErrors = [];

    public int $totalSize = 0;

    public string $formattedTotalSize = '0 B';

    public array $mimeBreakdown = [];

    public function mount(array $data = []): void
    {
        $this->hydrateData($data);
    }

    #[On('uploads-list:data')]
    public function receiveData(array $files, int $totalSize, string $formattedTotalSize, array $mimeBreakdown, array $uploadErrors): void
    {
        $this->fileData = $files;
        $this->totalSize = $totalSize;
        $this->formattedTotalSize = $formattedTotalSize;
        $this->mimeBreakdown = $mimeBreakdown;
        $this->uploadErrors = $uploadErrors;
    }

    #[On('uploads-list:reset')]
    public function resetData(): void
    {
        $this->fileData = [];
        $this->uploadErrors = [];
        $this->totalSize = 0;
        $this->formattedTotalSize = '0 B';
        $this->mimeBreakdown = [];
    }

    public function removeFile(int $index): void
    {
        $this->dispatch('uploads-list:remove-file', index: $index);
    }

    public function submitFiles(): void
    {
        $this->dispatch('uploads-list:submit-files');
    }

    public function clearFiles(): void
    {
        $this->dispatch('uploads-list:clear-files');
    }

    protected function hydrateData(array $data): void
    {
        $this->fileData = $data['files'] ?? [];
        $this->totalSize = $data['totalSize'] ?? 0;
        $this->formattedTotalSize = $data['formattedTotalSize'] ?? '0 B';
        $this->mimeBreakdown = $data['mimeBreakdown'] ?? [];
        $this->uploadErrors = $data['uploadErrors'] ?? [];
    }
}; ?>

<div class="flex flex-col items-center justify-center h-auto w-full max-w-screen-2xl p-4 md:p-6 lg:p-8 m-0" x-data="initMediableUploadsList()">
    <div class="flex flex-col lg:flex-row w-full h-full gap-6 lg:items-start">

        {{-- LEFT COLUMN: File Table --}}
        <div class="w-full lg:w-2/3 overflow-auto max-h-[80vh]">
            <div class="border border-gray-300 rounded-lg shadow-sm">
                <table class="border-collapse table-auto w-full text-sm">
                    <thead class="bg-gray-200 sticky top-0 z-10">
                        <tr>
                            <th class="px-4 py-2.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider w-12">#</th>
                            <th class="px-4 py-2.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Name</th>
                            <th class="px-4 py-2.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider hidden md:table-cell">Type</th>
                            <th class="px-4 py-2.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Size</th>
                            <th class="px-4 py-2.5 text-center text-xs font-bold text-gray-600 uppercase tracking-wider w-24"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-gray-100 divide-y divide-gray-200">
                        @foreach($fileData as $file)
                        <tr class="hover:bg-gray-200 transition-colors duration-150">
                            <td class="px-4 py-2 text-gray-500 text-xs">{{ $file['index'] + 1 }}</td>
                            <td class="px-4 py-2 text-gray-700 truncate max-w-xs" title="{{ $file['name'] }}">{{ Str::limit($file['name'], 40, '...') }}</td>
                            <td class="px-4 py-2 hidden md:table-cell">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-600 uppercase">{{ $file['extension'] }}</span>
                            </td>
                            <td class="px-4 py-2 text-gray-500 text-xs whitespace-nowrap">{{ $file['formattedSize'] }}</td>
                            <td class="px-4 py-2 text-center">
                                <button wire:click="removeFile({{ $file['index'] }})" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-neutral-900 py-1 px-3 text-xs font-normal text-white cursor-pointer">
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
            <div class="rounded-lg border border-gray-300 bg-gray-100 p-4 shadow-sm">
                <h3 class="text-xs font-bold text-gray-600 uppercase tracking-wider mb-3">Upload Summary</h3>
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total Files</span>
                        <span class="text-sm font-semibold text-gray-800">{{ count($fileData) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total Size</span>
                        <span class="text-sm font-semibold text-gray-800">{{ $formattedTotalSize }}</span>
                    </div>
                    @if(count($mimeBreakdown))
                    <div class="border-t border-gray-300 pt-2 mt-2">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">By Type</span>
                        <div class="mt-1.5 space-y-1">
                            @foreach($mimeBreakdown as $category => $data)
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500">{{ $category }}</span>
                                <span class="text-xs text-gray-700">{{ $data['count'] }} {{ $data['count'] === 1 ? 'file' : 'files' }} &middot; {{ $data['formattedSize'] }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Server Limits --}}
            <div class="rounded-lg border border-gray-300 bg-gray-100 p-4 shadow-sm">
                <h3 class="text-xs font-bold text-gray-600 uppercase tracking-wider mb-3">Server Limits</h3>
                <div class="space-y-3">
                    {{-- File Count Limit --}}
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-xs text-gray-500">Files</span>
                            <span class="text-xs text-gray-700">{{ count($fileData) }} / <span x-text="maxFileUploads"></span></span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                            <div class="h-1.5 rounded-full transition-all duration-300"
                                x-bind:class="({{ count($fileData) }} / maxFileUploads) > 0.8 ? 'bg-red-400' : 'bg-gray-500'"
                                x-bind:style="'width: ' + Math.min(({{ count($fileData) }} / maxFileUploads) * 100, 100) + '%'"></div>
                        </div>
                    </div>
                    {{-- Upload Size Limit --}}
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-xs text-gray-500">Upload Size</span>
                            <span class="text-xs text-gray-700">{{ $formattedTotalSize }} / <span x-text="Mediable.formatBytes(maxUploadSize)"></span></span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                            <div class="h-1.5 rounded-full transition-all duration-300"
                                x-bind:class="({{ $totalSize }} / maxUploadSize) > 0.8 ? 'bg-red-400' : 'bg-gray-500'"
                                x-bind:style="'width: ' + Math.min(({{ $totalSize }} / maxUploadSize) * 100, 100) + '%'"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-col gap-2">
                <button type="button" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-neutral-900 w-full py-2.5 px-4 font-medium text-xs tracking-wider text-gray-50 cursor-pointer" x-on:click="submitting = true; $wire.submitFiles()" x-bind:disabled="submitting">
                    <span class="absolute h-0 w-0 rounded-full bg-blue-400 transition-all duration-300 group-hover:h-full group-hover:w-full"></span>
                    <span class="spinner relative" x-show="submitting" x-cloak></span>
                    <span class="relative" x-show="!submitting">Submit</span>
                </button>
                <button type="button" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-neutral-900 w-full py-2.5 px-4 font-medium text-xs tracking-wider text-gray-50 cursor-pointer" wire:click="clearFiles">
                    <span class="absolute h-0 w-0 rounded-full bg-red-500 transition-all duration-300 group-hover:h-56 group-hover:w-full"></span>
                    <span class="relative">Reset</span>
                </button>
            </div>

        </div>

    </div>
</div>

@script
<script>
    Alpine.data('initMediableUploadsList', () => ({
        submitting: false,
        maxFileUploads: 0,
        maxUploadFileSize: 0,
        maxUploadSize: 0,

        async init() {
            const limits = await this.$wire.$parent.getServerLimits();
            this.maxUploadSize = limits.maxUploadSize;
            this.maxFileUploads = limits.maxFileUploads;
            this.maxUploadFileSize = limits.maxUploadFileSize;
        }
    }))
</script>
@endscript
